$(document).ready(function () {
  var navbar = $(".custom-navbar"); // تحديد العنصر
  var scrollOffset = 100; // المسافة التي يبدأ عندها إضافة الكلاس (يمكن تعديلها)

  // استمع لحدث التمرير (scroll)
  $(window).on("scroll", function () {
    if ($(window).scrollTop() > scrollOffset) {
      navbar.addClass("fixed-navbar");
    } else {
      navbar.removeClass("fixed-navbar");
    }
  });
  // عند الضغط على رابط التبويب
  $(".nav-tabs .nav-link").on("click", function (e) {
    e.preventDefault();

    // إزالة الكلاس 'active' من جميع التبويبات
    $(".nav-tabs .nav-link").removeClass("active");
    $(".tab-content .tab-pane").removeClass(
      "active animated fadeInRight fadeInLeft"
    );

    // إضافة الكلاس 'active' إلى التبويب الذي تم الضغط عليه
    $(this).addClass("active");

    // تحديد معرف القسم المرتبط بالتبويب
    var target = $(this).attr("href");

    // عرض القسم المرتبط بالتبويب وإضافة التأثير المناسب
    $(target).addClass("active animated fadeInRight");
  });

    $(function(){
        const $btn = $('#back-to-top').hide();
        $(window).on('scroll', () => $btn.toggle($(this).scrollTop() > 300));
        $btn.on('click', e => {
            e.preventDefault();
            $('html, body').animate({ scrollTop: 0 }, 300);
        });
    });
  //Utf Latest News Slide
  $(".utf_latest_news_slide").owlCarousel({
    loop: true,
    animateIn: "fadeInLeft",
    autoplay: false,
    autoplayHoverPause: true,
    rtl: true,
    nav: true,
    margin: 30,
    dots: false,
    mouseDrag: false,
    slideSpeed: 500,
    navText: [
      "<i class='fa fa-angle-right'></i>",
      "<i class='fa fa-angle-left'></i>",
    ],
    items: 3,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 2,
      },
      768: {
        items: 2,
      },
      992: {
        items: 3,
      },
      1200: {
        items: 4,
      },
    },
  });
  //Utf Post Slide
  $(".utf_post_slide").owlCarousel({
    loop: true,
    animateOut: "fadeOut",
    autoplay: false,
    autoplayHoverPause: true,
    nav: true,
    rtl: true,
    margin: 30,
    dots: false,
    mouseDrag: false,
    slideSpeed: 500,
    navText: [
      "<i class='fa fa-angle-right'></i>",
      "<i class='fa fa-angle-left'></i>",
    ],
    items: 1,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 1,
      },
    },
  });

  //Utf Latest More News Slide
  $(".utf_more_news_slide").owlCarousel({
    loop: true,
    autoplay: false,
    autoplayHoverPause: true,
    rtl: true,
    nav: false,
    margin: 30,
    dots: true,
    mouseDrag: false,
    slideSpeed: 500,
    navText: [
      "<i class='fa fa-angle-left'></i>",
      "<i class='fa fa-angle-right'></i>",
    ],
    items: 1,
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 1,
      },
    },
  });
  /*----------------------------------------------------*/
  // player.
  /*----------------------------------------------------*/

  $(".audio1 audio").mediaelementplayer({
    features: [
      "playlistfeature",
      "prevtrack",
      "playpause",
      "nexttrack",
      "progress",
      "current",
      "volume",
    ],
  });

  $(".audio2 audio").mediaelementplayer({
    features: ["playpause", "progress"],
  });

  $(".audio1 audio").mediaelementplayer({
    features: [
      "playlistfeature",
      "prevtrack",
      "playpause",
      "nexttrack",
      "progress",
      "current",
      "volume",
    ],
    success: function (media, player) {
      // التحقق من زر "السابق"
      if ($(".mejs-prevtrack-button").length === 0) {
        console.warn('زر "السابق" غير موجود!');
      }

      // التحقق من زر "التالي"
      if ($(".mejs-nexttrack-button").length === 0) {
        console.warn('زر "التالي" غير موجود!');
      }
    },
  });
  $(".audio1 audio").mediaelementplayer({
    features: ["playlistfeature", "playpause", "progress", "current", "volume"],
    success: function (media, player) {
      var controls = player.controls;

      // إضافة زر السابق
      $(
        '<div class="mejs-button mejs-prevtrack-button mejs-cust1-button"></div>'
      )
        .appendTo(controls)
        .on("click", function () {
          console.log("تم الضغط على زر السابق!");
        });

      // إضافة زر التالي
      $(
        '<div class="mejs-button mejs-nexttrack-button mejs-cust2-button"></div>'
      )
        .appendTo(controls)
        .on("click", function () {
          console.log("تم الضغط على زر التالي!");
        });
    },
  });
});
