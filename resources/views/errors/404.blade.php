@php
    $isFimed = config('app.launch') === 'fimed';
    $currentLocale = app()->getLocale();
    $isRtl = $currentLocale === 'ar';
    $lang = $currentLocale;
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - {{ __('messages.page_not_found_title') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @if($isFimed)
        <link rel="stylesheet" href="{{ asset('frontend/fimed/css/tokens.css') }}">
    @endif
    <style>
        :root {
            --c-primary: {{ $isFimed ? '#B71C1C' : '#3b82f6' }};
            --c-primary-light: {{ $isFimed ? '#E53935' : '#60a5fa' }};
            --c-bg: #FAFAF7;
            --c-surface: #FFFFFF;
            --c-ink: #1A1A1A;
            --c-muted: #6B6B6B;
            --c-border: #E0E0E0;
            --font: {{ $isRtl ? "'Cairo'" : "'Inter'" }}, system-ui, sans-serif;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--font);
            background: var(--c-bg);
            color: var(--c-ink);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            direction: {{ $isRtl ? 'rtl' : 'ltr' }};
        }
        .error-page {
            text-align: center;
            padding: 40px 24px;
            max-width: 600px;
        }
        .error-code {
            font-size: clamp(100px, 20vw, 180px);
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, var(--c-primary), var(--c-primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }
        .error-icon {
            font-size: 48px;
            color: var(--c-primary);
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }
        .error-title {
            font-size: clamp(20px, 4vw, 28px);
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--c-ink);
        }
        .error-text {
            font-size: 16px;
            color: var(--c-muted);
            line-height: 1.7;
            margin-bottom: 32px;
        }
        .error-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .error-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            font-family: var(--font);
        }
        .error-btn--primary {
            background: var(--c-primary);
            color: #fff;
        }
        .error-btn--primary:hover {
            background: var(--c-primary-light);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        .error-btn--secondary {
            background: var(--c-surface);
            color: var(--c-ink);
            border: 2px solid var(--c-border);
        }
        .error-btn--secondary:hover {
            border-color: var(--c-primary);
            color: var(--c-primary);
            transform: translateY(-2px);
        }
        .error-divider {
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--c-primary), var(--c-primary-light));
            border-radius: 2px;
            margin: 0 auto 24px;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="error-page">
        <div class="error-code">404</div>
        <div class="error-icon"><i class="fas fa-map-signs"></i></div>
        <div class="error-divider"></div>
        <h1 class="error-title">{{ __('messages.page_not_found_title') }}</h1>
        <p class="error-text">{{ __('messages.page_not_found_text') }}</p>
        <div class="error-actions">
            @if($isFimed)
                <a href="{{ route('main.fimed.index', $lang) }}" class="error-btn error-btn--primary">
                    <i class="fas fa-home"></i>
                    {{ __('messages.home') }}
                </a>
                <a href="javascript:history.back()" class="error-btn error-btn--secondary">
                    <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}"></i>
                    {{ __('messages.go_back') }}
                </a>
            @else
                <a href="/" class="error-btn error-btn--primary">
                    <i class="fas fa-home"></i>
                    {{ __('messages.home') }}
                </a>
                <a href="javascript:history.back()" class="error-btn error-btn--secondary">
                    <i class="fas fa-arrow-{{ $isRtl ? 'right' : 'left' }}"></i>
                    {{ __('messages.go_back') }}
                </a>
            @endif
        </div>
    </div>
</body>
</html>
