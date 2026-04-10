$(document).ready(function() {

  /*********************************************
   * إغلاق القائمة عند النقر في أي مكان خارجها
   *********************************************/
  $(document).on('click', function() {
    $('.top-bar-container .mega-menu').hide();
  });

  // منع إخفاء القائمة عند النقر داخلها
  $(document).on('click', '.top-bar-container .mega-menu', function(e) {
    e.stopPropagation();
  });

  // منع إعادة التحميل عند النقر على أسهم التمرير في القائمة
  $(document).on('click', '.curancy-mega-menu .owl-nav, .curancy-mega-menu .owl-nav *', function(e) {
    e.stopPropagation();
  });

  /***********************************************************
   * عند النقر على الأيقونة التي تحتوي على .curancy-mega-menu
   ***********************************************************/
  $(".curancy-mega-menu").closest('.icon-container').on('click', function(e) {
    e.stopPropagation();

    const megaMenu = $(this).find('.curancy-mega-menu');

    // إخفاء أي قوائم أخرى مفتوحة
    $(".top-bar-container .mega-menu").not(megaMenu).hide();

    // هل تم التحميل مسبقًا؟
    if (!megaMenu.data('loaded')) {
      // إظهار الـ Loading
      megaMenu.find('.loading-overlay').addClass('active');
      megaMenu.show();

      // جلب بيانات العملات
      fetchCurrencyData(megaMenu)
        .then(() => {
          // عند اكتمال الجلب بنجاح
          megaMenu.data('loaded', true); 
        });
    } else {
      // إذا تم التحميل من قبل، نعرض القائمة فقط
      megaMenu.show();
    }
  });

  /*********************************************
   * دالة لجلب بيانات العملات (تُعيد Promise)
   *********************************************/
  function fetchCurrencyData(menuElement) {
    const apiKey = "48ef6fafed9643d1bfd1855cb6b9bc0f";
    const apiUrl = `https://openexchangerates.org/api/latest.json?app_id=${apiKey}`;

    // إعادة الـ Promise ليتم التعامل معه في .then()
    return fetch(apiUrl)
      .then(response => {
        if (!response.ok) throw new Error("Network response was not ok");
        return response.json();
      })
      .then(data => {
        if (data.rates) {
          updateDateTime(menuElement);
          // تحديث سلايدر الدولار
          updateSlider(".slider-usd", data.rates.USD, data.rates, "USD");
          // تحديث سلايدر اليورو
          updateSlider(".slider-eur", data.rates.EUR, data.rates, "EUR");
        }
      })
      .catch(error => {
        console.error('Failed to fetch currency:', error);
        menuElement.find('.mega-menu-content').prepend(
          '<p class="error">حدث خطأ في تحميل البيانات</p>'
        );
      })
      .finally(() => {
        // إزالة تأثير اللودنج
        menuElement.find('.loading-overlay').removeClass('active');
      });
  }

  /*************************************
   * دالة تحديث السلايدر (Owl Carousel)
   *************************************/
  function updateSlider(sliderSelector, baseRate, rates, baseCurrency) {
    const slider = $(sliderSelector);

    // تدمير أي سلايدر قديم
    slider.owlCarousel('destroy');
    slider.html('');

    // بناء العناصر داخل السلايدر بناءً على العملات المتاحة
    Object.keys(rates).forEach(currency => {
      if (currency !== baseCurrency) {
        const rate = (rates[currency] / baseRate).toFixed(3);
        slider.append(`
          <div class="item">
            <div class="currency">
              <p>${currency}</p>
              <span>${rate} ${currency}</span>
            </div>
          </div>
        `);
      }
    });

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
        0: { items: 3 },
        600: { items: 4 },
        1000: { items: 4 }
      }
    });
  }

  /*********************************************
   * دالة تحديث التاريخ والوقت في الـ Mega Menu
   *********************************************/
  function updateDateTime(menuElement) {
    const now = new Date();
    const options = {
      hour: '2-digit',
      minute: '2-digit',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    };

    const dateTimeString = now.toLocaleDateString("ar-EG", options);
    menuElement.find(".date-slider").text(dateTimeString);
  }
});