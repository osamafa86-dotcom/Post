/**
 * =========================================================
 * HERO SLIDER & NEWS SLIDER + PRELOADER
 * =========================================================
 */
$(document).ready(function () {
    /* ===============================
       HERO EDITORIAL SLIDER
       =============================== */
    var $heroSection = $('#hero-section');
    var $heroSlider = $heroSection.find('.hero-slider');
    var totalSlides = $heroSection.find('.story-card').length;
    var autoplayInterval;
    var currentIndex = 0;

    // Initialize Owl Carousel
    $heroSlider.owlCarousel({
        loop: false,
        margin: 0,
        nav: false,
        dots: false,
        autoplay: false, // We'll handle autoplay manually
        mouseDrag: false,
        touchDrag: true,
        responsive: {
            0: { items: 2 },
            576: { items: 3 },
            992: { items: 4 }
        }
    });

    // Custom Navigation
    $heroSection.find('.next').on('click', function () {
        $heroSlider.trigger('next.owl.carousel');
    });

    $heroSection.find('.prev').on('click', function () {
        $heroSlider.trigger('prev.owl.carousel');
    });

    // Update Featured Content & Background
    function updateFeatured($card) {
        var bgUrl = $card.data('bg');
        var title = $card.data('title');
        var excerpt = $card.data('excerpt');
        var date = $card.data('date');
        var link = $card.data('link');

        // Animate background change
        $heroSection.css('background-image', 'url(' + bgUrl + ')');

        // Update featured content with fade effect
        var $featured = $('#featured-display');
        $featured.css('opacity', 0);

        setTimeout(function() {
            $('#featured-title a').text(title).attr('href', link);
            $('#featured-excerpt').text(excerpt);
            $('#featured-date').text(date);
            $featured.css('opacity', 1);
        }, 200);
    }

    // Update Progress Bar
    function updateProgress(index) {
        var progress = ((index + 1) / totalSlides) * 100;
        $('#slider-progress').css('width', progress + '%');
    }

    // Set initial state
    var $firstCard = $heroSlider.find('.story-card').first();
    if ($firstCard.length) {
        updateFeatured($firstCard);
        $firstCard.addClass('active');
        updateProgress(0);
    }

    // Hover interaction on story cards
    $heroSlider.on('mouseenter', '.story-card', function () {
        var $this = $(this);
        $heroSlider.find('.story-card').removeClass('active');
        $this.addClass('active');
        updateFeatured($this);

        // Update progress based on card index
        var index = $heroSlider.find('.story-card').index($this);
        updateProgress(index);

        // Pause autoplay on hover
        clearInterval(autoplayInterval);
    });

    // Resume autoplay on mouse leave
    $heroSlider.on('mouseleave', function () {
        startAutoplay();
    });

    // Autoplay functionality
    function startAutoplay() {
        clearInterval(autoplayInterval);
        autoplayInterval = setInterval(function () {
            currentIndex = (currentIndex + 1) % totalSlides;
            var $cards = $heroSlider.find('.story-card');
            var $nextCard = $cards.eq(currentIndex);

            $cards.removeClass('active');
            $nextCard.addClass('active');
            updateFeatured($nextCard);
            updateProgress(currentIndex);

            // Move carousel if needed
            if (currentIndex % 4 === 0 && currentIndex !== 0) {
                $heroSlider.trigger('next.owl.carousel');
            }
        }, 5000);
    }

    startAutoplay();

    // Pause autoplay when page is not visible
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            clearInterval(autoplayInterval);
        } else {
            startAutoplay();
        }
    });

    /* ===============================
       NEWS SLIDER INITIALIZATION
       =============================== */
    var $newsSlider = $('.news-slider');

    // Initialize each News Slider
    $newsSlider.owlCarousel({
        loop: false,
        margin: 0,
        nav: false,
        dots: false,
        responsive: {
            0: { items: 1 },
            576: { items: 2 },
            992: { items: 3 },
            1200: { items: 4 }
        }
    });

    // Helper function: update range display (e.g. "2 - 6")
    function updateRange($slider, rangeSelector) {
        var info = $slider.data('owl.carousel');
        if (!info) return;
        var currentIndex = info.current();
        var total = info.items().length;
        $(rangeSelector).text((currentIndex + 1) + ' - ' + total);
    }

    // Local navigation for each news-slider section
    $('.news-slider').each(function () {
        var $this = $(this);
        var $section = $this.closest('section');
        var $nextBtn = $section.find('.next');
        var $prevBtn = $section.find('.prev');
        var $range = $section.find('.slider-range');

        $nextBtn.on('click', function () {
            $this.trigger('next.owl.carousel');
        });
        $prevBtn.on('click', function () {
            $this.trigger('prev.owl.carousel');
        });

        $this.on('translated.owl.carousel', function () {
            updateRange($this, $range);
        });

        updateRange($this, $range);
    });


    /* ===============================
       PAGE PRELOADER
       =============================== */
    var $loader = $('#preloadr'),
        isHidden = false;

    function hideLoader() {
        if (isHidden) return;
        isHidden = true;
        $loader.fadeOut(500);
    }

    $(window).on('load', hideLoader);
    setTimeout(hideLoader, 7000); // Fallback auto-hide after 7s
});


