$(document).ready(function () {
  // Fixed Navbar: Toggle 'fixed-nav' class when scrolling past 50px
  $(window).on("scroll", function () {
    $(".navbar").toggleClass("fixed-nav", $(window).scrollTop() > 50);
  });

  // Counter Animation: Animate each .stat-number from 0 to its data-count value
  $('.stat-number').each(function () {
    var $this = $(this);
    var targetCount = parseInt($this.attr('data-count'), 10);
    $({ count: 0 }).animate(
      { count: targetCount },
      {
        duration: 2000,
        easing: 'swing',
        step: function () {
          $this.text(Math.floor(this.count));
        },
        complete: function () {
          $this.text(this.count);
        }
      }
    );
  });

  // Helper function to initialize an Owl Carousel slider
  function initCarousel(selector, options) {
    $(selector).owlCarousel(options);
  }

  // Initialize Owl Carousel for various sections

  // Suggestions Slider
  initCarousel('.owl-suggestions', {
    loop: true,
    margin: 20,
    nav: true,
    rtl: true,
    navText: [
      '<i class="fas fa-chevron-right"></i>', // Right arrow icon
      '<i class="fas fa-chevron-left"></i>'   // Left arrow icon
    ],
    responsive: {
      0: { items: 1 },
      600: { items: 2 },
      1000: { items: 3 }
    }
  });

  // Blog Slider
  initCarousel('.owl-blog', {
    loop: true,
    rtl: true,
    margin: 20,
    nav: true,
    dots: true,
    responsive: {
      0: { items: 1 },
      600: { items: 2 },
      1000: { items: 3 }
    }
  });

  // Events Slider
  initCarousel('.owl-events', {
    loop: true,
    rtl: true,
    margin: 20,
    nav: true,
    dots: true,
    responsive: {
      0: { items: 1 },
      600: { items: 2 },
      1000: { items: 3 }
    }
  });

  // Partners Slider
  initCarousel('.partners-slider', {
    rtl: true,
    loop: true,
    margin: 20,
    nav: false,
    dots: true,
    responsive: {
      0: { items: 2 },
      600: { items: 3 },
      1000: { items: 5 }
    }
  });

  // Testimonials Slider
  initCarousel('.owl-testimonials', {
    loop: true,
    rtl: true,
    margin: 20,
    nav: false,
    dots: true,
    responsive: {
      0: { items: 1 },
      600: { items: 2 },
      1000: { items: 3 }
    }
  });

  // Team Slider
  initCarousel('.owl-team', {
    loop: true,
    margin: 0,
    dots: true,
    rtl: true,
    responsive: {
      0: { items: 1 },
      600: { items: 2 },
      1000: { items: 4 }
    }
  });
});


   // Initialize Tagify for Equipment Input
   document.addEventListener("DOMContentLoaded", function () {
    // Initialize Flatpickr for booking date with Arabic locale
    flatpickr("#bookingDate", {
      dateFormat: "d/m/Y",
      locale: "ar"
    });
  });
  document.addEventListener("DOMContentLoaded", function () {
    var input = document.getElementById('requiredEquipment');
    new Tagify(input, {
      whitelist: ["كاميرا", "ميكروفون", "سماعات", "فلتر بوب", "ذراع بوم", "مكبرات صوت"],
      dropdown: {
        enabled: 0,
        maxItems: 5
      }
    });
 
  });