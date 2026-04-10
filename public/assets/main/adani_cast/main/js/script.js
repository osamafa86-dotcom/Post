function initializeCustomScripts() {
    console.log('Reinitializing custom scripts...');

    // Pause ticker animation on hover
    $(".ticker ul").hover(
        function () {
            $(this).css("animation-play-state", "paused");
        },
        function () {
            $(this).css("animation-play-state", "running");
        }
    );

    // Toggle theme
    $("#toggleTheme").off("click").on("click", function () {
        $("body").toggleClass("dark-theme");
    });

    // Owl Carousel initialization for #videos-carousel
    $("#videos-carousel").owlCarousel("destroy").owlCarousel({
        loop: true,
        nav: true,
        rtl: true,
        dots: false,
        navText: [
            '<i class="fas fa-chevron-right"></i>',
            '<i class="fas fa-chevron-left"></i>',
        ],
        autoplay: true, // Auto-play videos
        autoplayTimeout: 5000, // Transition time (5 seconds)
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 1,
            },
            600: {
                items: 1,
            },
            1000: {
                items: 1,
            },
        },
    });
}

// Attach initialization logic to Livewire events
document.addEventListener('DOMContentLoaded', initializeCustomScripts);
document.addEventListener('livewire:navigated', initializeCustomScripts);
