/**
 * Main JavaScript for FiMED News Theme
 * المؤسسة الفلسطينية للإعلام
 */

document.addEventListener('DOMContentLoaded', () => {
    // ========================================
    // 🔝 Navigation Scroll Effect
    // ========================================
    const nav = document.getElementById('mainNav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 200);
    });

    // ========================================
    // 📱 Mobile Navigation
    // ========================================
    const navToggle = document.getElementById('navToggle');
    const navList = document.querySelector('.nav__list');
    const navOverlay = document.querySelector('.nav__overlay');
    const body = document.body;

    // Create overlay if it doesn't exist
    let overlay = navOverlay;
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'nav__overlay';
        nav.appendChild(overlay);
    }

    // Toggle mobile menu
    if (navToggle) {
        navToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMobileMenu();
        });
    }

    // Close menu when clicking overlay
    overlay.addEventListener('click', () => {
        closeMobileMenu();
    });

    // Close menu when clicking on a link
    navList.querySelectorAll('.nav__link').forEach(link => {
        link.addEventListener('click', () => {
            closeMobileMenu();
        });
    });

    // Close menu on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeMobileMenu();
        }
    });

    // Close menu on resize if window becomes larger
    window.addEventListener('resize', () => {
        if (window.innerWidth > 992) {
            closeMobileMenu();
        }
    });

    function toggleMobileMenu() {
        navToggle.classList.toggle('active');
        navList.classList.toggle('active');
        overlay.classList.toggle('active');
        body.classList.toggle('menu-open');
    }

    function closeMobileMenu() {
        navToggle.classList.remove('active');
        navList.classList.remove('active');
        overlay.classList.remove('active');
        body.classList.remove('menu-open');
    }

    // ========================================
    // ✨ Intersection Observer for Animations
    // ========================================
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe all animate-in elements
    document.querySelectorAll('.animate-in').forEach(el => {
        el.style.animationPlayState = 'paused';
        observer.observe(el);
    });

    // ========================================
    // 🔍 Mobile Search Toggle (Optional)
    // ========================================
    const searchIcon = document.querySelector('.header__search-icon');
    const searchForm = document.querySelector('.header__search');

    if (searchIcon && searchForm) {
        searchIcon.addEventListener('click', () => {
            searchForm.classList.toggle('active');
            searchIcon.classList.toggle('active');
        });
    }

    // ========================================
    // 📰 Ticker Animation Pause on Hover
    // ========================================
    const tickerItems = document.querySelector('.ticker__items');
    if (tickerItems) {
        tickerItems.addEventListener('mouseenter', () => {
            tickerItems.style.animationPlayState = 'paused';
        });
        tickerItems.addEventListener('mouseleave', () => {
            tickerItems.style.animationPlayState = 'running';
        });
    }

    // ========================================
    // 🎠 Hero Mobile Swiper
    // ========================================
    let heroSwiper = null;

    function initHeroSwiper() {
        const swiperEl = document.querySelector('.hero-slider');
        if (!swiperEl) return;

        if (window.innerWidth <= 768) {
            if (!heroSwiper) {
                heroSwiper = new Swiper('.hero-slider', {
                    direction: 'horizontal',
                    loop: true,
                    autoplay: {
                        delay: 4500,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true
                    },
                    speed: 600,
                    effect: 'slide',
                    grabCursor: true,
                    pagination: {
                        el: '.hero-slider .swiper-pagination',
                        clickable: true
                    },
                    spaceBetween: 0
                });
            }
        } else {
            if (heroSwiper) {
                heroSwiper.destroy(true, true);
                heroSwiper = null;
            }
        }
    }

    if (typeof Swiper !== 'undefined') {
        initHeroSwiper();
        window.addEventListener('resize', initHeroSwiper);
    }
});
