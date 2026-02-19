<?php

use App\Models\User;
use App\Models\University;
use App\Models\ModuleSetting;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;

new #[Layout('layouts.guest')] class extends Component
{
    public string $university_name = '';
    public string $university_email = '';
    public ?string $university_phone = '';
    public ?string $university_address = '';

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'university_name' => ['required', 'string', 'max:255'],
            'university_email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:universities,email'],
            'university_phone' => ['nullable', 'string', 'max:255'],
            'university_address' => ['nullable', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $universityCodeBase = strtoupper(preg_replace('/[^A-Z0-9]/', '', substr($validated['university_name'], 0, 10)));
        $universityCodeBase = $universityCodeBase ?: 'UNI';

        $code = $universityCodeBase;
        $i = 0;
        while (University::where('code', $code)->exists()) {
            $i++;
            $code = $universityCodeBase . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
        }

        $university = University::create([
            'name' => $validated['university_name'],
            'code' => $code,
            'email' => $validated['university_email'],
            'phone' => $validated['university_phone'] ?: null,
            'address' => $validated['university_address'] ?: null,
            'trial_ends_at' => now()->addDays(14),
            'is_active' => true,
        ]);

        foreach (ModuleSetting::MODULES as $key => $info) {
            if (($info['required'] ?? false) || ($info['default'] ?? false)) {
                ModuleSetting::updateOrCreate([
                    'university_id' => $university->id,
                    'module_key' => $key,
                ], [
                    'is_enabled' => true,
                ]);
            }
        }

        $userPayload = [
            'university_id' => $university->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'user_type' => 'admin',
            'is_active' => true,
        ];

        Role::firstOrCreate(['name' => 'admin']);

        event(new Registered($user = User::create($userPayload)));

        $user->assignRole('admin');

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <div class="p-4 border border-gray-200 rounded-lg mb-4">
            <h3 class="font-semibold text-gray-900">{{ __('Université / École') }}</h3>

            <div class="mt-4">
                <x-input-label for="university_name" :value="__('Raison sociale')" />
                <x-text-input wire:model="university_name" id="university_name" class="block mt-1 w-full" type="text" name="university_name" required autocomplete="organization" />
                <x-input-error :messages="$errors->get('university_name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="university_email" :value="__('Email université')" />
                <x-text-input wire:model="university_email" id="university_email" class="block mt-1 w-full" type="email" name="university_email" required autocomplete="email" />
                <x-input-error :messages="$errors->get('university_email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="university_phone" :value="__('Téléphone')" />
                <x-text-input wire:model="university_phone" id="university_phone" class="block mt-1 w-full" type="text" name="university_phone" autocomplete="tel" />
                <x-input-error :messages="$errors->get('university_phone')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="university_address" :value="__('Adresse')" />
                <textarea wire:model="university_address" id="university_address" name="university_address" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                <x-input-error :messages="$errors->get('university_address')" class="mt-2" />
            </div>
        </div>

        <div class="p-4 border border-gray-200 rounded-lg">
            <h3 class="font-semibold text-gray-900">{{ __('Administrateur') }}</h3>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
        </div>
    </form>
</div>
