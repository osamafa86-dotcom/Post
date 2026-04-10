function initializePageScripts() {
    "use strict";

    // Preloader
    $(window).on("load", function () {
        $(".preloader").fadeOut("slow");
    });

    // Multi-level dropdown menu
    $(".dropdown-menu a.dropdown-toggle").on("click", function (e) {
        if (!$(this).next().hasClass("show")) {
            $(this)
                .parents(".dropdown-menu")
                .first()
                .find(".show")
                .removeClass("show");
        }
        var $subMenu = $(this).next(".dropdown-menu");
        $subMenu.toggleClass("show");

        $(this)
            .parents("li.nav-item.dropdown.show")
            .on("hidden.bs.dropdown", function (e) {
                $(".dropdown-submenu .show").removeClass("show");
            });
        return false;
    });

    // Header Search
    if ($(".search-box-outer").length) {
        $(".search-box-outer").off("click").on("click", function () {
            $("body").addClass("search-active");
        });
        $(".close-search").off("click").on("click", function () {
            $("body").removeClass("search-active");
        });
    }

    // Data-background
    $("[data-background]").each(function () {
        $(this).css(
            "background-image",
            "url(" + $(this).attr("data-background") + ")"
        );
    });

    // Sidebar popup
    $(".sidebar-btn").off("click").on("click", function () {
        $(".sidebar-popup").addClass("open");
        $(".sidebar-wrapper").addClass("open");
    });
    $(".close-sidebar-popup, .sidebar-popup").off("click").on("click", function () {
        $(".sidebar-popup").removeClass("open");
        $(".sidebar-wrapper").removeClass("open");
    });

    // WOW.js initialization
    new WOW().init();

    // Hero slider
    $(".hero-slider").owlCarousel({
        loop: true,
        nav: true,
        rtl: true,
        dots: true,
        margin: 0,
        autoplay: true,
        autoplayHoverPause: true,
        autoplayTimeout: 5000,
        items: 1,
        navText: [
            "<i class='fal fa-angle-left'></i>",
            "<i class='fal fa-angle-right'></i>",
        ],
        onInitialized: function (event) {
            var $firstAnimatingElements = $(".owl-item")
                .eq(event.item.index)
                .find("[data-animation]");
            doAnimations($firstAnimatingElements);
        },
        onChanged: function (event) {
            var $firstAnimatingElements = $(".owl-item")
                .eq(event.item.index)
                .find("[data-animation]");
            doAnimations($firstAnimatingElements);
        },
    });

    function doAnimations(elements) {
        var animationEndEvents =
            "webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend";
        elements.each(function () {
            var $this = $(this);
            var $animationDelay = $this.data("delay");
            var $animationDuration = $this.data("duration");
            var $animationType = "animated " + $this.data("animation");
            $this.css({
                "animation-delay": $animationDelay,
                "-webkit-animation-delay": $animationDelay,
                "animation-duration": $animationDuration,
                "-webkit-animation-duration": $animationDuration,
            });
            $this.addClass($animationType).one(animationEndEvents, function () {
                $this.removeClass($animationType);
            });
        });
    }
    /**
     * Preloader controller
     * - Hide ASAP when the page finishes loading
     * - Hard cap: hide after 3000ms even if something is still loading
     * - Also hides when coming back via bfcache (back/forward)
     */
    (function () {
        var pre = document.querySelector('.preloader');
        if (!pre) return;

        var MAX_MS = 3000;          // hard cap: 3s
        var hidden = false;

        function hidePreloader() {
            if (hidden) return;
            hidden = true;
            pre.classList.add('is-hidden');             // fade out
            // Remove from DOM after the fade to free memory
            setTimeout(function () {
                if (pre && pre.parentNode) pre.parentNode.removeChild(pre);
                pre = null;
            }, 400); // keep a bit > CSS transition (.35s)
        }

        // Hide as soon as the page is fully loaded
        window.addEventListener('load', hidePreloader, { once: true });

        // Hard timeout: hide even if 'load' doesn't fire quickly
        setTimeout(hidePreloader, MAX_MS);

        // If we return via back/forward cache, ensure it's hidden immediately
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) hidePreloader();
        });

        // (Optional) If you use Livewire navigate or SPA transitions:
        // window.addEventListener('livewire:navigated', hidePreloader);
    })();

    // Other Owl Carousel sliders
    // $(".show-slider, .testimonial-slider, .instagram-slider, .partner-slider").each(function () {
    //     $(this).owlCarousel("destroy").owlCarousel({
    //         loop: true,
    //         margin: $(this).data("margin") || 10,
    //         nav: $(this).data("nav") || false,
    //         rtl: true,
    //         dots: $(this).data("dots") || false,
    //         autoplay: $(this).data("autoplay") || true,
    //         responsive: {
    //             0: {
    //                 items: 1,
    //             },
    //             600: {
    //                 items: 2,
    //             },
    //             1000: {
    //                 items: 3,
    //             },
    //         },
    //     });
    // });

    // Magnific popup
    $(".popup-gallery").magnificPopup({
        delegate: ".popup-img",
        type: "image",
        gallery: {
            enabled: true,
        },
    });

    $(".popup-youtube, .popup-vimeo, .popup-gmaps").magnificPopup({
        type: "iframe",
        mainClass: "mfp-fade",
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false,
    });

    // Scroll to top
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $(".scroll-top").addClass("active");
        } else {
            $(".scroll-top").removeClass("active");
        }
    });

    $(".scroll-top").off("click").on("click", function () {
        $("html, body").animate({ scrollTop: 0 }, 1500);
        return false;
    });

    // Countdown
    $("[data-countdown]").each(function () {
        let finalDate = $(this).data("countdown");
        $(this).countdown(finalDate, function (event) {
            $(this).html(
                event.strftime(
                    '<span>%-D</span>:<span>%H</span>:<span>%M</span>:<span>%S</span>'
                )
            );
        });
    });
}
document.addEventListener('DOMContentLoaded', initializePageScripts);
document.addEventListener('livewire:navigated', initializePageScripts);
