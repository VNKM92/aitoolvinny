<div class="space-y-4 my-8" x-data="{ active: null }">
    @foreach($faqs as $faq)
        <div class="backdrop-blur-md bg-slate-900/40 border border-slate-900 rounded-xl overflow-hidden">
            <button @click="active = active === {{ $faq->id }} ? null : {{ $faq->id }}" 
                class="w-full text-left px-5 py-4 font-bold text-white text-xs sm:text-sm hover:text-indigo-400 transition-colors flex justify-between items-center focus:outline-none">
                <span>{{ $faq->translate('question', $locale) }}</span>
                <svg class="h-4 w-4 text-slate-500 transform transition-transform duration-200" 
                    :class="{ 'rotate-180 text-indigo-400': active === {{ $faq->id }} }"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            
            <div class="transition-all max-h-0 overflow-hidden" 
                x-ref="faq_{{ $faq->id }}"
                :style="active === {{ $faq->id }} ? 'max-height: ' + $refs.faq_{{ $faq->id }}.scrollHeight + 'px' : ''">
                <div class="px-5 pb-5 text-slate-400 text-xs sm:text-sm leading-relaxed border-t border-slate-950/20 pt-2 whitespace-pre-wrap">
                    {{ $faq->translate('answer', $locale) }}
                </div>
            </div>
        </div>
    @endforeach
</div>
