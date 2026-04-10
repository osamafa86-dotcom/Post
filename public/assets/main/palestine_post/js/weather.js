$(document).ready(function() {
  // إغلاق أي قائمة مفتوحة عند النقر في أي مكان خارجها
  $(document).on('click', function() {
    $('.top-bar-container .mega-menu').hide();
  });

  // منع إغلاق القائمة عند النقر داخلها
  $(document).on('click', '.top-bar-container .mega-menu', function(e) {
    e.stopPropagation();
  });

  // منع إعادة التحميل عند النقر على أسهم التمرير (التي ينشئها Owl Carousel)
  $(document).on('click', '.weather-mega-menu .owl-nav, .weather-mega-menu .owl-nav *', function(e) {
    e.stopPropagation();
  });

  // عند النقر على أيقونة الطقس (الأب الذي يحتوي على .weather-mega-menu)
  $(".weather-mega-menu").closest('.icon-container').on('click', function(e) {
    e.stopPropagation();
    
    const megaMenu = $(this).find('.weather-mega-menu');
    
    // إخفاء أي قوائم أخرى مفتوحة
    $(".top-bar-container .mega-menu").not(megaMenu).hide();

    // التحقق إذا كانت البيانات قد تم جلبها مسبقًا
    if (!megaMenu.data('loaded')) {
      // إظهار اللودنج
      megaMenu.find('.loading-overlay').addClass('active');
      megaMenu.show();

      // جلب بيانات الطقس
      fetchWeatherData(megaMenu).then(() => {
        // عند اكتمال جلب البيانات بنجاح
        megaMenu.data('loaded', true); 
      });
    } else {
      // في حال تم التحميل من قبل، فقط أظهر القائمة بدون لودنج
      megaMenu.show();
    }
  });

  // دالة لجلب بيانات الطقس (تُعيد Promise لتسهيل التحكم بالـ loading)
  function fetchWeatherData(menuElement) {
    return new Promise((resolve, reject) => {
      const apiKey = "4e1c311a82984f5686d131632252601";
      const cities = [
        { name: "غزة", query: "Gaza" },
        { name: "القدس", query: "Jerusalem" }
      ];

      // تفريغ أي محتوى سابق في السلايدرات
      menuElement.find('.weather-sliders').html('');

      let requestsCount = 0; // عدّ الطلبات لنعرف متى ننتهي من التحميل

      cities.forEach(city => {
        fetch(`https://api.weatherapi.com/v1/forecast.json?key=${apiKey}&q=${city.query}&days=5&lang=ar`)
          .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
          })
          .then(data => {
            if (!data.forecast) throw new Error('Invalid data structure');
            buildWeatherSlider(menuElement, city, data);
          })
          .catch(error => {
            console.error('Weather fetch error:', error);
            menuElement.find('.weather-sliders').append(`
              <div class="weather-error">
                <p>فشل تحميل بيانات الطقس لـ ${city.name}</p>
              </div>
            `);
          })
          .finally(() => {
            requestsCount++;
            // عندما تنتهي كل الطلبات (لكل المدن)
            if (requestsCount === cities.length) {
              // إزالة اللودنج
              menuElement.find('.loading-overlay').removeClass('active');
              resolve();
            }
          });
      });
    });
  }

  // دالة بناء سلايدر الطقس
  function buildWeatherSlider(menuElement, city, data) {
    const sliderId = `weather-slider-${city.query.replace(/\s+/g, '-')}`;
    const sliderContainer = $(`
      <div class="background-weather">
        <header class="d-flex align-items-center gap-2 justify-content-between mb-2">
          <div class="title-with-circle title-with-circle-small d-flex align-items-center gap-2">
            <div class="dot-title red-dot"></div>
            <h5>${city.name}</h5>
          </div>
          <div class="date">${new Date().toLocaleDateString("ar-EG", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric"
          })}</div>
        </header>
        <div class="owl-carousel weather-slider owl-theme" id="${sliderId}"></div>
      </div>
    `);

    const slider = sliderContainer.find(`#${sliderId}`);

    // إضافة الأيام والتوقعات إلى السلايدر
    data.forecast.forecastday.forEach(day => {
      const date = new Date(day.date);
      slider.append(`
        <div class="item">
          <div class="weather-item">
            <p>${date.toLocaleDateString("ar-EG", { weekday: "long" })} 
              <span>${day.date}</span>
            </p>
            <img src="https:${day.day.condition.icon}" alt="${day.day.condition.text}">
            <span class="description-weather">${day.day.condition.text}</span>
            <span class="temperature">${Math.round(day.day.avgtemp_c)}°C</span>
          </div>
        </div>
      `);
    });

    // ضم السلايدر إلى القسم الخاص بالمدن في الـ mega menu
    menuElement.find('.weather-sliders').append(sliderContainer);

    // تهيئة Owl Carousel
    slider.owlCarousel({
      loop: true,
      margin: 10,
      nav: true,
      rtl: true,
      dots: false,
      navText: [
        "<i class='fa-solid fa-chevron-right'></i>",
        "<i class='fa-solid fa-chevron-left'></i>"
      ],
      responsive: {
        0: { items: 1 },
        600: { items: 1 },
        1000: { items: 1.7 }
      }
    });
  }

});