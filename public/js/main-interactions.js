/**
 * main-interactions.js — Incremental hydration orchestrator
 * Loaded on first user interaction by inline interaction-loader.
 * Uses window.__schedule() from hydration-scheduler.js to spread
 * work across idle frames. No single task > 50ms.
 */
(function () {
  'use strict';

  var schedule = window.__schedule;
  var BASE = (document.querySelector('meta[name="asset-url"]') || {}).content || '';

  /* ── Utility: dynamic script loader ─────────────────── */
  function loadScript(src) {
    return new Promise(function (resolve) {
      var s = document.createElement('script');
      s.src = src;
      s.onload = resolve;
      s.onerror = function () { console.warn('Failed to load: ' + src); resolve(); };
      document.head.appendChild(s);
    });
  }

  function formatTime(sec) {
    var m = Math.floor(sec / 60), s = Math.floor(sec % 60);
    return m + ':' + (s < 10 ? '0' : '') + s;
  }

  function updateSliderBg(slider, vol) {
    var pct = vol * 100;
    slider.style.background =
      'linear-gradient(to right,#00a2b9 0%,#00a2b9 ' + pct + '%,#E0E0E0 ' + pct + '%,#E0E0E0 100%)';
  }

  /* ══════════════════════════════════════════════════════
     LOAD CHAIN:  jQuery → (Bootstrap + Owl) → schedule modules
     ══════════════════════════════════════════════════════ */
  loadScript(BASE + 'assets/main/palestine_post/js/jquery-3.7.1.min.js')
    .then(function () {
      return Promise.all([
        loadScript(BASE + 'assets/main/palestine_post/js/owl.carousel.min.js'),
        loadScript(BASE + 'assets/main/palestine_post/js/bootstrap.bundle.min.js'),
      ]);
    })
    .then(function () {
      // ── Schedule each module in separate idle frames ──
      schedule(initAccordions);
      schedule(initTabs);
      schedule(initDropdowns);
      schedule(initMenus);
      schedule(initScrollToTop);
      schedule(initShareButtons);
      schedule(initCarouselsLazy);
      schedule(initAudioOnDemand);
      schedule(initCORSSuppression);
      schedule(initLivewireHook);

      // Non-critical: currencies & weather in their own frames
      schedule(function () { loadScript(BASE + 'assets/main/palestine_post/js/currencies.js'); });
      schedule(function () { loadScript(BASE + 'assets/main/palestine_post/js/weather.js'); });

      // Flatpickr — only if the page has date inputs
      schedule(function () {
        if (document.querySelector('[data-flatpickr],.flatpickr-input,input[type="date"]')) {
          loadScript('https://cdn.jsdelivr.net/npm/flatpickr').then(function () {
            loadScript('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js');
          });
        }
      });

      // Replay any buffered Livewire carousel-reload events
      schedule(function () {
        if (window.__pendingCarouselReload) {
          delete window.__pendingCarouselReload;
          if (window.__reloadCarousels) window.__reloadCarousels();
        }
      });
    });

  /* ══════════════════════════════════════════════════════
     MODULE: Accordions
     ══════════════════════════════════════════════════════ */
  function initAccordions() {
    var $ = window.jQuery;
    if (!$) return;

    $('.fb-accordion-item').not('.fb-active').find('.fb-accordion-content').hide();

    $('.fb-accordion .fb-accordion-title').click(function () {
      var $item = $(this).closest('.fb-accordion-item');
      if ($item.hasClass('fb-active')) return;
      $('.fb-accordion .fb-accordion-item.fb-active')
        .removeClass('fb-active').addClass('fb-closed')
        .find('.fb-accordion-content').slideUp(300);
      $item.removeClass('fb-closed').addClass('fb-active');
      $item.find('.fb-accordion-content').slideDown(300);
    });

    $('.fb-accordion .fb-accordion-content .image img').click(function (e) {
      e.stopPropagation();
      var $item = $(this).closest('.fb-accordion-item');
      if ($item.hasClass('fb-active')) {
        $item.removeClass('fb-active').addClass('fb-closed');
        $item.find('.fb-accordion-content').slideUp(300);
      }
    });
  }

  /* ══════════════════════════════════════════════════════
     MODULE: Tabs (local news tabs + tab panes + bars/cells)
     ══════════════════════════════════════════════════════ */
  function initTabs() {
    var $ = window.jQuery;
    if (!$) return;

    // Local news tabs
    document.querySelectorAll('.right-choises li').forEach(function (li) {
      li.addEventListener('click', function () {
        var idx = li.getAttribute('data-index');
        document.querySelectorAll('.right-choises li').forEach(function (el) {
          el.classList.remove('active');
        });
        li.classList.add('active');
        document.querySelectorAll('.local-news-tab').forEach(function (tab) {
          tab.classList.toggle('active', tab.getAttribute('data-index') === idx);
        });
      });
    });

    // Tab panes
    $('.buttons-group-tabs li').click(function () {
      var tab = $(this).data('tab');
      $('.buttons-group-tabs li').removeClass('active');
      $(this).addClass('active');
      $('.tab-pane').hide();
      $('.' + tab).show();
    });

    // Bars / cells toggle
    $('.bars-container').addClass('active');
    $('.bars-container, .cells-container i').click(function () {
      $('.bars-container, .cells-container').removeClass('active');
      $(this).addClass('active');
    });
    $('#bars-btn, #cells-btn').click(function () {
      var isBars = $(this).is('#bars-btn');
      $('.hide-right-section').toggle(isBars);
      $('.toggle-menu').toggle(!isBars);
      $('.toggle-menu-card').toggle(isBars);
      $('.bars-container').toggleClass('active', isBars);
      $('.cells-container').toggleClass('active', !isBars);
    });
  }

  /* ══════════════════════════════════════════════════════
     MODULE: Dropdowns (notification mega menu)
     ══════════════════════════════════════════════════════ */
  function initDropdowns() {
    var $ = window.jQuery;
    if (!$) return;

    $(document).on('click', function () { $('.notifications-mega-menu').hide(); });
    $(document).on('click', '.notifications-mega-menu', function (e) { e.stopPropagation(); });
    $('.notifications-mega-menu').closest('.icon-container').on('click', function (e) {
      e.stopPropagation();
      var notificationMenu = $(this).find('.notifications-mega-menu');
      $('.top-bar-container .mega-menu').not(notificationMenu).hide();
      var notificationItems = notificationMenu.find('.notification-item');
      if (notificationItems.length === 0) {
        var container = notificationMenu.find('.notifications-container');
        if (!container.find('.no-notifications-msg').length) {
          container.html('<p class="no-notifications-msg">\u0644\u0627 \u064a\u0648\u062c\u062f \u0625\u0634\u0639\u0627\u0631\u0627\u062a</p>');
        }
      }
      notificationMenu.toggle();
    });
  }

  /* ══════════════════════════════════════════════════════
     MODULE: Menus (Bootstrap-powered, initialized by loading bootstrap.bundle)
     ══════════════════════════════════════════════════════ */
  function initMenus() {
    // Bootstrap JS self-initializes dropdowns/collapses via data attributes.
    // This module exists as a hook for Livewire re-init if needed.
    if (window.bootstrap && window.bootstrap.Dropdown) {
      document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (el) {
        if (!bootstrap.Dropdown.getInstance(el)) {
          new bootstrap.Dropdown(el);
        }
      });
    }
  }

  /* ══════════════════════════════════════════════════════
     MODULE: Share buttons
     ══════════════════════════════════════════════════════ */
  function initShareButtons() {
    var $ = window.jQuery;
    if (!$) return;

    $('.share-arrow, .share h4, .btn-share').click(function (e) {
      e.preventDefault();
      var shareIcons = $(this).siblings('.share-social-icons');
      if (shareIcons.hasClass('show')) {
        shareIcons.removeClass('show');
        setTimeout(function () { shareIcons.hide(); }, 300);
      } else {
        shareIcons.show();
        setTimeout(function () { shareIcons.addClass('show'); }, 10);
      }
    });
  }

  /* ══════════════════════════════════════════════════════
     MODULE: Scroll to top
     ══════════════════════════════════════════════════════ */
  function initScrollToTop() {
    var $ = window.jQuery;
    if (!$) return;

    $(window).on('scroll', function () {
      $('#scrollToTop').toggleClass('show', $(this).scrollTop() > 800);
    });
    $('#scrollToTop').on('click', function () {
      $('html, body').animate({ scrollTop: 0 }, 'smooth');
    });
  }

  /* ══════════════════════════════════════════════════════
     MODULE: CORS suppression
     ══════════════════════════════════════════════════════ */
  function initCORSSuppression() {
    window.addEventListener('unhandledrejection', function (event) {
      if (event.reason && event.reason.message &&
          event.reason.message.indexOf('Failed to fetch') !== -1) {
        event.preventDefault();
      }
    });
  }

  /* ══════════════════════════════════════════════════════
     LAZY CAROUSELS via IntersectionObserver (unchanged)
     ══════════════════════════════════════════════════════ */
  var NAV_TEXT = [
    "<i class='fa-solid fa-chevron-right'></i>",
    "<i class='fa-solid fa-chevron-left'></i>"
  ];

  var CAROUSEL_CONFIGS = {
    '#slider1':      { loop:true, margin:20, rtl:true, nav:true, navText:NAV_TEXT, responsive:{0:{items:1},600:{items:2},1000:{items:3}} },
    '#slider2':      { loop:true, margin:20, rtl:true, nav:true, mouseDrag:false, touchDrag:false, navText:NAV_TEXT, responsive:{0:{items:1},600:{items:2},1000:{items:2}} },
    '#slider3':      { loop:true, rtl:true, margin:20, nav:true, navText:NAV_TEXT, responsive:{0:{items:1},600:{items:2},1000:{items:2}} },
    '#slider4':      { loop:true, margin:15, rtl:true, nav:true, dots:false, navText:NAV_TEXT, responsive:{0:{items:1},600:{items:2},1000:{items:3}} },
    '#safit-slider': { loop:true, margin:20, rtl:true, nav:true, dots:true, autoplay:true, autoplayTimeout:3000, autoplayHoverPause:true, responsive:{0:{items:1},600:{items:2},1000:{items:3}} },
    '.owl-news':     { loop:true, margin:20, rtl:true, nav:true, navText:NAV_TEXT, responsive:{0:{items:1},600:{items:2},1000:{items:2}} }
  };

  function initCarouselsLazy() {
    var $ = window.jQuery;
    if (!$ || !$.fn.owlCarousel) return;

    // Expose globally for Livewire reloadOwlCarousel event
    window.__reloadCarousels = function () {
      Object.keys(CAROUSEL_CONFIGS).forEach(function (sel) {
        var el = document.querySelector(sel);
        if (el) {
          try {
            $(sel).trigger('destroy.owl.carousel').removeClass('owl-loaded');
            $(sel).find('.owl-stage-outer').children().unwrap();
            $(sel).removeData();
          } catch (e) {}
          $(sel).owlCarousel(CAROUSEL_CONFIGS[sel]);
        }
      });
    };

    if (!('IntersectionObserver' in window)) {
      window.__reloadCarousels();
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        observer.unobserve(el);

        var sel = el.id ? '#' + el.id : null;
        if (!sel) {
          Object.keys(CAROUSEL_CONFIGS).forEach(function (s) {
            if (s.charAt(0) === '.' && el.matches(s)) sel = s;
          });
        }
        if (sel && CAROUSEL_CONFIGS[sel]) {
          $(el).owlCarousel(CAROUSEL_CONFIGS[sel]);
        }
      });
    }, { rootMargin: '200px 0px' });

    Object.keys(CAROUSEL_CONFIGS).forEach(function (sel) {
      document.querySelectorAll(sel).forEach(function (el) {
        observer.observe(el);
      });
    });
  }

  /* ══════════════════════════════════════════════════════
     ON-DEMAND AUDIO (WaveSurfer click-to-init)
     Player creation is scheduled via __schedule.
     Only one player active at a time.
     ══════════════════════════════════════════════════════ */
  var activePlayer = null;

  function destroyActivePlayer() {
    if (!activePlayer) return;
    try { activePlayer.ws.pause(); activePlayer.ws.destroy(); } catch (e) {}
    var oldBtn = activePlayer.container.querySelector('.play-pause');
    if (oldBtn) oldBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
    var oldWaveform = activePlayer.container.querySelector('.waveform-ph');
    if (oldWaveform) oldWaveform.innerHTML = '';
    activePlayer.container.__ws = null;
    activePlayer = null;
  }

  function initAudioOnDemand() {
    document.addEventListener('click', function (e) {
      var playBtn = e.target.closest('.play-pause');
      if (playBtn) { handlePlayPause(playBtn); return; }

      var rewBtn = e.target.closest('.rewind');
      if (rewBtn) { seekBy(rewBtn, -10); return; }

      var fwdBtn = e.target.closest('.forward');
      if (fwdBtn) { seekBy(fwdBtn, 10); return; }
    });
  }

  function handlePlayPause(btn) {
    var container = btn.closest('.audio-player');
    if (!container) return;

    // Already initialized — toggle play/pause (instant, no schedule needed)
    if (container.__ws) {
      container.__ws.playPause();
      btn.innerHTML = container.__ws.isPlaying()
        ? '<i class="fa-solid fa-pause"></i>'
        : '<i class="fa-solid fa-play"></i>';
      return;
    }

    if (container.__loading) return;
    container.__loading = true;

    var waveformEl = container.querySelector('.waveform-ph');
    var audioPath = waveformEl ? waveformEl.dataset.audio : null;
    if (!audioPath || audioPath === '' || audioPath === 'null' || audioPath === 'undefined') {
      container.__loading = false;
      return;
    }

    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    destroyActivePlayer();

    var p = window.WaveSurfer ? Promise.resolve() : loadScript('https://unpkg.com/wavesurfer.js');

    p.then(function () {
      if (!window.WaveSurfer) {
        container.__loading = false;
        btn.innerHTML = '<i class="fa-solid fa-play"></i>';
        return;
      }

      // Schedule the heavy player creation in an idle frame
      schedule(function () {
        createPlayer(container, waveformEl, audioPath, btn);
      });
    });
  }

  function createPlayer(container, waveformEl, audioPath, btn) {
    var ws = WaveSurfer.create({
      container: waveformEl,
      waveColor: '#E0E0E0',
      progressColor: '#33B3C0',
      height: 20,
      responsive: true,
      backend: 'MediaElement',
    });

    ws.on('error', function (err) {
      console.warn('WaveSurfer error:', audioPath, err);
    });

    try {
      ws.load(audioPath);
    } catch (ex) {
      console.warn('WaveSurfer load failed:', ex.message);
      container.__loading = false;
      btn.innerHTML = '<i class="fa-solid fa-play"></i>';
      return;
    }

    container.__ws = ws;
    activePlayer = { ws: ws, container: container };
    container.__loading = false;

    // Volume control
    var volumeSlider = container.querySelector('.volume-slider');
    var volumeIcon = container.querySelector('.volume-icon');
    var vol = 1.0;
    ws.setVolume(vol);

    if (volumeSlider) {
      volumeSlider.value = vol;
      updateSliderBg(volumeSlider, vol);
      volumeSlider.addEventListener('input', function () {
        vol = parseFloat(volumeSlider.value);
        ws.setVolume(vol);
        updateSliderBg(volumeSlider, vol);
        if (volumeIcon) {
          volumeIcon.innerHTML = vol === 0
            ? '<i class="fas fa-volume-mute"></i>'
            : '<i class="fas fa-volume-up"></i>';
        }
      });
    }

    // Time display
    var timeDisplay = container.querySelector('.time-display');
    ws.on('audioprocess', function () {
      if (timeDisplay)
        timeDisplay.textContent = formatTime(ws.getCurrentTime()) + ' / ' + formatTime(ws.getDuration());
    });

    ws.on('ready', function () {
      if (timeDisplay)
        timeDisplay.textContent = formatTime(0) + ' / ' + formatTime(ws.getDuration());
      ws.play();
      btn.innerHTML = '<i class="fa-solid fa-pause"></i>';
    });

    ws.on('finish', function () {
      btn.innerHTML = '<i class="fa-solid fa-play"></i>';
    });
  }

  function seekBy(btn, delta) {
    var container = btn.closest('.audio-player');
    if (!container || !container.__ws) return;
    var ws = container.__ws;
    var d = ws.getDuration();
    if (d <= 0) return;
    var t = Math.max(0, Math.min(d, ws.getCurrentTime() + delta));
    ws.seekTo(t / d);
  }

  /* ══════════════════════════════════════════════════════
     STEP 4 — LIVEWIRE HOOK
     On DOM morph: only schedule affected modules,
     NOT full re-init.
     ══════════════════════════════════════════════════════ */
  function initLivewireHook() {
    // Livewire v3 hook
    if (window.Livewire && Livewire.hook) {
      Livewire.hook('morph.updated', function () {
        schedule(initDropdowns);
        schedule(initTabs);
      });
      return;
    }

    // Fallback: listen for Livewire init event (v3)
    document.addEventListener('livewire:init', function () {
      if (window.Livewire && Livewire.hook) {
        Livewire.hook('morph.updated', function () {
          schedule(initDropdowns);
          schedule(initTabs);
        });
      }
    });

    // Fallback: Livewire v2
    document.addEventListener('livewire:load', function () {
      if (window.Livewire) {
        Livewire.hook('message.processed', function () {
          schedule(initDropdowns);
          schedule(initTabs);
        });
      }
    });
  }

})();
