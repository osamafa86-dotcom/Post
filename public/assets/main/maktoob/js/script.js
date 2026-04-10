/**
 * Maktoobmedia Script File
 * Author: Islam Nasser
 * Version: 1.3 • 25-Aug-2025
 * Description: Initialize site interactions: ticker, navigation, carousels, and video modal (robust Owl wait + scoped controls).
 */
(function (w, d, $) {
    "use strict";


    /* ===================== Helpers ===================== */
    function whenDOMReady(fn) {
        if (d.readyState === 'loading') d.addEventListener('DOMContentLoaded', fn, {once: true});
        else fn();
    }

    // انتظر حتى تتوفر jQuery + Owl ثم نفّذ fn (مع retries + onload)
    function waitForOwl(fn, opts) {
        const maxTries = (opts && opts.maxTries) || 30;   // ~ 30*150ms = 4.5s
        const delay = (opts && opts.delay) || 150;
        let tries = 0, timer = null, done = false;

        function ready() {
            return !!(w.jQuery && w.jQuery.fn && w.jQuery.fn.owlCarousel);
        }

        function attempt() {
            if (done) return;
            if (ready()) {
                done = true;
                clearInterval(timer);
                fn();
                return;
            }
            if (++tries >= maxTries) {
                clearInterval(timer); /* give up silently */
            }
        }

        if (ready()) {
            fn();
            return;
        }
        // poll
        timer = setInterval(attempt, delay);
        // also retry on full load (بعض الصفحات تحمل Owl بعد DOMContentLoaded)
        w.addEventListener('load', () => {
            if (!done) attempt();
        }, {once: true});
    }

    // منع التهيئة المكررة على نفس العنصر
    function ensureOwlOnce($el, initCb) {
        if (!$el.length) return false;
        if ($el.data('owl-initialized')) return false;
        initCb();
        $el.data('owl-initialized', true);
        return true;
    }

    /* ===================== Dropdown: More categories ===================== */
    (function () {
        const setups = [];
        d.querySelectorAll('.category-more-wrapper').forEach(wrapper => {
            const btn = wrapper.querySelector('.category-more');
            const panel = wrapper.querySelector('.categories-dropdown');
            if (btn && panel) setups.push({btn, panel});
        });
        setups.forEach(({btn, panel}) => {
            btn.addEventListener('mouseenter', () => {
                setups.forEach(item => {
                    if (item.panel !== panel) item.panel.classList.remove('open');
                });
                panel.classList.add('open');
            });
            panel.addEventListener('mouseleave', () => panel.classList.remove('open'));
            btn.addEventListener('mouseleave', (e) => {
                if (!panel.contains(e.relatedTarget)) panel.classList.remove('open');
            });
        });
    })();

    /* ===================== Qty controls ===================== */
    function initQtyControls() {
        d.querySelectorAll('.product-card').forEach(card => {
            const dec = card.querySelector('.qty-decrease');
            const inc = card.querySelector('.qty-increase');
            const input = card.querySelector('.qty-input');
            if (!dec || !inc || !input) return;
            dec.addEventListener('click', () => {
                const v = parseInt(input.value || '1', 10);
                if (v > 1) input.value = v - 1;
            });
            inc.addEventListener('click', () => {
                const v = parseInt(input.value || '1', 10);
                input.value = v + 1;
            });
        });
    }

    /* ===================== Ticker ===================== */
    function initTicker() {
        const headlines = [
            'Israel kills more than 70 Palestinians',
            'UN Security Council debates Gaza ceasefire',
            'Mass protests sweep Tel Aviv against the war',
            'WHO warns Gaza health system is collapsing'
        ];
        const el = d.getElementById('breakingTickerText');
        if (!el) return;
        let i = 0;
        setInterval(() => {
            i = (i + 1) % headlines.length;
            el.textContent = headlines[i];
        }, 8000);
    }

    /* ===================== Mobile Nav ===================== */
    function initMobileNav() {
        const toggleBtn = d.querySelector('.menu-toggle');
        const primaryNav = d.querySelector('.primary-nav');
        if (!toggleBtn || !primaryNav) return;
        toggleBtn.addEventListener('click', () => primaryNav.classList.toggle('open'));
    }

    /* ===================== Carousels (scoped + robust) ===================== */
    function initCarousels() {
        const $jq = w.jQuery; // تأكيد على استخدام jQuery الصحيح حتى لو noConflict
        if (!$jq || !$jq.fn || !$jq.fn.owlCarousel) return;

        // Footer thumbs
        $jq('.thumb-carousel.owl-carousel').each(function () {
            const $c = $jq(this);
            ensureOwlOnce($c, () => {
                $c.owlCarousel({
                    loop: false, margin: 16, smartSpeed: 500, animateIn: 'fadeIn', animateOut: 'fadeOut',
                    responsive: {0: {items: 1}, 576: {items: 2}, 768: {items: 3}, 992: {items: 4}}
                });
            });
        });

        // Trending (vertical) — داخل trending-section
        $jq('.trending-section').each(function () {
            const $section = $jq(this);
            const $c = $section.find('.small-cards.owl-carousel');
            ensureOwlOnce($c, () => {
                $c.owlCarousel({
                    items: 1, loop: true, dots: false, smartSpeed: 600, animateIn: 'fadeInUp',
                    animateOut: 'fadeOutDown', mouseDrag: false, touchDrag: true
                });
            });
            $section.find('.trend-prev').off('.tr').on('click.tr', () => $c.trigger('prev.owl.carousel'));
            $section.find('.trend-next').off('.tr').on('click.tr', () => $c.trigger('next.owl.carousel'));
        });

        // Photos slider
        $jq('.photos-slider.owl-carousel').each(function () {
            const $c = $jq(this);
            ensureOwlOnce($c, () => {
                $c.owlCarousel({
                    items: 1,
                    loop: true,
                    smartSpeed: 800,
                    animateIn: 'fadeIn',
                    animateOut: 'fadeOut',
                    dots: true,
                    nav: false,
                    autoplay: false
                });
            });
        });

        // Films & TV — داخل films-section
        $jq('.films-section').each(function () {
            const $section = $jq(this);
            const $c = $section.find('.films-carousel.owl-carousel');
            ensureOwlOnce($c, () => {
                $c.owlCarousel({
                    loop: false, margin: 20, smartSpeed: 500, animateIn: 'fadeIn', animateOut: 'fadeOut',
                    dots: false, nav: false, responsive: {0: {items: 1}, 576: {items: 2}, 992: {items: 3}}
                });
            });
            $section.find('.films-prev').off('.flm').on('click.flm', () => $c.trigger('prev.owl.carousel'));
            $section.find('.films-next').off('.flm').on('click.flm', () => $c.trigger('next.owl.carousel'));
        });

        // Opinion — داخل opinion-section
        $jq('.opinion-section').each(function () {
            const $section = $jq(this);
            const $c = $section.find('.opinion-carousel.owl-carousel');
            ensureOwlOnce($c, () => {
                $c.owlCarousel({
                    loop: false, margin: 20, smartSpeed: 500, animateIn: 'fadeIn', animateOut: 'fadeOut',
                    dots: false, nav: false, responsive: {0: {items: 1}, 576: {items: 2}, 992: {items: 3}}
                });
            });
            $section.find('.opinion-prev').off('.opn').on('click.opn', () => $c.trigger('prev.owl.carousel'));
            $section.find('.opinion-next').off('.opn').on('click.opn', () => $c.trigger('next.owl.carousel'));
        });



        // Trustees / About-Us — داخل .trustees-slider
        $jq('.trustees-slider').each(function () {
            const $section = $jq(this);
            const $c = $section.find('.owl-about-us.owl-carousel');

            ensureOwlOnce($c, () => {
                $c.owlCarousel({
                    loop: true,
                    margin: 20,
                    smartSpeed: 600,
                    nav: false,
                    dots: true,
                    rtl: document.documentElement.dir === 'rtl',
                    autoplay: true,
                    autoplayTimeout: 3000,
                    autoplayHoverPause: true,
                    responsive: {
                        0:    { items: 1 },
                        576:  { items: 2 },
                        768:  { items: 3 },
                        1200: { items: 4 }
                    }
                });
            });

            $section.find('.about-prev').off('.abt').on('click.abt', () => $c.trigger('prev.owl.carousel'));
            $section.find('.about-next').off('.abt').on('click.abt', () => $c.trigger('next.owl.carousel'));
        });

    }

    function observeCarousels() {
        const obs = new MutationObserver((muts) => {
            let needInit = false;
            muts.forEach(m => {
                if (m.addedNodes && m.addedNodes.length) {
                    m.addedNodes.forEach(n => {
                        if (!(n instanceof Element)) return;
                        if (n.matches && (
                            n.matches('.films-carousel.owl-carousel') ||
                            n.matches('.photos-slider.owl-carousel') ||
                            n.matches('.thumb-carousel.owl-carousel') ||
                            n.matches('.opinion-carousel.owl-carousel') ||
                            n.matches('.trending-section .small-cards.owl-carousel') ||
                            n.querySelector('.films-carousel.owl-carousel') ||
                            n.querySelector('.photos-slider.owl-carousel') ||
                            n.querySelector('.thumb-carousel.owl-carousel') ||
                            n.querySelector('.opinion-carousel.owl-carousel') ||
                            n.querySelector('.trending-section .small-cards.owl-carousel')
                        )) needInit = true;
                    });
                }
            });
            if (needInit) waitForOwl(initCarousels);
        });
        obs.observe(d.body, {childList: true, subtree: true});
    }

    /* ===================== Video Modal ===================== */
    function initVideoModal() {
        const modal = d.getElementById('videoModal');
        const iframe = d.getElementById('videoIframe');
        const closeBtn = modal?.querySelector('.modal-close');
        if (!modal || !iframe || !closeBtn) return;

        d.querySelectorAll('[data-video-url]').forEach(el => {
            el.addEventListener('click', () => {
                const url = el.getAttribute('data-video-url');
                iframe.src = `${url}${url.includes('?') ? '&' : '?'}autoplay=1`;
                modal.classList.add('open');
            });
        });

        const closeModal = () => {
            modal.classList.remove('open');
            iframe.src = '';
        };
        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', e => {
            if (e.target === modal) closeModal();
        });
    }

    // Bootstrap modal (لو مستخدم)
    d.querySelectorAll('.play-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const videoUrl = btn.getAttribute('data-video-url');
            const iframe = d.getElementById('videoIframe');
            if (!iframe) return;
            iframe.src = `${videoUrl}${videoUrl.includes('?') ? '&' : '?'}autoplay=1`;
            if (w.bootstrap?.Modal) {
                const modal = new w.bootstrap.Modal(d.getElementById('videoModal'));
                modal.show();
                d.getElementById('videoModal').addEventListener('hidden.bs.modal', () => {
                    iframe.src = '';
                }, {once: true});
            } else {
                d.getElementById('videoModal')?.classList.add('open');
            }
        });
    });

    /* ===================== View toggle (grid/list) ===================== */
    d.querySelectorAll('#viewToggle .toggle-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            d.querySelectorAll('#viewToggle .toggle-btn').forEach(b => b.classList.toggle('active', b === btn));
            const grid = d.querySelector('.saved-grid');
            if (grid) grid.classList.toggle('list-view', btn.dataset.view === 'list');
        });
    });

    /* ===================== Profile dropdown ===================== */
    const profileToggle = d.getElementById('profileToggle');
    const profileMenu = d.getElementById('profileMenu');
    profileToggle?.addEventListener('click', e => {
        e.stopPropagation();
        profileMenu?.classList.toggle('show');
    });
    d.addEventListener('click', () => profileMenu?.classList.remove('show'));

    /* ===================== Notification panel ===================== */
    const notifToggle = d.getElementById('notifToggle');
    const notifPanel = d.getElementById('notifPanel');
    const notifClose = d.getElementById('notifClose');
    notifToggle?.addEventListener('click', () => notifPanel?.classList.add('open'));
    notifClose?.addEventListener('click', () => notifPanel?.classList.remove('open'));
    d.addEventListener('click', (e) => {
        if (notifPanel?.classList.contains('open') && !notifPanel.contains(e.target) && !notifToggle.contains(e.target)) {
            notifPanel.classList.remove('open');
        }
    });

    /* ===================== Boot ===================== */
    whenDOMReady(() => {
        initTicker();
        initMobileNav();
        initQtyControls();
        initVideoModal();

        // انتظر Owl ثم فعّل الكاروسيلات (حتى لو لسه ملف Owl بيتحمّل)
        waitForOwl(initCarousels);

        // أعد المحاولة بعد load تحسبًا لتحميل متأخر
        w.addEventListener('load', () => waitForOwl(initCarousels), {once: true});

        // راقب تغييرات DOM (Ajax/Livewire/Turbo) وأعد التهيئة عند الحاجة
        observeCarousels();

        // تكامل شائع مع Livewire/Turbo/PJAX إن وجد
        d.addEventListener('livewire:load', () => waitForOwl(initCarousels));
        d.addEventListener('livewire:update', () => waitForOwl(initCarousels));
        d.addEventListener('turbo:load', () => waitForOwl(initCarousels));
        d.addEventListener('pjax:end', () => waitForOwl(initCarousels));
        // لو عندك تبّات/أكورديون، اعمل refresh عند الفتح
        d.addEventListener('shown.bs.tab', () => waitForOwl(() => w.jQuery('.owl-carousel').trigger('refresh.owl.carousel')));
        d.addEventListener('shown.bs.collapse', () => waitForOwl(() => w.jQuery('.owl-carousel').trigger('refresh.owl.carousel')));
    });

})(window, document, window.jQuery);
