document.addEventListener('DOMContentLoaded', function() {
  // Tab functionality
  const tabs = document.querySelectorAll('.tab');
  const imageContainers = document.querySelectorAll('.image-container');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      // Remove active class from all tabs and containers
      tabs.forEach(t => t.classList.remove('active'));
      imageContainers.forEach(container => container.classList.remove('active'));

      // Add active class to clicked tab
      tab.classList.add('active');

      // Show corresponding container
      const tabNumber = tab.getAttribute('data-tab');
      const activeContainer = document.querySelector(`.image-container[data-tab="${tabNumber}"]`);
      if (activeContainer) {
        activeContainer.classList.add('active');
      }
    });
  });

  // Initialize Swiper sliders
  let swiper4, swiper5, swiper6, swiper7;

  function initSwipers() {
    swiper4 = new Swiper('.swiper4', {
      direction: 'horizontal',
      loop: true,
      navigation: {
        nextEl: '.swiper4 .swiper-button-next',
        prevEl: '.swiper4 .swiper-button-prev',
      },
      pagination: {
        el: '.swiper4 .swiper-pagination',
        clickable: true,
      },
    });

    swiper5 = new Swiper('.swiper5', {
      direction: 'horizontal',
      loop: true,
      navigation: {
        nextEl: '.swiper5 .swiper-button-next',
        prevEl: '.swiper5 .swiper-button-prev',
      },
      pagination: {
        el: '.swiper5 .swiper-pagination',
        clickable: true,
      },
    });

    swiper6 = new Swiper('.swiper6', {
      direction: 'horizontal',
      loop: true,
      navigation: {
        nextEl: '.swiper6 .swiper-button-next',
        prevEl: '.swiper6 .swiper-button-prev',
      },
      pagination: {
        el: '.swiper6 .swiper-pagination',
        clickable: true,
      },
    });

    swiper7 = new Swiper('.swiper7', {
      direction: 'horizontal',
      loop: true,
      navigation: {
        nextEl: '.swiper7 .swiper-button-next',
        prevEl: '.swiper7 .swiper-button-prev',
      },
    });
  }

  // Initialize swipers
  initSwipers();

  // Update swipers when tabs are changed
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const tabNumber = tab.getAttribute('data-tab');
      setTimeout(() => {
        if (tabNumber === '4' && swiper4) swiper4.update();
        if (tabNumber === '5' && swiper5) swiper5.update();
        if (tabNumber === '6' && swiper6) swiper6.update();
      }, 100);
    });
  });
}); 