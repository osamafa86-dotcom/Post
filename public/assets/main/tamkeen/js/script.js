$(document).ready(function () {
  $(window).on("scroll", function () {
    $(".navbar").toggleClass("fixed-nav", $(window).scrollTop() > 50);
  });

  var commonOptions = {
    rtl: true,
    loop: true,
    nav: true,
    dots: false,
    navText: [
      '<i class="fa-solid fa-chevron-right"></i>',
      '<i class="fa-solid fa-chevron-left"></i>',
    ]
  };

  $(".studies-carousel").owlCarousel($.extend({}, commonOptions, {
    margin: 0,
    responsive: {
      0: { stagePadding: 50, items: 1 },
      576: { items: 2 },
      992: { items: 3 }
    }
  }));

  $(".research-carousel").owlCarousel($.extend({}, commonOptions, {
    margin: 20,
    responsive: {
      0: { stagePadding: 50, items: 1 },
      768: { items: 2 },
      992: { items: 3 }
    }
  }));

  $(".reports-carousel").owlCarousel($.extend({}, commonOptions, {
    margin: 20,
    responsive: {
      0: { stagePadding: 50, items: 1 },
      992: { items: 1 }
    }
  }));

  $(".courses-carousel").owlCarousel($.extend({}, commonOptions, {
    margin: 20,
    responsive: {
      0: { stagePadding: 50, items: 1 },
      576: { items: 2 },
      768: { items: 3 },
      992: { items: 4 }
    }
  }));


  $(".owl-carousel2").owlCarousel($.extend({}, commonOptions, {
    margin: 20,
    responsive: {
      0: { stagePadding: 50, items: 1 },
      576: { items: 1 },
      768: { items: 1 },
      992: { items: 2 }
    }
  }));

  $(".team-slider").owlCarousel($.extend({}, commonOptions, {
    margin: 10,
    responsive: {
      0: { stagePadding: 50, items: 1 },
      600: { items: 4 },
      1000: { items: 6 }
    }

    
  }));
});
