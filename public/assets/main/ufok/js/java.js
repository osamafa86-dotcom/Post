$(document).ready(function () {
  $(".nav-link.dropdown-toggle").on("click", function (e) {
    e.preventDefault(); // Prevent default action
    $(this).next(".dropdown-menu").slideToggle(); // Toggle menu visibility
  });
});

$(document).ready(function () {
  $("#slider1").owlCarousel({
    rtl: true,
    loop: true,
    margin: 5,
    nav: false,
    responsive: {
      0: { items: 1.5 },
      600: { items: 2 },
      1000: { items: 3 },
    },
    autoplay: false,
  });

  $("#slider2").owlCarousel({
    rtl: true,
    loop: true,
    margin: 5,
    nav: false,
    responsive: {
      0: { items: 1.5 },
      600: { items: 2 },
      1000: { items: 3 },
    },

    autoplay: false,
  });
});

$(document).ready(function () {
  // عند النقر على أحد أزرار التبويب
  $(".tab-button").on("click", function () {
    // إزالة الصف `active` من جميع الأزرار
    $(".tab-button").removeClass("active");
    // إضافة الصف `active` إلى الزر الحالي
    $(this).addClass("active");
    // عرض التبويب المقابل (إذا كنت تريد إظهار محتوى بناءً على data-tab)
    var tabId = $(this).data("tab");
    $(".tab-content").hide(); // إخفاء جميع التبويبات
    $("#" + tabId).show(); // عرض التبويب الحالي
  });
});

$(document).ready(function () {
  // Show the first tab slider by default
  $("#slider1").show();

  // Handle tab button click
  $(".tab-button").click(function () {
    var tabId = $(this).data("tab");

    // Remove active class from all tab buttons
    $(".tab-button").removeClass("active");

    // Add active class to clicked tab button
    $(this).addClass("active");

    // Hide all sliders
    $(".owl-carousel").hide();

    // Show the slider corresponding to the clicked tab
    $("#slider" + tabId.split("-")[1]).show();
  });
});

//   start counter

$(document).ready(function () {
  $(".counter").each(function () {
    let $this = $(this),
      target = +$this.data("target"),
      format = $this.data("format"), // الحصول على نوع التنسيق
      current = 0;

    let interval = setInterval(() => {
      current += Math.ceil(target / 90); // زيادة تدريجية

      if (current >= target) {
        clearInterval(interval);
        current = target; // التأكد من الوصول إلى الهدف النهائي
      }

      // تطبيق التنسيق بناءً على نوع البيانات
      if (format === "+K" && current >= 1000) {
        $this.text((current / 1000).toFixed(1) + "K+");
      } else if (format === "%") {
        $this.text(current + "%");
      } else {
        $this.text(current);
      }
    }, 20);
  });
});

//  swiper-slider start

function initSwiper() {
  const isMobile = window.innerWidth <= 768;

  const swiper = new Swiper(".swiper-container", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: isMobile ? 1.1 : "auto", // عرض الشرائح مع تقليل المسافات على الشاشات الصغيرة
    initialSlide: 2,
    coverflowEffect: {
      slideShadows: !isMobile,
      rotate: 0,
      stretch: isMobile ? 20 : 450, // تقليل التمدد لتقريب الشرائح
      depth: isMobile ? 80 : 250, // تقليل العمق لتقريب الشرائح
      modifier: 1,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });

  swiper.on("transitionEnd", () => {
    getSlideContent();
  });
}

function getSlideContent() {
  const ActiveSlide = document.querySelector(".swiper-slide-active");
  console.log(ActiveSlide.innerText);
}

//  swiper-slider end

// استدعاء الدالة عند تحميل الصفحة أو تغيير الحجم
window.addEventListener("load", initSwiper);
window.addEventListener("resize", initSwiper);

// d / h / m / s counter start

$(document).ready(function () {
  const targetDate = Date.now() + 7 * 86400000;

  function updateCountdown() {
    const timeRemaining = targetDate - Date.now();
    if (timeRemaining <= 0)
      return $(".countdown").hide(), $("#finished-message").show();

    const [d, h, m, s] = [
      Math.floor(timeRemaining / 86400000),
      Math.floor((timeRemaining % 86400000) / 3600000),
      Math.floor((timeRemaining % 3600000) / 60000),
      Math.floor((timeRemaining % 60000) / 1000),
    ];

    $("#days, #hours, #minutes, #seconds").each((i, el) =>
      $(el).text([d, h, m, s][i].toString().padStart(2, "0"))
    );
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);
});

// d / h / m / s counter end
$("#fileInput").on("change", function () {
  $(".placeholder").text(this.files[0]?.name || "Choose a file...");
});
