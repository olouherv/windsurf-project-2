<?php

namespace App\Livewire\Rooms;

use App\Models\Room;
use Livewire\Component;

class RoomList extends Component
{
    public string $search = '';

    public function delete(int $id): void
    {
        Room::whereKey($id)->delete();
        session()->flash('success', __('Salle supprimée.'));
    }

    public function render()
    {
        $universityId = auth()->user()->university_id;

        $rooms = Room::where('university_id', $universityId)
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('building', 'like', '%' . $this->search . '%');
            })
            ->orderBy('code')
            ->get();

        return view('livewire.rooms.room-list', [
            'rooms' => $rooms,
        ]);
    }
}
