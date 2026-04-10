@section('title')
    {{ __('messages.dashboard') . ' - ' }}{{ __('messages.login.login') }}
@endsection

@php
    $company = strtolower(trim($_ENV['COMPANY_LUNCH']
        ?? $_SERVER['COMPANY_LUNCH']
        ?? env('COMPANY_LUNCH', '')));
    $isZentra = $company === 'zentra';
    $isRtl = app()->getLocale() === 'ar';
@endphp

@section('style')
    <style>
        /* ============================================================================
           Devlo CMS — Login (animated, RTL-ready, single-root safe)
           ========================================================================== */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap');

        html, body { height: 100% }
        body{
            margin:0; padding:0; overflow:hidden;
            font-family:"Poppins",system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif;
            color: var(--bs-body-color); background: var(--bs-body-bg);
        }
        /* ---------- Single root ---------- */
        #devlo-login-root{ position:relative; min-height:100vh; isolation:isolate }

        /* ---------- Background ---------- */
        .login-bg{ position:fixed; inset:0; z-index:0;
            background:
                radial-gradient(1000px 60% at 0% 0%, color-mix(in srgb, var(--bs-primary) 13%, transparent), transparent 60%),
                linear-gradient(145deg,
                color-mix(in srgb, var(--bs-primary) 12%, #0000) 0%,
                color-mix(in srgb, var(--bs-primary)  6%, #0000) 45%,
                color-mix(in srgb, var(--bs-primary) 12%, #0000) 100%);
        }
        .login-bg::after{ content:""; position:absolute; inset:0; pointer-events:none; opacity:.06;
            background:
                linear-gradient(90deg, currentColor 1px, transparent 1px) 0 0/28px 28px,
                linear-gradient( 0deg, currentColor 1px, transparent 1px) 0 0/28px 28px;
            color: var(--bs-body-color);
        }
        @keyframes floaty{ to{ transform: translateY(14px) translateX(-10px) scale(1.01) } }
        .brand-orb{
            position:absolute; width:520px; height:520px; right:-140px; top:-140px; border-radius:50%;
            background: radial-gradient(closest-side, color-mix(in srgb, var(--bs-primary) 38%, #fff) 0%, transparent 70%);
            filter: blur(22px) saturate(115%); opacity:.32; animation: floaty 8s ease-in-out infinite alternate;
        }
        [data-bs-theme="dark"] .login-bg{
            background:
                radial-gradient(900px 60% at 0% 30%, color-mix(in srgb, var(--bs-primary) 16%, transparent), transparent),
                linear-gradient(160deg, #0b0f1c 0%, color-mix(in srgb, #0b0f1c 80%, var(--bs-card-bg)) 60%);
        }
        [data-bs-theme="dark"] .brand-orb{ opacity:.26; filter: blur(26px) saturate(125%) }

        /* ---------- Language switcher ---------- */
        .language-switcher{
            position:fixed; top:18px; inset-inline-end:18px; z-index:3;
            display:inline-flex; gap:0; padding:4px; border-radius:999px;
            background: color-mix(in srgb, var(--bs-card-bg) 60%, transparent);
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 80%, transparent);
            backdrop-filter: blur(6px);
            box-shadow: 0 8px 20px rgba(0,0,0,.10);
        }
        .lang-btn{
            appearance:none; border:0; margin:0; padding:.45rem .85rem; font-size:.85rem; font-weight:800; letter-spacing:.02em;
            border-radius:999px; background:transparent; color:var(--bs-body-color); cursor:pointer;
            transition: background .2s, color .2s, transform .05s, box-shadow .2s;
        }
        .lang-btn:hover{ background: color-mix(in srgb, var(--bs-primary) 10%, transparent) }
        .lang-btn:active{ transform: translateY(1px) }
        .lang-btn.active, .lang-btn[aria-pressed="true"]{
            background: var(--bs-primary); color:#fff; box-shadow: 0 6px 14px color-mix(in srgb, var(--bs-primary) 45%, transparent);
        }

        /* ---------- Shell & card ---------- */
        .login-shell{ position:relative; z-index:1; min-height:100vh; display:grid; place-items:center; padding:clamp(16px,2vw,28px) }
        .login-card{
            width:min(96vw,980px); display:grid; grid-template-columns:1.15fr 1fr; border-radius:20px; overflow:hidden;
            background: linear-gradient(180deg, color-mix(in srgb, var(--bs-card-bg) 90%, transparent), var(--bs-card-bg));
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 70%, transparent);
            box-shadow: 0 14px 46px rgba(0,0,0,.14), inset 0 0 0 1px color-mix(in srgb, var(--bs-primary) 8%, transparent);
            backdrop-filter: blur(10px);
        }

        /* ---------- Left brand panel ---------- */
        .brand-panel{
            position:relative; padding:28px; display:grid; align-content:space-between; gap:18px;
            background:
                radial-gradient(120% 100% at 100% 0%, color-mix(in srgb, var(--bs-primary) 20%, transparent), transparent 65%),
                linear-gradient(180deg, color-mix(in srgb, var(--bs-primary) 16%, var(--bs-card-bg)), var(--bs-card-bg));
            border-inline-end: 1px solid color-mix(in srgb, var(--bs-primary) 24%, var(--bs-border-color));
        }
        .brand-head{ display:grid; gap:14px }
        .brand-row{ display:flex; align-items:center; justify-content:center; gap:12px }
        .brand-logo{ height:80px; display:none }
        .brand-logo-light{ display:inline-block }
        [data-bs-theme="dark"] .brand-logo-light{ display:none }
        [data-bs-theme="dark"] .brand-logo-dark{ display:inline-block }

        /* features */
        .brand-features{ display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin-top:6px; padding:0 }
        .brand-features li{
            list-style:none; display:flex; align-items:center; gap:10px; font-weight:600; font-size:1rem;
            color: color-mix(in srgb, var(--bs-body-color) 92%, transparent);
        }
        .brand-chip{
            inline-size:28px; block-size:28px; border-radius:9px; display:grid; place-items:center; font-size:16px; line-height:0;
            background: color-mix(in srgb, var(--bs-primary) 18%, transparent);
            border: 1px solid color-mix(in srgb, var(--bs-primary) 42%, transparent); color:#fff;
        }

        /* ---------- Right form panel ---------- */
        .form-panel{ padding:28px; display:grid; align-content:center; gap:18px }
        .form-head{ text-align:start; display:block; gap:6px }
        .form-title{ font-size:48px; margin-bottom:0 }
        .form-tag{ color:var(--bs-text-muted); font-size:20px }

        /* inputs */
        .form-stack{ display:grid; gap:12px }
        .input-wrap{ position:relative }
        .input-wrap .bi{
            position:absolute; inset-inline-start:12px; inset-block-start:50%; transform:translateY(-50%);
            color: color-mix(in srgb, var(--bs-text-muted) 85%, transparent); font-size:1rem; pointer-events:none;
        }
        .form-panel .form-control{
            background: color-mix(in srgb, var(--bs-card-bg) 94%, transparent);
            border:1px solid var(--bs-border-color); color:var(--bs-body-color);
            border-radius:12px; padding:.85rem 1rem; padding-inline-start:2.4rem; transition:border-color .15s, box-shadow .15s, background-color .15s;
            text-align:start;
        }
        .form-panel .form-control::placeholder{ color: color-mix(in srgb, var(--bs-text-muted) 88%, transparent) }
        .form-panel .form-control:focus{
            outline:0; border-color: color-mix(in srgb, var(--bs-primary) 60%, var(--bs-border-color));
            box-shadow:0 0 0 3px color-mix(in srgb, var(--bs-primary) 28%, transparent), 0 10px 30px rgba(0,0,0,.06);
            background: color-mix(in srgb, var(--bs-card-bg) 98%, transparent)
        }

        /* password toggle */
        .btn-eye{
            position:absolute; inset-inline-end:8px; inset-block:0; display:grid; place-items:center; width:38px; border:0;
            background:transparent; color:var(--bs-text-muted); cursor:pointer; border-radius:10px;
        }
        .btn-eye:hover{ background: color-mix(in srgb, var(--bs-primary) 10%, transparent); color:var(--bs-body-color) }

        /* actions */
        .form-actions{ display:flex; align-items:center; justify-content:space-between; gap:10px }
        .link-muted{ color:var(--bs-text-muted); text-decoration:none; font-weight:600 }
        .link-muted:hover{ color:var(--bs-body-color) }

        /* submit */
        .btn-login{
            width:100%; border:0; border-radius:12px; padding:1rem; font-weight:800; letter-spacing:.2px;
            background:var(--bs-primary); color:#fff; display:inline-flex; align-items:center; justify-content:center; gap:.55rem;
            transition: transform .06s, filter .18s, background-color .18s; position:relative; overflow:hidden;
        }
        .btn-login:hover{ background:var(--bs-primary-hover); filter:brightness(.98) }
        .btn-login:active{ transform:translateY(1px) }
        .btn-login::after{
            content:""; position:absolute; inset:0 -120%; transform: translateX(-120%);
            background: linear-gradient(120deg, transparent 0%, rgba(255,255,255,.18) 45%, rgba(255,255,255,.28) 50%, transparent 55%);
            filter: blur(.5px);
        }
        .btn-login:hover::after{ animation: sheen 1.2s ease }
        @keyframes sheen{ to{ transform: translateX(120%) } }

        /* helpers */
        .alert-danger{ border-radius:10px; border:1px solid color-mix(in srgb, #ef4444 40%, var(--bs-border-color)) }
        .form-foot{ text-align:center; color:var(--bs-text-muted); font-size:.9rem; margin-top:4px }

        /* ---------- RTL ---------- */
        [dir="rtl"] .form-panel .form-control{ text-align:start }
        [dir="rtl"] .form-actions{ flex-direction: row-reverse }
        [dir="rtl"] .brand-features li{ direction: rtl }
        [dir="rtl"] .language-switcher{ inset-inline-end:18px }

        /* ---------- Motion primitives ---------- */
        @media (prefers-reduced-motion: no-preference){
            .fx-fadeUp   {opacity:0; transform: translateY(12px)}
            .fx-fadeIn   {opacity:0}
            .fx-scaleIn  {opacity:0; transform: scale(.98)}
            .fx-slideInX {opacity:0; transform: translateX(var(--slide-x, 12px))}

            /* دعم “نفس العنصر” + “الأب/الإبن” */
            .is-in .fx-fadeUp , .fx-fadeUp.is-in  {animation: fadeUp  .5s ease forwards var(--d,0s)}
            .is-in .fx-fadeIn , .fx-fadeIn.is-in  {animation: fadeIn  .5s ease forwards var(--d,0s)}
            .is-in .fx-scaleIn, .fx-scaleIn.is-in {animation: scaleIn .5s ease forwards var(--d,0s)}
            .is-in .fx-slideInX, .fx-slideInX.is-in{animation: slideInX .55s cubic-bezier(.2,.7,.2,1) forwards var(--d,0s)}

            @keyframes fadeUp  {to{opacity:1; transform:none}}
            @keyframes fadeIn  {to{opacity:1}}
            @keyframes scaleIn {to{opacity:1; transform:none}}
            @keyframes slideInX{to{opacity:1; transform:none}}

            @keyframes logoFloat {0%{transform:translateY(0)}50%{transform:translateY(-6px)}100%{transform:translateY(0)}}
            .fx-logoFloat{ animation: logoFloat 6s ease-in-out infinite }

            @keyframes cardBreath {0%{transform:translateZ(0)}50%{transform:translateZ(0) scale(1.005)}100%{transform:translateZ(0)}}
            .fx-card-breath{ animation: cardBreath 10s ease-in-out infinite }
        }

        /* Parallax helpers / stagger */
        .fx-parallax{ will-change:transform; transform-style:preserve-3d; perspective:900px }
        .fx-tilt{ transition: transform .15s ease; will-change: transform }
        [data-stagger] > * { --d: calc(var(--i, 0) * .06s) }

        /* responsive */
        @media (max-width:920px){ .login-card{ grid-template-columns:1fr } .brand-panel{ display:none } }
    </style>
@endsection

<div id="devlo-login-root" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
    <div class="login-bg" aria-hidden="true"><span class="brand-orb"></span></div>

    <div class="language-switcher" role="group" aria-label="Language switcher">
        <button wire:click="changeLanguage('ar')" class="lang-btn {{ app()->getLocale() === 'ar' ? 'active' : '' }}" aria-pressed="{{ app()->getLocale() === 'ar' ? 'true' : 'false' }}">عربي</button>
        <button wire:click="changeLanguage('en')" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}" aria-pressed="{{ app()->getLocale() === 'en' ? 'true' : 'false' }}">English</button>
    </div>

    <div class="login-shell">
        <section class="login-card fx-parallax fx-card-breath fx-fadeUp is-in">
            <!-- Left: brand panel -->
            <aside class="brand-panel">
                <div class="brand-head is-in">
                    <header class="form-head fx-fadeIn" style="--d:.05s">
                        <h1 class="form-title">{{ __('messages.login.heading') }}</h1>
                        <p class="form-tag">{{ __('messages.login.subheading') }}</p>
                    </header>

                    <ul class="brand-features" data-stagger>
                        <li><span class="brand-chip"><i class="bi bi-lightning-charge"></i></span>{{ __('messages.login.feature_real_time_updates') }}</li>
                        <li><span class="brand-chip"><i class="bi bi-columns-gap"></i></span>{{ __('messages.login.feature_theme_customizer') }}</li>
                        <li><span class="brand-chip"><i class="bi bi-graph-up-arrow"></i></span>{{ __('messages.login.feature_analytics_dashboard') }}</li>
                        <li><span class="brand-chip"><i class="bi bi-images"></i></span>{{ __('messages.login.feature_media_hub') }}</li>
                        <li><span class="brand-chip"><i class="bi bi-search"></i></span>{{ __('messages.login.feature_lightning_search') }}</li>
                        <li><span class="brand-chip"><i class="bi bi-translate"></i></span>{{ __('messages.login.feature_multi_language_support') }}</li>
                        <li><span class="brand-chip"><i class="bi bi-sliders2"></i></span>{{ __('messages.login.feature_seo_tools') }}</li>
                        <li><span class="brand-chip"><i class="bi bi-arrow-left-right"></i></span>{{ __('messages.login.feature_rtl_darkmode') }}</li>
                    </ul>
                </div>
            </aside>

            <!-- Right: form panel -->
            <article class="form-panel is-in">
                <div class="brand-row fx-fadeIn" style="--d:.12s">
                    @if($isZentra)
                        <img src="{{ asset('assets/dashboard/images/zentra/logo-light.png') }}" class="brand-logo brand-logo-light fx-logoFloat" alt="Zentra">
                        <img src="{{ asset('assets/dashboard/images/zentra/logo-dark.png') }}"  class="brand-logo brand-logo-dark  fx-logoFloat" alt="Zentra">
                    @else
                        <img src="{{ asset('assets/dashboard/images/logo-light.png') }}" class="brand-logo brand-logo-light fx-logoFloat" alt="Taktek">
                        <img src="{{ asset('assets/dashboard/images/logo-dark.png') }}"  class="brand-logo brand-logo-dark  fx-logoFloat" alt="Taktek">
                    @endif
                </div>

                <form wire:submit.prevent="login" class="form-stack fx-scaleIn is-in" novalidate>
                    <!-- Email -->
                    <div class="input-wrap fx-slideInX is-in" style="--slide-x:-12px; --d:.18s">
                        <i class="bi bi-envelope"></i>
                        <input type="text" name="email" wire:model="email" class="form-control" inputmode="email" autocomplete="username" placeholder="{{ __('messages.login.email') }}">
                    </div>

                    <!-- Password -->
                    <div class="input-wrap fx-slideInX is-in" style="--slide-x:-12px; --d:.24s">
                        <i class="bi bi-shield-lock"></i>
                        <input id="devlo-password" type="password" name="password" wire:model="password" class="form-control" autocomplete="current-password" placeholder="{{ __('messages.login.password') }}">
                        <button type="button" class="btn-eye" onclick="
              const p = document.getElementById('devlo-password');
              const t = this.querySelector('i');
              const isText = p.type === 'text';
              p.type = isText ? 'password' : 'text';
              t.classList.toggle('bi-eye'); t.classList.toggle('bi-eye-slash');
            " aria-label="{{ __('messages.login.show_hide_password') }}">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    @if(session()->has('error'))
                        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                    @endif

                    <div class="form-actions">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">{{ __('messages.login.remember_me') }}</label>
                        </div>
                        <a href="#" class="link-muted">{{ __('messages.login.forgot_password') }}</a>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>{{ __('messages.login.login_button') }}</span>
                    </button>
                </form>

                <footer class="form-foot">
                    @if($isZentra)
                        {{ __('messages.footer.copy_right_line' ,['company' => 'Zentra']) }}
                    @else
                        {{ __('messages.footer.copy_right_line' ,['company' => __('messages.footer.taktek')]) }}
                    @endif
                    &nbsp;© {{ now()->year }}
                </footer>
            </article>
        </section>
    </div>
</div>
