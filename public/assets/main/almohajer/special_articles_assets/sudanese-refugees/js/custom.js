// Add this at the start of the file
if (history.scrollRestoration) {
  history.scrollRestoration = 'manual';
}



document.addEventListener('DOMContentLoaded', function() {
  // Force scroll to top
  // window.scrollTo(0, 0);
  
  const startButton = document.querySelector('#startExperienceBtn');
  const startContent = document.getElementById('startContent');
  const mainBanner = document.getElementById('mainBanner');
  const audio = document.getElementById('introAudio');
  const audio2 = document.getElementById('introAudio2');
  const muteBtn = document.getElementById('muteBtn');
  const image1 = document.getElementById('img01');
  const image2 = document.getElementById('img02');
  
  startButton.addEventListener('click', function() {
    // Hide start content
    startContent.style.display = "none";
    this.style.display = "none";

    // Show and animate images
    image1.classList.remove('hidden');
    image2.classList.remove('hidden');

    // Play audio and start animations
    audio.play().then(() => {
      const tl = gsap.timeline({
        onComplete: () => {
          audio2.play();
          // Remove no-scroll class after animations complete and banner is visible
          document.body.classList.remove('no-scroll');
          document.body.style.overflow = 'auto';
          document.body.style.position = 'static';
        }
      });

      // Your existing animation timeline
      // tl.fromTo(image1, 
      //   { scale: 0.1, opacity: 0 }, 
      //   { scale: 1, opacity: 1, duration: 4, ease: "power2.out" }
      // );

      // tl.to(image1, { opacity: 0, duration: 1 });
      // tl.fromTo(image2, 
      //   { scale: 1.2, opacity: 0 }, 
      //   { scale: 1.9, opacity: 1,  duration: .4, ease: "power2.out" }
      // );

      // tl.to(image1, { opacity: 0, duration: 0.5 });
      // tl.to(image2, { opacity: 0, duration: 0.5 });

      tl.fromTo(mainBanner, 
        {opacity: 0, zIndex: -1}, 
        {opacity: 1, zIndex: 1120000, duration: 1, ease: "back.out(1.7)"}
      );

        tl.add(() => {
          image1.classList.remove('hidden');
          image1.classList.add('floating');
        });

        tl.to(image1, { opacity: 1, duration: 0.5 });

    }).catch(err => {
      console.error("Audio failed to play:", err);
      // If audio fails, still remove no-scroll
      document.body.classList.remove('no-scroll');
      document.body.style.overflow = 'auto';
      document.body.style.position = 'static';
    });

    setTimeout(() => {
      if (document.body.classList.contains('no-scroll')) {
        document.body.classList.remove('no-scroll');
        document.body.style.overflow = 'auto';
        document.body.style.position = 'static';
      }
    }, 500); 
  });
});

// document.getElementById('muteBtn').addEventListener('click', function () {
//   const audio2 = document.getElementById('introAudio2');
//   const container = this.closest('.sound-container');

//   audio2.muted = !audio2.muted;
//   container.classList.toggle('muted');

//   console.log('Mute clicked', audio2.muted);
// });


const image = document.getElementById('fixed-image');
const image3 = document.getElementById('fixed-image02');
const image2 = document.getElementById('images-contaner');
const wrapper = document.getElementById('image-wrapper');
const sectionTop = document.getElementById('mainBanner');

let imageHeight = image.offsetHeight;
let isFixed = false;

window.addEventListener('scroll', () => {
  const imageOffsetTop = sectionTop.offsetTop;
  const scrollTop = window.scrollY;

  if (scrollTop >= imageOffsetTop && !isFixed) {
    isFixed = true;
    image.classList.add('animate-fixed');
    image2.classList.add('animate-fixed');
    image3.classList.add('animate-fixed');
    wrapper.style.height = imageHeight + 'px'; // reserve space
    setTimeout(() => {
      image.classList.remove('animate-fixed');
      image2.classList.remove('animate-fixed');
      image3.classList.remove('animate-fixed');
      image.classList.add('fixed');
      image2.classList.add('fixed');
      image3.classList.add('fixed');
    }, 100); // match the CSS transition duration
  } else if (scrollTop < imageOffsetTop && isFixed) {
    isFixed = false;
    image.classList.remove('fixed');
    image2.classList.remove('fixed');
    image3.classList.remove('fixed');
  }
});

  

gsap.registerPlugin(ScrollTrigger);


gsap.to("#img00-sections", {
  y: 0,
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline00",
    //toggleActions: "play none none reverse"
    toggleActions: "play none none reverse"
  }
});


