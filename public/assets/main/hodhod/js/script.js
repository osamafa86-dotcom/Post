/* Main Script for Elmohajer News */

$(document).ready(function() {
    // ======== Infographic Slider ========
    // Initialize Owl Carousel for infographic with RTL support
    $('.infographic-slider').owlCarousel({
        rtl: true,               // Enable right-to-left mode
        items: 1,                // One slide at a time
        loop: true,              // Infinite loop
        margin: 16,              // Space between slides in px
        dots: true,              // Show pagination dots
        nav: false,              // Hide navigation arrows
        autoplay: true,          // Enable auto-play
        autoplayTimeout: 5000,   // 5 seconds per slide
        autoplayHoverPause: true,// Pause on hover
        animateOut: 'fadeOut'    // Fade transition
    });

    // ======== Caricature Slider ========
    // Initialize Owl Carousel for caricatures with RTL support
    $('.caricature-slider').owlCarousel({
        rtl: true,
        items: 1,
        loop: true,
        margin: 16,
        nav: true,
        navText: [
            '<i class="fas fa-chevron-left"></i>',
            '<i class="fas fa-chevron-right"></i>'
        ],
        dots: false,
        autoplay: true,
        autoplayTimeout: 6000,
        autoplayHoverPause: true,
        animateOut: 'fadeOut'
    });

    // ======== Situation Carousel ========
    // Initialize Owl Carousel for situation wrapper with RTL support
    $('.situation-carousel').owlCarousel({
        rtl: true,
        loop: true,
        margin: 20,
        nav: true,
        dots: false,
        navText: [
            '<i class="fas fa-chevron-right"></i>',
            '<i class="fas fa-chevron-left"></i>'
        ],
        responsive: {
            0:   { items: 1 },
            576: { items: 2 },
            992: { items: 3 }
        }
    });

    // ======== News Ticker ========
    //     // Continuous scrolling of breaking news items to the right with hover pause
    const ticker = document.querySelector('.news-ticker');
    if (ticker) {
        const track = ticker.querySelector('.news-list');
        const trackContainer = ticker.querySelector('.news-track');
        // Force LTR for transform consistency on RTL page
        if (trackContainer) trackContainer.style.direction = 'ltr';
        // Duplicate items for seamless loop
        track.innerHTML += track.innerHTML;
        const width = track.scrollWidth / 2;
        let pos = -width;
        const speed = 0.8;         // Scroll speed (px per frame)
        let rafId;

        function scrollNews() {
            pos += speed;            // Move right
            if (pos >= 0) pos = -width;  // Reset position seamlessly
            track.style.transform = `translateX(${pos}px)`;
            rafId = requestAnimationFrame(scrollNews);
        }
        // Start scrolling
        scrollNews();
        // Pause on hover
        ticker.addEventListener('mouseenter', () => cancelAnimationFrame(rafId));
        ticker.addEventListener('mouseleave', () => rafId = requestAnimationFrame(scrollNews));
    }

    // ======== Preloader ========
    // Hide loader when full page has loaded
    $(window).on('load', function() {
        $('#preloadr').fadeOut(500);
    });

    // Fallback: hide loader after max wait time
    setTimeout(function() {
        if ($('#preloadr').is(':visible')) {
            $('#preloadr').fadeOut(500);
        }
    }, 3000); // 3 seconds max wait

    // Allow manual dismissal of loader on click
    $('#preloadr').on('click', function() {
        $(this).fadeOut(300);
    });

    (function ($) {
        // حاله صغيرة لمنع spam أثناء الأنيميشن
        let isAnimating = false;

        function closeAll() {
            $("#mainNav .dropdown.open").each(function () {
                const $p = $(this);
                $p.removeClass("open")
                    .find("> .dropdown-toggle").removeClass("is-open");
                $p.find("> .dropdown-menu").stop(true, true).slideUp(200);
            });
        }

        // فك أي بايندينج قديم بالـ namespace قبل الربط
        $(document)
            .off("click.hodhodOutside")
            .on("click.hodhodOutside", function (e) {
                // اقفل لو الضغط خارج الدروبداون
                if ($(e.target).closest("#mainNav .dropdown").length === 0) {
                    closeAll();
                }
            });

        // اربط مرة واحدة بالـ namespace على الناف
        $("#mainNav")
            .off("click.hodhodToggle", ".dropdown-toggle")
            .on("click.hodhodToggle", ".dropdown-toggle", function (e) {
                e.preventDefault();
                e.stopPropagation(); // مهم لتفادي أي ليستنر تاني فوق

                if (isAnimating) return;

                const $toggle = $(this);
                const $parent = $toggle.closest(".dropdown");
                const $menu   = $parent.find("> .dropdown-menu");

                isAnimating = true;

                if ($parent.hasClass("open")) {
                    // إغلاق الحالي
                    $menu.stop(true, true).slideUp(200, function () {
                        $parent.removeClass("open");
                        $toggle.removeClass("is-open");
                        isAnimating = false;
                    });
                } else {
                    // إغلاق أي مفتوح أولاً
                    $("#mainNav .dropdown.open").each(function () {
                        const $p = $(this);
                        $p.removeClass("open")
                            .find("> .dropdown-toggle").removeClass("is-open");
                        $p.find("> .dropdown-menu").stop(true, true).slideUp(200);
                    });

                    // فتح الحالي
                    $parent.addClass("open");
                    $toggle.addClass("is-open");
                    $menu.stop(true, true).hide().slideDown(200, function () {
                        isAnimating = false;
                    });
                }
            });

    })(jQuery);

    (function(){
        function wireFakePlaceholder(group){
            const input = group.querySelector('input[type="date"]');
            const update = () => {
                // لو فيه قيمة، اخفي الـ placeholder (حتى بدون فوكس)
                group.classList.toggle('has-value', !!input.value);
                if (input.value) {
                    group.querySelector('.fake-placeholder')?.style.setProperty('opacity', '0');
                    group.querySelector('.fake-placeholder')?.style.setProperty('visibility', 'hidden');
                } else {
                    group.querySelector('.fake-placeholder')?.style.removeProperty('opacity');
                    group.querySelector('.fake-placeholder')?.style.removeProperty('visibility');
                }
            };
            input.addEventListener('change', update);
            input.addEventListener('input', update);
            update(); // حالة ابتدائية (في حال في قيمة جايه من Livewire)
        }

        document.querySelectorAll('.date-field').forEach(wireFakePlaceholder);

        // خَلّي الأيقونة تفتح الـ picker من أول ضغطة (من غير تغيير النوع)
        document.querySelectorAll('.date-field .input-group-text').forEach(function(icon){
            icon.addEventListener('click', function(e){
                const input = this.parentElement.querySelector('input[type="date"]');
                if (!input) return;
                // Chrome/Edge/Android:
                if (typeof input.showPicker === 'function') { input.showPicker(); }
                else { input.focus(); setTimeout(() => input.click(), 0); } // iOS fallback
            });
        });
    })();

    // ======== Video Section Tabs ========
    // Handle tab switching for video section
    document.querySelectorAll('.tab-item').forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
            // Remove active class from all tab panes
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show corresponding tab pane
            const tabId = this.getAttribute('data-tab');
            const targetPane = document.getElementById(tabId);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

});
