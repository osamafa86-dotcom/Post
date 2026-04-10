$(document).ready(function () {
  var $menu = $('.mobile-menu'),
    $nav = $menu.find('.mobile-menu__nav'),
    $back = $menu.find('.mobile-menu__backdrop'),
    toggle = function () {
      $menu.toggleClass('mobile-menu--open');
      $('body').toggleClass('mobile-menu--open');
      var isOpen = $menu.hasClass('mobile-menu--open');
      $nav.attr('aria-hidden', !isOpen);
    };
  // Open/close on toggle, close, or backdrop click
  $menu.on('click', '.mobile-menu__btn--toggle, .mobile-menu__btn--close, .mobile-menu__backdrop', toggle);


  $(".main-slider").owlCarousel({
    items: 1,              // one slide at a time
    loop: true,            // infinite loop
    rtl: true,             // enable RTL
    nav: true,             // show next/prev buttons
    dots: false,           // hide pagination dots
    autoplay: true,        // auto-play slides
    autoplayTimeout: 5000, // time between slides
    autoplayHoverPause: true, // pause on hover
    navText: [
      '<i class="fas fa-chevron-left"></i>',
      '<i class="fas fa-chevron-right"></i>'
    ]
  });

  $('.authors-slider').owlCarousel({
    items: 4,            // three cards per slide on desktop
    margin: 24,          // gap between cards (uses var spacing indirectly)
    loop: true,
    rtl: true,
    nav: true,
    dots: false,
    autoplay: true,
    autoplayTimeout: 5000,
    responsive: {
      0: { items: 1 },
      576: { items: 2 },
      992: { items: 5 }
    },
    navText: [
      '<i class="fas fa-chevron-left"></i>',
      '<i class="fas fa-chevron-right"></i>'
    ]
  });

  // Professional comment: Hide the loader when the full page (including images) has loaded
  $(window).on('load', function () {
    $('#preloadr').fadeOut(500);
  });

  // Professional comment: Fallback in case 'load' never fires (e.g. on some mobile browsers)
  setTimeout(function () {
    if ($('#preloadr').is(':visible')) {
      $('#preloadr').fadeOut(500);
    }
  }, 2000);

  // Professional comment: Also allow tapping the loader itself to dismiss it manually
  $('#preloadr').on('click', function () {
    $(this).fadeOut(300);
  });


});
