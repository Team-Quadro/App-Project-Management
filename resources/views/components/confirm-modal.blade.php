@props(['wireClick', 'title' => 'Konfirmasi', 'message' => 'Apakah Anda yakin?', 'confirmText' => 'Ya, Lanjutkan', 'cancelText' => 'Batal', 'confirmStyle' => 'danger'])

<div x-data="{ open: false }" @open-confirm-modal.window="if ($event.detail.id === '{{ $attributes->get('id') }}') open = true">
    <div x-show="open" 
         x-transition:enter="ease-out duration-200" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-150" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" 
         x-cloak>
        <div @click.outside="open = false" 
             x-show="open" 
             x-transition:enter="ease-out duration-200" 
             x-transition:enter-start="scale-95 opacity-0" 
             x-transition:enter-end="scale-100 opacity-100" 
             x-transition:leave="ease-in duration-150" 
             x-transition:leave-start="scale-100 opacity-100" 
             x-transition:leave-end="scale-95 opacity-0" 
             class="relative bg-canvas rounded-xl border border-hairline shadow-2xl max-w-sm w-full p-6 text-center">
            
            <div class="mb-4">
                @if($confirmStyle === 'danger')
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-3">
                        <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600" />
                    </div>
                @else
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-surface-2 mb-3">
                        <x-heroicon-o-question-mark-circle class="h-6 w-6 text-ink-subtle" />
                    </div>
                @endif
                <h3 class="text-[15px] font-semibold text-ink">{{ $title }}</h3>
                <p class="text-[13px] text-ink-subtle mt-1.5">{{ $message }}</p>
            </div>
            
            <div class="flex gap-2 w-full mt-6">
                <button @click="open = false" type="button" class="v-btn-secondary flex-1 text-[13px] justify-center">
                    {{ $cancelText }}
                </button>
                <button type="button" 
                        wire:click="{{ $wireClick }}" 
                        @click="open = false" 
                        class="flex-1 text-[13px] justify-center {{ $confirmStyle === 'danger' ? 'v-btn-danger' : 'v-btn-primary' }}">
                    {{ $confirmText }}
                </button>
            </div>
        </div>
    </div>
</div>
