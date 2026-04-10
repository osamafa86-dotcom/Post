!function (e) {
    e(window);
    var t = e(document), n = e("body"), a = e(".navbar"), i = e(".custom-navbar"), o = 0, l = !1;

    function s() {
        o = window.scrollY || window.pageYOffset || 0, l || (requestAnimationFrame(function () {
            var e = o > 50;
            a.length && a.toggleClass("fixed-nav", e), i.length && i.toggleClass("fixed-nav", e), l = !1
        }), l = !0)
    }

    window.addEventListener("scroll", s, {passive: !0}), s();
    var r = e(".search-toggle"), c = e(".search-input-container");
    r.length && c.length && r.on("click", function (e) {
        if (e.preventDefault(), c.toggleClass("active"), c.hasClass("active")) {
            var t = c.find("input,textarea").get(0);
            t && t.focus()
        }
    });
    var u = e(".articlesSlider"), v = e(".owl-about-us"), d = u.find(".owl-lazy").length > 0,
        $ = v.find(".owl-lazy").length > 0;
    u.length && u.owlCarousel({
        loop: !0,
        rtl: !0,
        margin: 15,
        nav: !1,
        dots: !0,
        autoplay: !0,
        autoplayTimeout: 7e3,
        smartSpeed: 500,
        autoplayHoverPause: !0,
        animateOut: "fadeOut",
        animateIn: "fadeIn",
        items: 1,
        lazyLoad: d,
        lazyLoadEager: d ? 1 : 0,
        responsiveRefreshRate: 120
    }), v.length && v.owlCarousel({
        loop: !0,
        margin: 20,
        nav: !1,
        dots: !0,
        rtl: !0,
        autoplay: !0,
        autoplayTimeout: 3e3,
        smartSpeed: 400,
        responsive: {0: {items: 1}, 600: {items: 3}, 1e3: {items: 4}},
        lazyLoad: $,
        lazyLoadEager: $ ? 1 : 0,
        responsiveRefreshRate: 120
    }), document.addEventListener("visibilitychange", function () {
        if (e.fn.owlCarousel && e.fn.owlCarousel.Constructor) {
            var t = document.hidden;
            [u, v].forEach(function (e) {
                if (e && e.length) try {
                    t ? e.trigger("stop.owl.autoplay") : e.trigger("play.owl.autoplay")
                } catch (n) {
                }
            })
        }
    }), function e() {
        var t = document.querySelector(".news-ticker");
        if (t) {
            var n = t.querySelector(".news-list"), a = t.querySelector(".news-track");
            if (n && a) {
                a.style.direction = "ltr", n.innerHTML += n.innerHTML;
                var i = n.scrollWidth / 2, o = -i, l = null;
                window.matchMedia("(prefers-reduced-motion: reduce)").matches || (r(), t.addEventListener("mouseenter", c), t.addEventListener("mouseleave", r), document.addEventListener("visibilitychange", function () {
                    document.hidden ? c() : r()
                }))
            }
        }

        function s() {
            (o += .8) >= 0 && (o = -i), n.style.transform = "translateX(" + o + "px)", l = requestAnimationFrame(s)
        }

        function r() {
            l || (l = requestAnimationFrame(s))
        }

        function c() {
            l && (cancelAnimationFrame(l), l = null)
        }
    }();
    var m = e("#preloadr");
    if (m.length) {
        var f = !1;

        function g() {
            f || (f = !0, m.fadeOut(400))
        }

        window.addEventListener("load", g, {once: !0}), setTimeout(g, 3e3), m.on("click", g)
    }
    e(".mobile-menu-toggle").on("click", function () {
        e(".navbar-links-mobile").addClass("active"), n.addClass("mobile-menu-open")
    }), e(".mobile-close").on("click", function () {
        e(".navbar-links-mobile").removeClass("active"), n.removeClass("mobile-menu-open")
    }), t.on("click", function (t) {
        e(t.target).closest(".navbar-links-mobile, .mobile-menu-toggle").length || (e(".navbar-links-mobile").removeClass("active"), n.removeClass("mobile-menu-open"))
    }), t.on("keydown", function (t) {
        "Escape" === t.key && (e(".navbar-links-mobile").removeClass("active"), n.removeClass("mobile-menu-open"))
    });

    try {
        jQuery.event.special.touchstart = {
            setup: function (e, t, n) {
                this.addEventListener("touchstart", n, {passive: !0})
            }
        }, jQuery.event.special.touchmove = {
            setup: function (e, t, n) {
                this.addEventListener("touchmove", n, {passive: !0})
            }
        }, jQuery.event.special.wheel = {
            setup: function (e, t, n) {
                this.addEventListener("wheel", n, {passive: !0})
            }
        }
    } catch (h) {
    }
    try {
        var p = window.innerHeight || document.documentElement.clientHeight || 800;
        document.querySelectorAll("img:not([loading])").forEach(function (e) {
            e.getBoundingClientRect().top > .8 * p ? e.setAttribute("loading", "lazy") : (e.setAttribute("loading", "eager"), e.setAttribute("fetchpriority", "high")), e.setAttribute("decoding", "async")
        })
    } catch (y) {
    }
}(jQuery);
