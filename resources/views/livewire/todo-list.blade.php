<div class="p-4 border border-t-1 flex flex-col gap-2 relative">
    <div class="w-full text-right my-2">
        Show:
        <select name="filter" id="filter" wire:model="filter">
            <option value="*">All</option>
            <option value="0">Incompleted</option>
            <option value="1">Completed</option>
        </select>
    </div>

    <table class="text-center border border-1">
        <thead class="border border-1">
        <tr>
            <th class="border border-2 font-extrabold">ID</th>
            <th class="border border-2 font-extrabold">Title</th>
            <th class="border border-2 font-extrabold">Body</th>
            <th class="border border-2 font-extrabold">When</th>
            <th class="border border-2 font-extrabold">Actions</th>
        </tr>
        </thead>
        <tbody x-data="{}">
        @foreach($todos as $todo)
            <tr class="{{ $todo->isComplete() ? 'bg-green-100' : 'bg-red-100'}}">
                <td class="border border-1 w-1/12">{{ $todo->id }}</td>
                <td class="border border-1 w-3/12">{{ $todo->title }}</td>
                <td class="border border-1 w-5/12">{{ $todo->body }}</td>
                <td class="border border-1 w-1/12 text-xs">{{ $todo->created_at->diffForHumans() }}</td>
                <td class="border border-1 w-2/12 p-2">
                    {{-- Toggle completion --}}
                    <x-button
                        type="button"
                        x-on:click="confirmCallback(
                            '{{ $todo->isComplete() ? 'Todo is currently completed. Mark as incomplete?' : 'Todo is currently incomplete. Mark as completed?' }}',
                            $wire.toggleTodo,
                            '{{ $todo->id }}'
                        )"
                    >
                        @if (!$todo->isComplete())
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        @endif
                    </x-button>
                    {{-- /Toggle completion --}}

                    {{-- Delete --}}
                    <x-button type="button"
                              x-on:click="confirmCallback('You sure you wanna delete this?', $wire.deleteTodo, '{{ $todo->id }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </x-button>
                    {{-- /Delete --}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
