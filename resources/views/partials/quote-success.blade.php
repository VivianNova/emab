{{-- Shown after a quote request is saved. Used by the quote page and the service detail page. --}}
@if (session('success'))
    <div id="success-modal" role="dialog" aria-modal="true" aria-labelledby="success-title"
         class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-6">
        <div class="flex w-full max-w-md flex-col items-center rounded-lg bg-white p-8 text-center shadow-xl">
            <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-brand-tint text-3xl text-brand-navy">
                <i class="bi bi-check-circle"></i>
            </div>
            <h3 id="success-title" class="mb-2 text-xl font-semibold text-brand-navy">Quote Request Sent</h3>
            <p class="mb-6 text-base text-slate-600">
                Thank you. We have received your details and will be in touch soon.
            </p>
            <button type="button" onclick="document.getElementById('success-modal').remove()"
                    class="w-full rounded-md bg-brand-blue py-3 text-base font-semibold text-white transition-colors hover:bg-brand-blue-hover">
                Done
            </button>
        </div>
    </div>
@endif
