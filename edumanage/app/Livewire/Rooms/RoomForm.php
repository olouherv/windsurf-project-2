<?php

namespace App\Livewire\Rooms;

use App\Models\Room;
use Illuminate\Validation\Rule;
use Livewire\Component;

class RoomForm extends Component
{
    public ?int $roomId = null;
    public bool $editMode = false;

    public string $name = '';
    public string $code = '';
    public ?string $building = '';
    public int $capacity = 30;
    public string $type = 'classroom';
    public ?string $equipment = '';
    public bool $is_available = true;

    protected function rules(): array
    {
        $roomId = $this->roomId;
        $universityId = auth()->user()->university_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('rooms', 'code')->where('university_id', $universityId)->ignore($roomId),
            ],
            'building' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'type' => ['required', Rule::in(['classroom', 'amphitheater', 'lab', 'computer_room', 'meeting_room', 'other'])],
            'equipment' => ['nullable', 'string'],
            'is_available' => ['boolean'],
        ];
    }

    public function mount(?int $roomId = null): void
    {
        if ($roomId) {
            $room = Room::whereKey($roomId)->first();
            if ($room) {
                $this->roomId = $room->id;
                $this->editMode = true;
                $this->name = $room->name;
                $this->code = $room->code;
                $this->building = $room->building;
                $this->capacity = (int) $room->capacity;
                $this->type = $room->type;
                $this->equipment = is_array($room->equipment) ? implode(', ', $room->equipment) : '';
                $this->is_available = (bool) $room->is_available;
            }
        }
    }

    public function save()
    {
        $data = $this->validate();

        $equipment = null;
        if (!empty($data['equipment'])) {
            $equipment = collect(explode(',', (string) $data['equipment']))
                ->map(fn($v) => trim($v))
                ->filter()
                ->values()
                ->all();
        }

        $payload = [
            'university_id' => auth()->user()->university_id,
            'name' => $data['name'],
            'code' => $data['code'],
            'building' => $data['building'],
            'capacity' => $data['capacity'],
            'type' => $data['type'],
            'equipment' => $equipment,
            'is_available' => $data['is_available'],
        ];

        if ($this->editMode && $this->roomId) {
            Room::whereKey($this->roomId)->update($payload);
            session()->flash('success', __('Salle mise à jour.'));
        } else {
            Room::create($payload);
            session()->flash('success', __('Salle créée.'));
        }

        return $this->redirect(route('rooms.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.rooms.room-form');
    }
}
