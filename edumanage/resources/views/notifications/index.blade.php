<x-layouts.admin>
    <x-slot name="header">Notifications</x-slot>

    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Notifications</h2>
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Tout marquer comme lu
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="divide-y divide-gray-200">
                @forelse($notifications as $n)
                    <div class="p-4 {{ $n->read_at ? '' : 'bg-indigo-50' }}">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-sm text-gray-900 font-medium">
                                    {{ $n->data['title'] ?? class_basename($n->type) }}
                                </div>
                                @if(!empty($n->data['message']))
                                    <div class="text-sm text-gray-600 mt-1">{{ $n->data['message'] }}</div>
                                @endif
                                <div class="text-xs text-gray-500 mt-2">
                                    {{ $n->created_at->format('d/m/Y H:i') }}
                                    @if(!$n->read_at)
                                        • <span class="text-indigo-600">Non lu</span>
                                    @endif
                                </div>
                            </div>
                            @if(!empty($n->data['url']))
                                <a href="{{ $n->data['url'] }}" class="text-indigo-600 text-sm hover:text-indigo-900">Ouvrir</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">Aucune notification</div>
                @endforelse
            </div>
        </div>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</x-layouts.admin>
