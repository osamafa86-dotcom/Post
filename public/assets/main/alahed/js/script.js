$(document).ready(function () {
  $(".owl-carousel").owlCarousel({
    loop: true,
    margin: 20,
    nav: true,
    rtl: true,
    dots: true,
    stagePadding:10,
    responsive: {
      0: {
        items: 1,
      },
      768: {
        items: 2,
      },
      1200: {
        items: 4,
      },
    },
    navText: [
      '<i class="fas fa-chevron-right"></i>',
      '<i class="fas fa-chevron-left"></i>',
    ],
  });
});
document.addEventListener("DOMContentLoaded", function () {
  flatpickr(".date-picker", {
    locale: "ar", // اللغة العربية
    dateFormat: "Y-m-d", // تنسيق التاريخ (YYYY-MM-DD)
    disableMobile: "true", // يعطل التقويم الافتراضي على الأجهزة المحمولة
  });
});
document.addEventListener("DOMContentLoaded", () => {
  const counters = document.querySelectorAll(".stat-number");
  const speed = 200; // السرعة (كلما كان أقل، كان العداد أسرع)
  counters.forEach((counter) => {
    const updateCount = () => {
      const target = +counter.getAttribute("data-count");
      const count = +counter.innerText;
      const increment = target / speed;
      if (count < target) {
        counter.innerText = Math.ceil(count + increment);
        setTimeout(updateCount, 10);
      } else {
        counter.innerText = target;
      }
    };
    updateCount();
  });
});



document.addEventListener('DOMContentLoaded', function () {
  const labels = document.querySelectorAll('.report-form .form-label');

  labels.forEach(label => {
    const inputId = label.getAttribute('for');
    const input = document.getElementById(inputId);

    if (input && input.hasAttribute('required')) {
      const star = document.createElement('span');
      star.textContent = " *";
      star.style.color = "#F34947";
      label.appendChild(star);
    }
  });
});


function loadArticleContent(title, content) {
  event.preventDefault();

  document.getElementById('articleModalLabel').textContent = title;
  document.getElementById('articleModalContent').textContent = content;
}
