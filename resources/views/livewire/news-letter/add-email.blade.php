<div class="footer-widget-box list">
    <h4 class="footer-widget-title">النشرة الإخبارية</h4>
    <div class="footer-newsletter">
         <p>اشترك في نشرتنا للحصول على آخر التحديثات والأخبار</p>
        <div class="subscribe-form">
            <form wire:submit.prevent="addNewsLetterEmail">
                <div class="input-group">
                    <input
                        type="email"
                        class="form-control"
                        placeholder="بريدك الإلكتروني"
                        wire:model="email"
                    >
                    <button class="theme-btn mt-0" type="submit">
                         <i class="far fa-paper-plane"></i>
                    </button>
                </div> {{-- نهاية div.input-group --}}

                {{-- رسالة الخطأ للحقول --}}
                @error('email')
                <span class="text-danger d-block mt-2">{{ $message }}</span>
                @enderror

                {{-- رسالة النجاح بعد الإرسال --}}
                @if (session()->has('successEmail'))
                    <span class="text-success d-block mt-2">
                {{ session('successEmail') }}
            </span>
                @endif
            </form>
        </div>

    </div>
</div>
