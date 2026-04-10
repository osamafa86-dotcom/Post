$(document).ready(function () {
    // Initialize the accordion: hide all items except the active one
    $('.fb-accordion-item').not('.fb-active').find('.fb-accordion-content').hide();

// Handle opening the accordion when clicking on the title or the whole item
    $('.fb-accordion .fb-accordion-title').click(function () {
        var $item = $(this).closest('.fb-accordion-item');

        // If the clicked item is already active, do nothing (prevent toggling off)
        if ($item.hasClass('fb-active')) return;

        // Close any currently active item
        $('.fb-accordion .fb-accordion-item.fb-active')
            .removeClass('fb-active')
            .addClass('fb-closed')
            .find('.fb-accordion-content')
            .slideUp(300);

        // Open the clicked item
        $item.removeClass('fb-closed').addClass('fb-active');
        $item.find('.fb-accordion-content').slideDown(300);
    });

// Handle closing the accordion only when clicking on the image inside the content
    $('.fb-accordion .fb-accordion-content .image img').click(function (e) {
        e.stopPropagation(); // Prevent event from bubbling up and triggering parent clicks

        var $item = $(this).closest('.fb-accordion-item');

        // Close the item if it is currently active
        if ($item.hasClass('fb-active')) {
            $item.removeClass('fb-active').addClass('fb-closed');
            $item.find('.fb-accordion-content').slideUp(300);
        }
    });



    document.querySelectorAll('.right-choises li').forEach(li => {
        li.addEventListener('click', () => {
            const idx = li.getAttribute('data-index');

            // 1) تبديل الـ active في التبويبات
            document.querySelectorAll('.right-choises li')
                .forEach(el => el.classList.remove('active'));
            li.classList.add('active');

            // 2) عرض الـ tab المقابلة وإخفاء الباقي
            document.querySelectorAll('.local-news-tab')
                .forEach(tab => tab.classList.toggle(
                    'active',
                    tab.getAttribute('data-index') === idx
                ));
        });
    });

    $(".owl-news").owlCarousel({
        loop: true,
        margin: 20,
        rtl: true,
        nav: true,
        navText: [
            "<i class='fa-solid fa-chevron-right'></i>",
            "<i class='fa-solid fa-chevron-left'></i>",
        ],
        responsive: {
            0: {items: 1},
            600: {items: 2},
            1000: {items: 2},
        },
    });

    // عند النقر على التبويبة
    $(".buttons-group-tabs li").click(function () {
        var tab = $(this).data("tab");

        // تغيير النمط النشط للتبويبات
        $(".buttons-group-tabs li").removeClass("active");
        $(this).addClass("active");

        // إخفاء جميع المحتويات وعرض المحتوى المقابل للتبويب
        $(".tab-pane").hide();
        $("." + tab).show();
    });

    // Initialize sliders with configurations
    const sliderConfigs = [
        {
            id: "#slider1",
            options: {
                loop: true,
                margin: 20,
                rtl: true,
                nav: true,

                navText: [
                    "<i class='fa-solid fa-chevron-right'></i>",
                    "<i class='fa-solid fa-chevron-left'></i>",
                ],
                responsive: {
                    0: {items: 1},
                    600: {items: 2},
                    1000: {items: 3},
                },
            },
        },
        {
            id: "#slider2",
            options: {
                loop: true,
                margin: 20,
                rtl: true,
                nav: true,
                // Disable mouse and touch dragging
                mouseDrag: false,
                touchDrag: false,
                navText: [
                    "<i class='fa-solid fa-chevron-right'></i>",
                    "<i class='fa-solid fa-chevron-left'></i>",
                ],
                responsive: {
                    0: {items: 1},
                    600: {items: 2},
                    1000: {items: 2},
                },
            },
        },
        {
            id: "#slider3",
            options: {
                loop: true,
                rtl: true,
                margin: 20,
                nav: true,
                navText: [
                    "<i class='fa-solid fa-chevron-right'></i>",
                    "<i class='fa-solid fa-chevron-left'></i>",
                ],
                responsive: {
                    0: {items: 1},
                    600: {items: 2},
                    1000: {items: 2},
                },
            },
        },
        {
            id: "#slider4",
            options: {
                loop: true,
                margin: 15,
                rtl: true,
                nav: true,
                dots: false,
                navText: [
                    "<i class='fa-solid fa-chevron-right'></i>",
                    "<i class='fa-solid fa-chevron-left'></i>",
                ],
                responsive: {
                    0: {items: 1},
                    600: {items: 2},
                    1000: {items: 3},
                },
            },
        },
        {
            id: "#safit-slider",
            options: {
                loop: true,

                margin: 20,
                rtl: true,
                nav: true,
                dots: true,
                autoplay: true,
                autoplayTimeout: 3000,
                autoplayHoverPause: true,
                responsive: {0: {items: 1}, 600: {items: 2}, 1000: {items: 3}},
            },
        },
    ];

    sliderConfigs.forEach(({id, options}) => $(id).owlCarousel(options));

    $(".bars-container ").addClass("active");
    $(".bars-container , .cells-container i").click(function () {
        $(".bars-container , .cells-container ").removeClass("active");
        $(this).addClass("active");
    });

    $("#bars-btn, #cells-btn").click(function () {
        const isBars = $(this).is("#bars-btn");
        $(".hide-right-section").toggle(isBars);
        $(".toggle-menu").toggle(!isBars);
        $(".toggle-menu-card").toggle(isBars);
        $(".bars-container ").toggleClass("active", isBars);
        $(".cells-container ").toggleClass("active", !isBars);
    });

    $(".share-arrow, .share h4, .btn-share").click(function (e) {
        e.preventDefault();
        let shareIcons = $(this).siblings(".share-social-icons");

        if (shareIcons.hasClass("show")) {
            shareIcons.removeClass("show");
            setTimeout(() => shareIcons.hide(), 300); // يخفي العنصر بعد الانتقال
        } else {
            shareIcons.show(); // يظهر العنصر قبل إضافة التأثير
            setTimeout(() => shareIcons.addClass("show"), 10);
        }
    });

    $(window).on("scroll", function () {
        $("#scrollToTop").toggleClass("show", $(this).scrollTop() > 800);
    });

    $("#scrollToTop").on("click", function () {
        $("html, body").animate({scrollTop: 0}, "smooth");
    });

    const hideSliders = () => $(".owl-carousel-container").hide();

    // Suppress CORS-related fetch errors from wavesurfer
    window.addEventListener('unhandledrejection', function (event) {
        if (event.reason && event.reason.message && event.reason.message.indexOf('Failed to fetch') !== -1) {
            event.preventDefault();
        }
    });

    // Loop through each audio player container on the page
    if (typeof WaveSurfer !== 'undefined') {
    document.querySelectorAll('.audio-player').forEach((container) => {
        const waveformEl = container.querySelector('.waveform-ph');
        const audioPath = waveformEl ? waveformEl.dataset.audio : null;

        // Skip if no audio path or empty
        if (!audioPath || audioPath === '' || audioPath === 'null' || audioPath === 'undefined') {
            return;
        }

        // Create a WaveSurfer instance using MediaElement backend
        // MediaElement uses <audio> tag which bypasses CORS restrictions for playback
        const wavesurfer = WaveSurfer.create({
            container: waveformEl,
            waveColor: '#E0E0E0',
            progressColor: '#33B3C0',
            height: 20,
            responsive: true,
            backend: 'MediaElement',
        });

        // Handle load errors (CORS, 404, etc.)
        wavesurfer.on('error', function (err) {
            console.warn('WaveSurfer: Could not load audio -', audioPath, err);
        });

        // Load the audio file
        try {
            wavesurfer.load(audioPath);
        } catch (e) {
            console.warn('WaveSurfer: Failed to load audio -', e.message);
            return;
        }

        // Get control buttons and time display element
        const playPauseButton = container.querySelector('.play-pause');
        const rewindButton = container.querySelector('.rewind');
        const forwardButton = container.querySelector('.forward');
        const timeDisplay = container.querySelector('.time-display');

        // Initialize volume level (range: 0.0 to 1.0)
        let currentVolume = 1.0;
        wavesurfer.setVolume(currentVolume);

        // Get the volume slider element and volume icon element
        const volumeSlider = container.querySelector('.volume-slider');
        const volumeIcon = container.querySelector('.volume-icon');

        // Set the slider's initial value and update the background gradient
        volumeSlider.value = currentVolume;
        updateSliderBackground(currentVolume);

        // Function to update the slider background gradient for RTL:
        // The gradient fills from the right (0%) to left (100%).
        function updateSliderBackground(volume) {
            const percentage = volume * 100; // percentage of the active (colored) portion
            // For RTL, the gradient is set to "to left" so that the active color (#00a2b9)
            // fills from the right edge up to the given percentage, and the remainder is gray.
            volumeSlider.style.background = `linear-gradient(to right, #00a2b9 0%, #00a2b9 ${percentage}%, #E0E0E0 ${percentage}%, #E0E0E0 100%)`;
        }

        // Update the volume and slider appearance when the slider value changes
        volumeSlider.addEventListener('input', () => {
            currentVolume = parseFloat(volumeSlider.value);
            wavesurfer.setVolume(currentVolume);
            updateSliderBackground(currentVolume);

            // Update the volume icon: show mute icon if volume is 0, otherwise show volume up icon
            if (currentVolume === 0) {
                volumeIcon.innerHTML = '<i class="fas fa-volume-mute"></i>';
            } else {
                volumeIcon.innerHTML = '<i class="fas fa-volume-up"></i>';
            }
        });

        // Play/Pause toggle with icon update
        playPauseButton.addEventListener('click', () => {
            wavesurfer.playPause();
            playPauseButton.innerHTML = wavesurfer.isPlaying() ? '<i class="fa-solid fa-pause"></i>' : '<i class="fa-solid fa-play"></i>';
        });

        // Rewind 10 seconds
        rewindButton.addEventListener('click', () => {
            const currentTime = wavesurfer.getCurrentTime();
            let newTime = currentTime - 10;
            if (newTime < 0) newTime = 0;
            const duration = wavesurfer.getDuration();
            if (duration > 0) {
                wavesurfer.seekTo(newTime / duration);
            }
        });

        // Forward 10 seconds
        forwardButton.addEventListener('click', () => {
            const currentTime = wavesurfer.getCurrentTime();
            let newTime = currentTime + 10;
            const duration = wavesurfer.getDuration();
            if (newTime > duration) newTime = duration;
            if (duration > 0) {
                wavesurfer.seekTo(newTime / duration);
            }
        });

        // Update the time display during audio playback
        wavesurfer.on('audioprocess', () => {
            const currentTime = wavesurfer.getCurrentTime();
            const duration = wavesurfer.getDuration();
            timeDisplay.textContent = `${formatTime(currentTime)} / ${formatTime(duration)}`;
        });

        // Set the initial time display when the audio is ready
        wavesurfer.on('ready', () => {
            const duration = wavesurfer.getDuration();
            timeDisplay.textContent = `${formatTime(0)} / ${formatTime(duration)}`;
        });
        wavesurfer.on('finish', () => {
            playPauseButton.innerHTML = '<i class="fa-solid fa-play"></i>';
        });
    });

    // Function to format seconds into MM:SS format
    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }
    } // end WaveSurfer check

    // إغلاق قائمة الإشعارات عند النقر في أي مكان خارجها
    $(document).on('click', function () {
        $('.notifications-mega-menu').hide();
    });

    // منع إخفاء القائمة عند النقر داخل القائمة نفسها
    $(document).on('click', '.notifications-mega-menu', function (e) {
        e.stopPropagation();
    });

    // عند النقر على أيقونة الإشعارات
    $('.notifications-mega-menu').closest('.icon-container').on('click', function (e) {
        e.stopPropagation();

        const notificationMenu = $(this).find('.notifications-mega-menu');

        // إخفاء أي قوائم أخرى (Mega Menus) مفتوحة
        $(".top-bar-container .mega-menu").not(notificationMenu).hide();

        // التأكد من وجود إشعارات، وإن لم توجد نضع رسالة "لا يوجد إشعارات"
        const notificationItems = notificationMenu.find('.notification-item');
        if (notificationItems.length === 0) {
            const container = notificationMenu.find('.notifications-container');
            // تأكد أننا لم نضف الرسالة من قبل
            if (!container.find('.no-notifications-msg').length) {
                container.html('<p class="no-notifications-msg">لا يوجد إشعارات</p>');
            }
        }

        // فتح/إغلاق القائمة
        notificationMenu.toggle();
    });
});
