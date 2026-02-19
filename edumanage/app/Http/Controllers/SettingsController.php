<?php

namespace App\Http\Controllers;

use App\Models\MoodleConfig;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function updateGeneral(Request $request): RedirectResponse
    {
        $university = $request->user()->university;
        abort_if(!$university, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'website' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('universities/logos', 'public');
            $data['logo'] = $path;

            if (!empty($university->logo)) {
                Storage::disk('public')->delete($university->logo);
            }
        } else {
            unset($data['logo']);
        }

        $university->update($data);

        return back()->with('success', __('Paramètres généraux mis à jour.'));
    }

    public function updateMoodle(Request $request): RedirectResponse
    {
        $university = $request->user()->university;
        abort_if(!$university, 403);

        $data = $request->validate([
            'moodle_url' => ['required', 'url', 'max:255'],
            'moodle_token' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sync_students' => ['nullable', 'boolean'],
            'sync_teachers' => ['nullable', 'boolean'],
            'sync_courses' => ['nullable', 'boolean'],
            'sync_cohorts' => ['nullable', 'boolean'],
        ]);

        $config = MoodleConfig::updateOrCreate(
            ['university_id' => $university->id],
            [
                'moodle_url' => $data['moodle_url'],
                'moodle_token' => $data['moodle_token'],
                'is_active' => (bool) ($data['is_active'] ?? false),
                'sync_students' => (bool) ($data['sync_students'] ?? false),
                'sync_teachers' => (bool) ($data['sync_teachers'] ?? false),
                'sync_courses' => (bool) ($data['sync_courses'] ?? false),
                'sync_cohorts' => (bool) ($data['sync_cohorts'] ?? false),
            ]
        );

        return back()->with('success', __('Configuration Moodle enregistrée.'));
    }
}
