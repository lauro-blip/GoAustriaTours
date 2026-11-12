@php($discount = \App\Models\Setting::get('modal_discount', '[CONFIRM: discount %]'))
<div x-data x-show="$store.modal.open" x-cloak
     @keydown.escape.window="$store.modal.hide()"
     @if(session('error_form') === 'header-modal') x-init="$store.modal.show()" @endif
     class="fixed inset-0 z-[90] flex items-end sm:items-center justify-center p-0 sm:p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="absolute inset-0 bg-black/60" @click="$store.modal.hide()"></div>
    <div class="relative w-full sm:max-w-2xl max-h-[92vh] overflow-y-auto bg-ivory rounded-t-2xl sm:rounded-2xl p-5 sm:p-8 shadow-2xl"
         x-trap.noscroll="$store.modal.open">
        <button type="button" class="absolute right-3 top-3 p-2" @click="$store.modal.hide()" aria-label="Close"><x-icon name="close" class="size-6" /></button>
        <h2 id="modal-title" class="text-3xl pr-8">Fill out this form, save up to {{ $discount }} on your trip</h2>
        <p class="mt-1 mb-5 text-sm text-muted">Free, no-obligation quote from a local Austria expert.</p>
        <template x-if="$store.modal.open">
            <div>
                <x-enquiry-form form-name="header-modal" uid="modal" :extended="true" button="Get My Free Quote" />
            </div>
        </template>
    </div>
</div>
