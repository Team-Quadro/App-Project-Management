@props(['action', 'title' => 'Confirm Delete', 'message' => 'This action cannot be undone. Are you sure?', 'id' =>
'confirm-delete-modal'])

<div x-data="{ open: false }" {{ $attributes }}>
    <button @click="open = true" type="button" class="v-btn-danger text-[13px] px-3 py-1.5">
        Delete
    </button>

    <template x-teleport="body">
        <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[200] flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-black/20" @click="open = false"></div>
            <div x-show="open" x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="scale-100 opacity-100"
                x-transition:leave-end="scale-95 opacity-0"
                class="relative bg-white rounded-[6px] border border-border shadow-lg max-w-sm w-full p-5">
                <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
                <p class="text-[13px] text-gray-500 mt-1.5">{{ $message }}</p>
                <div class="flex gap-2 mt-5">
                    <button @click="open = false" type="button" class="v-btn-secondary flex-1 text-[13px]">
                        Cancel
                    </button>
                    <form method="POST" action="{{ $action }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2 text-[13px] font-medium text-white bg-red-600 rounded-[4px] border border-red-600 hover:bg-red-700 transition-colors duration-150">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>