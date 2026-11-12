<div
    x-data="{ show: false, message: '', type: 'success' }"
    x-init="
        @if (session()->has('success'))
            message = '{{ addslashes(session('success')) }}';
            type = 'success';
            show = true;
            setTimeout(() => show = false, 4000);
        @elseif (session()->has('error'))
            message = '{{ addslashes(session('error')) }}';
            type = 'error';
            show = true;
            setTimeout(() => show = false, 5000);
        @elseif (session()->has('warning'))
            message = '{{ addslashes(session('warning')) }}';
            type = 'warning';
            show = true;
            setTimeout(() => show = false, 4500);
        @endif
    "
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    style="display: none;"
    class="fixed top-20 md:top-auto md:bottom-6 right-4 md:right-6 z-50 max-w-sm w-full shadow-modal rounded-2xl overflow-hidden pointer-events-auto"
>
    <div
        :class="{
            'bg-emerald-50 text-emerald-900 border-emerald-200': type === 'success',
            'bg-red-50 text-red-900 border-red-200': type === 'error',
            'bg-amber-50 text-amber-900 border-amber-200': type === 'warning'
        }"
        class="border p-4 flex items-start gap-3 rounded-2xl"
    >
        <div class="flex-shrink-0 pt-0.5">
            <template x-if="type === 'success'">
                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </template>
            <template x-if="type === 'error'">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </template>
            <template x-if="type === 'warning'">
                <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </template>
        </div>
        <div class="flex-1 text-sm font-medium" x-text="message"></div>
        <button type="button" @click="show = false" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>