gsap.to("#img00-sections", {
  y: 0,
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline01",
    //toggleActions: "play none none reverse"
    toggleActions: "play none none reverse"
  }
});
gsap.to("#img01-sections", {
  y: 0,
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline01",
    //toggleActions: "play none none reverse"
    toggleActions: "play none none reverse"
  }
});

gsap.to("#img01-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline02",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img02-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline02",
   toggleActions: "play none none reverse"
  }
});




gsap.to("#img02-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline03",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img03-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline03",
   toggleActions: "play none none reverse"
  }
});




gsap.to("#img03-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline04",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img04-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline04",
   toggleActions: "play none none reverse"
  }
});





gsap.to("#img04-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline05",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img05-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline05",
   toggleActions: "play none none reverse"
  }
});




gsap.to("#img05-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline06",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img06-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline06",
   toggleActions: "play none none reverse"
  }
});




gsap.to("#img06-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline07",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img07-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline07",
   toggleActions: "play none none reverse"
  }
});








// Initialize timeline items animation
function initializeTimelineItems() {
  // Get all timeline items
  const timelineItems = document.querySelectorAll('.timeline-item');
  
  // First, set initial state for all items to ensure they're hidden
  gsap.set(timelineItems, { opacity: 0, y: 50 });
  
  timelineItems.forEach((item) => {
    gsap.fromTo(item,
      {
        opacity: 0,
        y: 50 // Start slightly below
      },
      {
        opacity: 1,
        y: 0,
        duration: 0.8,
        ease: "power2.out",
        scrollTrigger: {
          trigger: item,
          start: "top 120%", // Start much earlier - when element is still below viewport
          end: "bottom 20%", 
          toggleActions: "play none none none",
          scrub: false,
          once: true,
          // Add markers for debugging (remove in production)
          // markers: true
        }
      }
    );
  });
}

// Initialize timeline line animation
gsap.fromTo(".timeline-container::before",
  { height: 0 },
  {
    height: "100%",
    duration: 2, // Slower duration for line
    ease: "power2.out",
    scrollTrigger: {
      trigger: ".timeline-container",
      start: "top 80%", // Start line animation earlier
      end: "bottom 20%",
      toggleActions: "play none none none",
      once: true
    }
  }
);

// Call the initialization function when the document is ready
document.addEventListener('DOMContentLoaded', function() {
  initializeTimelineItems();
}); 





document.querySelectorAll('.box-cases-col').forEach(trigger => {
  trigger.addEventListener('click', () => {
    const targetId = trigger.getAttribute('data-modal');
    const modal = document.getElementById(targetId);
    
    originalOverflow = document.body.style.overflow || getComputedStyle(document.body).overflow;
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
  });
});

document.querySelectorAll('.modal .close').forEach(closeBtn => {
  closeBtn.addEventListener('click', () => {
    const modal = closeBtn.closest('.modal');
    modal.classList.remove('show');
    document.body.style.overflow = '';
  });
});

window.addEventListener('click', e => {
  if (e.target.classList.contains('modal')) {
    e.target.classList.remove('show');
    document.body.style.overflow = '';
  }
});

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    const openModal = document.querySelector('.modal.show');
    if (openModal) {
      openModal.classList.remove('show');
      document.body.style.overflow = '';
    }
  }
});





// Map Tooltip Functionality
document.addEventListener('DOMContentLoaded', function() {
  const pins = document.querySelectorAll('.allPin');
  const tooltips = document.querySelectorAll('.tooltip-text');
  
  // Function to hide all tooltips
  function hideAllTooltips() {
    tooltips.forEach(tooltip => {
      tooltip.classList.remove('show');
    });
    pins.forEach(pin => {
      pin.classList.remove('active');
    });
  }
  
  // Add click event listeners to pins
  pins.forEach(pin => {
    pin.addEventListener('click', function(e) {
      e.stopPropagation();
      
      const tooltip = this.querySelector('.tooltip-text');
      
      // If this tooltip is already showing, hide it
      if (tooltip.classList.contains('show')) {
        hideAllTooltips();
        return;
      }
      
      // Hide all other tooltips first
      hideAllTooltips();
      
      // Show this tooltip
      tooltip.classList.add('show');
      this.classList.add('active');
    });
  });
  
  // Close tooltips when clicking outside
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.allPin')) {
      hideAllTooltips();
    }
  });
  
  // Close tooltips on escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      hideAllTooltips();
    }
  });
});




