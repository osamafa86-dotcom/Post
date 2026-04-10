/**
 * homepage-lite.js — Zero-framework homepage interactions
 * No jQuery, no Bootstrap JS, no Livewire, no Alpine.
 * Loaded on first user interaction. Total: ~5KB.
 */
(function () {
  'use strict';

  /* ── Accordion ─────────────────────────────────────── */
  function initAccordions() {
    // Hide inactive accordion contents
    document.querySelectorAll('.fb-accordion-item:not(.fb-active) .fb-accordion-content').forEach(function (el) {
      el.style.display = 'none';
    });

    document.querySelectorAll('.fb-accordion .fb-accordion-title').forEach(function (title) {
      title.addEventListener('click', function () {
        var item = title.closest('.fb-accordion-item');
        if (item.classList.contains('fb-active')) return;

        // Close active
        document.querySelectorAll('.fb-accordion-item.fb-active').forEach(function (active) {
          active.classList.remove('fb-active');
          active.classList.add('fb-closed');
          var c = active.querySelector('.fb-accordion-content');
          if (c) c.style.display = 'none';
        });

        // Open clicked
        item.classList.remove('fb-closed');
        item.classList.add('fb-active');
        var content = item.querySelector('.fb-accordion-content');
        if (content) content.style.display = '';
      });
    });

    // Close on image click
    document.querySelectorAll('.fb-accordion .fb-accordion-content .image img').forEach(function (img) {
      img.addEventListener('click', function (e) {
        e.stopPropagation();
        var item = img.closest('.fb-accordion-item');
        if (item && item.classList.contains('fb-active')) {
          item.classList.remove('fb-active');
          item.classList.add('fb-closed');
          var c = item.querySelector('.fb-accordion-content');
          if (c) c.style.display = 'none';
        }
      });
    });
  }

  /* ── Tabs ───────────────────────────────────────────── */
  function initTabs() {
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
    document.querySelectorAll('.buttons-group-tabs li').forEach(function (li) {
      li.addEventListener('click', function () {
        var tab = li.getAttribute('data-tab') || li.dataset.tab;
        document.querySelectorAll('.buttons-group-tabs li').forEach(function (el) {
          el.classList.remove('active');
        });
        li.classList.add('active');
        document.querySelectorAll('.tab-pane').forEach(function (p) { p.style.display = 'none'; });
        document.querySelectorAll('.' + tab).forEach(function (p) { p.style.display = ''; });
      });
    });

    // Bars / cells toggle
    var barsC = document.querySelector('.bars-container');
    if (barsC) barsC.classList.add('active');

    document.querySelectorAll('.bars-container, .cells-container i').forEach(function (el) {
      el.addEventListener('click', function () {
        document.querySelectorAll('.bars-container, .cells-container').forEach(function (c) {
          c.classList.remove('active');
        });
        el.classList.add('active');
      });
    });

    var barsBtn = document.getElementById('bars-btn');
    var cellsBtn = document.getElementById('cells-btn');
    if (barsBtn) {
      barsBtn.addEventListener('click', function () { toggleView(true); });
    }
    if (cellsBtn) {
      cellsBtn.addEventListener('click', function () { toggleView(false); });
    }

    function toggleView(isBars) {
      toggle('.hide-right-section', isBars);
      toggle('.toggle-menu', !isBars);
      toggle('.toggle-menu-card', isBars);
      var bc = document.querySelector('.bars-container');
      var cc = document.querySelector('.cells-container');
      if (bc) bc.classList.toggle('active', isBars);
      if (cc) cc.classList.toggle('active', !isBars);
    }

    function toggle(sel, show) {
      document.querySelectorAll(sel).forEach(function (el) {
        el.style.display = show ? '' : 'none';
      });
    }
  }

  /* ── Share buttons ─────────────────────────────────── */
  function initShareButtons() {
    document.addEventListener('click', function (e) {
      var trigger = e.target.closest('.share-arrow, .share h4, .btn-share');
      if (!trigger) return;
      e.preventDefault();
      var parent = trigger.parentElement;
      var icons = parent ? parent.querySelector('.share-social-icons') : null;
      if (!icons) {
        icons = trigger.nextElementSibling;
        if (!icons || !icons.classList.contains('share-social-icons')) return;
      }
      if (icons.classList.contains('show')) {
        icons.classList.remove('show');
        setTimeout(function () { icons.style.display = 'none'; }, 300);
      } else {
        icons.style.display = '';
        setTimeout(function () { icons.classList.add('show'); }, 10);
      }
    });
  }

  /* ── Scroll to top ─────────────────────────────────── */
  function initScrollToTop() {
    var btn = document.getElementById('scrollToTop');
    if (!btn) return;

    window.addEventListener('scroll', function () {
      btn.classList.toggle('show', window.scrollY > 800);
    }, { passive: true });

    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ── Load more (fetch + DOM swap) ──────────────────── */
  function initLoadMore() {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('#load-more-btn');
      if (!btn) return;
      e.preventDefault();

      var url = btn.href;
      btn.innerHTML = '<span class="spinner-grow spinner-grow-sm text-info" role="status"></span> جاري التحميل...';
      btn.style.pointerEvents = 'none';

      fetch(url)
        .then(function (r) { return r.text(); })
        .then(function (html) {
          var doc = new DOMParser().parseFromString(html, 'text/html');
          var newContent = doc.querySelector('.hide-right-section');
          var newMore = doc.querySelector('#view-more-container');
          var currentContent = document.querySelector('.hide-right-section');
          var currentMore = document.getElementById('view-more-container');

          if (currentContent && newContent) {
            currentContent.innerHTML = newContent.innerHTML;
          }

          if (currentMore) {
            if (newMore) {
              currentMore.innerHTML = newMore.innerHTML;
            } else {
              currentMore.remove();
            }
          }

          // Re-init accordions for newly loaded content
          initAccordions();

          history.replaceState(null, '', url);
        })
        .catch(function () {
          btn.innerHTML = 'عرض المزيد <i class="fa-solid fa-arrow-left"></i>';
          btn.style.pointerEvents = '';
        });
    });
  }

  /* ── Audio on-demand (WaveSurfer dynamic import) ──── */
  var activePlayer = null;

  function destroyActivePlayer() {
    if (!activePlayer) return;
    try { activePlayer.ws.pause(); activePlayer.ws.destroy(); } catch (e) {}
    var oldBtn = activePlayer.container.querySelector('.play-pause');
    if (oldBtn) oldBtn.innerHTML = '<i class="fa-solid fa-play"></i>';
    var oldWave = activePlayer.container.querySelector('.waveform-ph');
    if (oldWave) oldWave.innerHTML = '';
    activePlayer.container._ws = null;
    activePlayer = null;
  }

  function loadScript(src) {
    return new Promise(function (resolve) {
      var s = document.createElement('script');
      s.src = src;
      s.onload = resolve;
      s.onerror = function () { console.warn('Failed: ' + src); resolve(); };
      document.head.appendChild(s);
    });
  }

  function formatTime(sec) {
    var m = Math.floor(sec / 60), s = Math.floor(sec % 60);
    return m + ':' + (s < 10 ? '0' : '') + s;
  }

  function initAudioOnDemand() {
    document.addEventListener('click', function (e) {
      var playBtn = e.target.closest('.play-pause');
      if (playBtn) { handlePlay(playBtn); return; }
      var rew = e.target.closest('.rewind');
      if (rew) { seekBy(rew, -10); return; }
      var fwd = e.target.closest('.forward');
      if (fwd) { seekBy(fwd, 10); return; }
    });
  }

  function handlePlay(btn) {
    var container = btn.closest('.audio-player');
    if (!container) return;

    if (container._ws) {
      container._ws.playPause();
      btn.innerHTML = container._ws.isPlaying()
        ? '<i class="fa-solid fa-pause"></i>'
        : '<i class="fa-solid fa-play"></i>';
      return;
    }

    if (container._loading) return;
    container._loading = true;

    var waveEl = container.querySelector('.waveform-ph');
    var audio = waveEl ? waveEl.dataset.audio : null;
    if (!audio || audio === '' || audio === 'null' || audio === 'undefined') {
      container._loading = false;
      return;
    }

    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    destroyActivePlayer();

    var p = window.WaveSurfer ? Promise.resolve() : loadScript('https://unpkg.com/wavesurfer.js');

    p.then(function () {
      if (!window.WaveSurfer) {
        container._loading = false;
        btn.innerHTML = '<i class="fa-solid fa-play"></i>';
        return;
      }

      var ws = WaveSurfer.create({
        container: waveEl,
        waveColor: '#E0E0E0',
        progressColor: '#33B3C0',
        height: 20,
        responsive: true,
        backend: 'MediaElement',
      });

      ws.on('error', function (err) { console.warn('WaveSurfer:', err); });

      try { ws.load(audio); } catch (ex) {
        container._loading = false;
        btn.innerHTML = '<i class="fa-solid fa-play"></i>';
        return;
      }

      container._ws = ws;
      activePlayer = { ws: ws, container: container };
      container._loading = false;

      // Volume
      var slider = container.querySelector('.volume-slider');
      var icon = container.querySelector('.volume-icon');
      ws.setVolume(1);
      if (slider) {
        slider.value = 1;
        slider.addEventListener('input', function () {
          var v = parseFloat(slider.value);
          ws.setVolume(v);
          var pct = v * 100;
          slider.style.background = 'linear-gradient(to right,#00a2b9 0%,#00a2b9 ' + pct + '%,#E0E0E0 ' + pct + '%,#E0E0E0 100%)';
          if (icon) icon.innerHTML = v === 0 ? '<i class="fas fa-volume-mute"></i>' : '<i class="fas fa-volume-up"></i>';
        });
      }

      // Time
      var td = container.querySelector('.time-display');
      ws.on('audioprocess', function () {
        if (td) td.textContent = formatTime(ws.getCurrentTime()) + ' / ' + formatTime(ws.getDuration());
      });
      ws.on('ready', function () {
        if (td) td.textContent = formatTime(0) + ' / ' + formatTime(ws.getDuration());
        ws.play();
        btn.innerHTML = '<i class="fa-solid fa-pause"></i>';
      });
      ws.on('finish', function () {
        btn.innerHTML = '<i class="fa-solid fa-play"></i>';
      });
    });
  }

  function seekBy(btn, delta) {
    var c = btn.closest('.audio-player');
    if (!c || !c._ws) return;
    var ws = c._ws, d = ws.getDuration();
    if (d <= 0) return;
    ws.seekTo(Math.max(0, Math.min(d, ws.getCurrentTime() + delta)) / d);
  }

  /* ── Notification dropdown (vanilla) ───────────────── */
  function initNotifications() {
    document.addEventListener('click', function (e) {
      // Close all notification menus on outside click
      if (!e.target.closest('.icon-container')) {
        document.querySelectorAll('.notifications-mega-menu').forEach(function (m) {
          m.style.display = 'none';
        });
        return;
      }

      // Clicked on notification menu itself — stop propagation
      if (e.target.closest('.notifications-mega-menu')) {
        e.stopPropagation();
        return;
      }

      // Clicked on icon container with notification menu
      var iconContainer = e.target.closest('.icon-container');
      var menu = iconContainer ? iconContainer.querySelector('.notifications-mega-menu') : null;
      if (!menu) return;

      e.stopPropagation();

      // Hide other mega menus
      document.querySelectorAll('.top-bar-container .mega-menu').forEach(function (m) {
        if (m !== menu) m.style.display = 'none';
      });

      // Toggle
      var items = menu.querySelectorAll('.notification-item');
      if (items.length === 0) {
        var container = menu.querySelector('.notifications-container');
        if (container && !container.querySelector('.no-notifications-msg')) {
          container.innerHTML = '<p class="no-notifications-msg">\u0644\u0627 \u064a\u0648\u062c\u062f \u0625\u0634\u0639\u0627\u0631\u0627\u062a</p>';
        }
      }
      menu.style.display = menu.style.display === 'none' ? '' : 'none';
    });
  }

  /* ── Opportunity modal (vanilla — no Bootstrap JS) ── */
  function initOpportunityModal() {
    var modal = document.getElementById('opportunityModal1');
    if (!modal) return;

    // Open
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.opportunity-btn');
      if (!btn) return;

      var title = modal.querySelector('.modal-title');
      var img = modal.querySelector('.modal-body img');
      var desc = modal.querySelector('.modal-body p');

      if (title) title.textContent = btn.dataset.name || '';
      if (img) { img.src = btn.dataset.image || ''; img.alt = btn.dataset.name || ''; }
      if (desc) desc.textContent = btn.dataset.description || '';

      modal.classList.add('show');
      modal.style.display = 'block';
      modal.removeAttribute('aria-hidden');
      document.body.classList.add('modal-open');

      // Create backdrop
      if (!document.getElementById('modal-backdrop')) {
        var backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'modal-backdrop';
        document.body.appendChild(backdrop);
      }
    });

    // Close
    function closeModal() {
      modal.classList.remove('show');
      modal.style.display = 'none';
      modal.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('modal-open');
      var backdrop = document.getElementById('modal-backdrop');
      if (backdrop) backdrop.remove();
    }

    modal.addEventListener('click', function (e) {
      if (e.target.closest('.modal-close-btn') || e.target === modal) {
        closeModal();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.classList.contains('show')) {
        closeModal();
      }
    });
  }

  /* ── CORS suppression ──────────────────────────────── */
  function initCORSSuppression() {
    window.addEventListener('unhandledrejection', function (event) {
      if (event.reason && event.reason.message &&
          event.reason.message.indexOf('Failed to fetch') !== -1) {
        event.preventDefault();
      }
    });
  }

  /* ── Init all — use requestIdleCallback for spreading ── */
  var ric = window.requestIdleCallback || function (cb) {
    return setTimeout(function () {
      var start = Date.now();
      cb({ didTimeout: false, timeRemaining: function () { return Math.max(0, 50 - (Date.now() - start)); } });
    }, 1);
  };

  ric(function () {
    initAccordions();
    initTabs();
    initShareButtons();
    initScrollToTop();
    initLoadMore();
    initAudioOnDemand();
    initNotifications();
    initOpportunityModal();
    initCORSSuppression();
  });

})();
