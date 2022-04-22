<div>
    <form action="#" method="post" wire:submit.prevent="createTodo">
        @csrf

        <div class="w-full flex flex-col justify-center items-center gap-2">
            <div class="w-full">
                <div class="w-full px-6 py-2">
                    <input type="text" class="p-1 w-full" placeholder="Title" wire:model.defer="todoTitle">
                </div>
                <div class="w-full px-6">
                    <textarea class="p-2 resize-none w-full" placeholder="Body" wire:model.defer="todoBody"></textarea>
                </div>
                <div class="w-full px-6 py-4">
                    <x-button class="w-full">Create Todo</x-button>
                </div>
            </div>
        </div>
    </form>

    <div class="my-2 text-center">
        <x-auth-validation-errors class="mb-4" :errors="$errors"></x-auth-validation-errors>
    </div>
</div>