/**
 * =========================================================
 * VIDEO PLAYER HANDLER
 * =========================================================
 */
document.addEventListener('DOMContentLoaded', function () {

    /**
     * Create a dynamic video player (YouTube or local video)
     */
    function createPlayer(container, id, type, thumbnail) {
        const iframeContainer = container.querySelector('.video-iframe');
        let player;

        if (type === 'youtube') {
            player = document.createElement('iframe');
            player.setAttribute('src', 'https://www.youtube.com/embed/' + id + '?autoplay=1&rel=0');
            player.setAttribute('allow', 'autoplay; encrypted-media');
            player.setAttribute('frameborder', '0');
            player.style.width = '100%';
            player.style.height = '100%';
        } else if (type === 'local') {
            player = document.createElement('video');
            player.setAttribute('src', id);
            player.setAttribute('controls', '');
            player.setAttribute('autoplay', '');
            if (thumbnail) player.setAttribute('poster', thumbnail);
            player.style.width = '100%';
        }

        // Replace thumbnail with player
        iframeContainer.innerHTML = '';
        iframeContainer.appendChild(player);
        container.querySelector('.video-thumb')?.classList.add('d-none');
        container.querySelector('.play-overlay')?.classList.add('d-none');
        iframeContainer.classList.remove('d-none');
    }

    // Handle video clicks inside all sections
    document.querySelectorAll('section[id^="video-stories"]').forEach(function (section) {
        const mainPlayer = section.querySelector('.video-player');

        if (mainPlayer) {
            mainPlayer.addEventListener('click', function () {
                const id = this.getAttribute('data-video-id');
                const type = this.getAttribute('data-video-type');
                const thumb = this.getAttribute('data-video-thumb');
                createPlayer(this, id, type, thumb);
            });
        }

        section.querySelectorAll('.video-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.getAttribute('data-video-id');
                const type = this.getAttribute('data-video-type');
                const thumb = this.getAttribute('data-video-thumb');
                const container = section.querySelector('.video-player');
                if (container) createPlayer(container, id, type, thumb);
            });
        });
    });
});


/**
 * =========================================================
 * SHARE DROPDOWN MENU
 * =========================================================
 */
document.addEventListener("DOMContentLoaded", function () {
    const shareBtn = document.getElementById("shareBtn");
    const shareMenu = document.getElementById("shareMenu");

    // Toggle share menu on button click
    shareBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        shareMenu.classList.toggle("show");
    });

    // Hide share menu when clicking outside
    document.addEventListener("click", function (e) {
        if (!shareBtn.contains(e.target) && !shareMenu.contains(e.target)) {
            shareMenu.classList.remove("show");
        }
    });
});
