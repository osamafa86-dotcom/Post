// Add this at the start of the file
if (history.scrollRestoration) {
  history.scrollRestoration = 'manual';
}

// window.onbeforeunload = function() {
//   window.scrollTo(0, 0);
// }

// Force scroll to top on page load/refresh
// window.onload = function() {
//   window.scrollTo(0, 0);
//   document.body.classList.add('no-scroll');
// }

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
      tl.fromTo(image1, 
        { scale: 0.1, opacity: 0 }, 
        { scale: 1, opacity: 1, duration: 4, ease: "power2.out" }
      );

      tl.to(image1, { opacity: 0, duration: 1 });
      tl.fromTo(image2, 
        { scale: 1.2, opacity: 0 }, 
        { scale: 1.9, opacity: 1,  duration: .4, ease: "power2.out" }
      );

      tl.to(image1, { opacity: 0, duration: 0.5 });
      tl.to(image2, { opacity: 0, duration: 0.5 });

      tl.fromTo(mainBanner, 
        {opacity: 0, zIndex: -1}, 
        {opacity: 1, zIndex: 20000, duration: 1, ease: "back.out(1.7)"}
      );

      tl.add(() => {
        image1.classList.remove('hidden');
        image1.classList.add('floating');
      });

      tl.to(image1, { opacity: 1, duration: 0.5 });
      tl.to(muteBtn, { opacity: 1, duration: 0.5 });

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
    }, 7000); 
  });
});

// Toggle mute and add/remove muted line
// document.getElementById('muteBtn').addEventListener('click', function() {
//   const audio2 = document.getElementById('introAudio2');
//   audio2.muted = !audio2.muted;
//   this.classList.toggle('muted');
//   if (audio2.muted) {
//     mutedLine.style.opacity = '1';
//   } else {
//     mutedLine.style.opacity = '0';
//   }
// });

document.getElementById('muteBtn').addEventListener('click', function () {
  const audio2 = document.getElementById('introAudio2');
  const container = this.closest('.sound-container');

  audio2.muted = !audio2.muted;
  container.classList.toggle('muted');

  console.log('Mute clicked', audio2.muted);
});


const image = document.getElementById('fixed-image');
const image3 = document.getElementById('fixed-image02');
const image2 = document.getElementById('images-contaner');
const wrapper = document.getElementById('image-wrapper');
const sectionTop = document.getElementById('main-section');

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
    }, 500); // match the CSS transition duration
  } else if (scrollTop < imageOffsetTop && isFixed) {
    isFixed = false;
    image.classList.remove('fixed');
    image2.classList.remove('fixed');
    image3.classList.remove('fixed');
  }
});

  


// window.onload = function playSequence() {
//   const audio = document.getElementById('introAudio');
//   const image1 = document.getElementById('img01');
//   const image2 = document.getElementById('img02');
//   const startContent = document.getElementById('startContent');

//   audio.play();

//   // Make sure all elements are visible for GSAP to animate
//   image1.classList.remove('hidden');
//   image2.classList.remove('hidden');
//   startContent.classList.remove('hidden');

//   const tl = gsap.timeline();

//   // Image 1: Scale from 0.1 to 1 over 5s
//   tl.fromTo(image1, 
//     { scale: 0.1, opacity: 0 }, 
//     { scale: 1, opacity: 1, duration: 5, ease: "power2.out" }
//   );

//   // Hide image1, show and scale image2
//   tl.to(image1, { opacity: 0, duration: 0.5 });
//   tl.fromTo(image2, 
//     { scale: 1.2, opacity: 0 }, 
//     { scale: 1.6, opacity: 1, duration: 2, ease: "power2.out" }
//   );

//   // Show start button
//   tl.fromTo(startContent, 
//     { scale: 0.5, opacity: 0 }, 
//     { scale: 1, opacity: 1, duration: 1, ease: "back.out(1.7)" }
//   );
// };




// gsap.registerPlugin(ScrollTrigger);


// // You can use a ScrollTrigger in a tween or timeline
// gsap.to(".img01a", {
//   y: 0,
//   // rotation: 360,
//   opacity: 1,
//   scrollTrigger: {
//     trigger: "#timeline01",
//     // start: "top center",
//     // end: "top 100px",
//    toggleActions: "play none none reverse",
//     markers: false,
//     id: "scrub",
//   }
// });

// gsap.to(".part02-img", {
//   // rotation: 360,
//   opacity: 0,
//   display:"none",
//   scrollTrigger: {
//     trigger: "#timeline02",
//     // start: "top center",
//     // end: "top 100px",
//    toggleActions: "play none none reverse",
//     markers: false,
//     id: "scrub",
//   }
// });

// gsap.to(".part03-img", {
//   // rotation: 360,
//   opacity: 1,
//   display:"block",
//   scrollTrigger: {
//     trigger: "#timeline02",
//     // start: "top center",
//     // end: "top 100px",
//    toggleActions: "play none none reverse",
//     markers: false,
//     id: "scrub",
//   }
// });


gsap.registerPlugin(ScrollTrigger);

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



gsap.utils.toArray('.timeline-item').forEach(item => {
  gsap.set(item, {
    opacity: 0,
    y: 100
  });
  
  gsap.to(item, {
    y: 0,
    opacity: 1,
    duration: 1,
    scrollTrigger: {
      trigger: item,
      start: "top bottom", // Starts animation when the top of the element hits the bottom of the viewport
      end: "top 80%",
      toggleActions: "play none none none",
      once: true,
      markers: false
    }
  });
});

// Animate the timeline line
gsap.fromTo(".timeline-container::before", 
  { height: 0 }, 
  { 
    height: "100%",
    duration: 1,
    scrollTrigger: {
      trigger: ".timeline-container",
      start: "top bottom",
      end: "bottom 80%",
      toggleActions: "play none none none",
      once: true
    }
  }
);



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
gsap.to("#img21a-sections", {
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
gsap.to("#img21a-sections", {
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



gsap.to("#img07-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline08",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img08-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline08",
   toggleActions: "play none none reverse"
  }
});


gsap.to("#img08-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline09",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img09-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline09",
   toggleActions: "play none none reverse"
  }
});




gsap.to("#img09-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline11",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img10-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline11",
   toggleActions: "play none none reverse"
  }
});






gsap.to("#img10-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline12",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img11-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline12",
   toggleActions: "play none none reverse"
  }
});





gsap.to("#img11-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline13",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img12-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline13",
   toggleActions: "play none none reverse"
  }
});




gsap.to("#img12-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline14",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img13-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline14",
   toggleActions: "play none none reverse"
  }
});






// gsap.to("#img13-sections", {
//   opacity: 0,
//   scrollTrigger: {
//     trigger: "#timeline15",
//    toggleActions: "play none none reverse"
//   }
// });
// gsap.to("#img14-sections", {
//   opacity: 1,
//   scrollTrigger: {
//     trigger: "#timeline15",
//    toggleActions: "play none none reverse"
//   }
// });

// gsap.to("#img14-sections", {
//   opacity: 0,
//   scrollTrigger: {
//     trigger: "#timeline16",
//    toggleActions: "play none none reverse"
//   }
// });
// gsap.to("#img15-sections", {
//   opacity: 1,
//   scrollTrigger: {
//     trigger: "#timeline16",
//    toggleActions: "play none none reverse"
//   }
// });
// gsap.to("#img15-sections", {
//   opacity: 0,
//   scrollTrigger: {
//     trigger: "#timeline17",
//    toggleActions: "play none none reverse"
//   }
// });
// gsap.to("#img16-sections", {
//   opacity: 1,
//   scrollTrigger: {
//     trigger: "#timeline17",
//    toggleActions: "play none none reverse"
//   }
// });
// gsap.to("#img16-sections", {
//   opacity: 0,
//   scrollTrigger: {
//     trigger: "#timeline18",
//    toggleActions: "play none none reverse"
//   }
// });
// gsap.to("#img17-sections", {
//   opacity: 1,
//   scrollTrigger: {
//     trigger: "#timeline18",
//    toggleActions: "play none none reverse"
//   }
// });

// gsap.to("#img17-sections", {
//   opacity: 0,
//   scrollTrigger: {
//     trigger: "#timeline19",
//    toggleActions: "play none none reverse"
//   }
// });
// gsap.to("#img18-sections", {
//   opacity: 1,
//   scrollTrigger: {
//     trigger: "#timeline19",
//    toggleActions: "play none none reverse"
//   }
// });




gsap.to("#img13-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline20",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img19-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline20",
   toggleActions: "play none none reverse"
  }
});


gsap.to("#img19-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline21",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img20-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline21",
   toggleActions: "play none none reverse"
  }
});




gsap.to("#img20-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline22",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img21-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline22",
   toggleActions: "play none none reverse"
  }
});


gsap.to("#img21-sections", {
  opacity: 0,
  scrollTrigger: {
    trigger: "#timeline23",
   toggleActions: "play none none reverse"
  }
});
gsap.to("#img22-sections", {
  opacity: 1,
  scrollTrigger: {
    trigger: "#timeline23",
   toggleActions: "play none none reverse"
  }
});






function showDog(id, el) {
  document.querySelectorAll('.dog-block').forEach(el => el.classList.remove('active'));
  document.getElementById(id).classList.add('active');

  document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active'));
  el.classList.add('active');
}

// Dogs Tabs Functionality
document.addEventListener('DOMContentLoaded', function() {
  const tabButtons = document.querySelectorAll('.tab-btn');
  const tabContents = document.querySelectorAll('.tab-content');

  function switchTab(breedId) {
    // Remove active class from all buttons and contents
    tabButtons.forEach(btn => btn.classList.remove('active'));
    tabContents.forEach(content => content.classList.remove('active'));

    // Add active class to clicked button and corresponding content
    const activeButton = document.querySelector(`[data-breed="${breedId}"]`);
    const activeContent = document.getElementById(breedId);

    if (activeButton && activeContent) {
      activeButton.classList.add('active');
      activeContent.classList.add('active');
    }
  }

  // Add click event listeners to all tab buttons
  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      const breedId = button.getAttribute('data-breed');
      switchTab(breedId);
    });
  });
});

// Legal Tabs Functionality
document.addEventListener('DOMContentLoaded', function() {
  const tabButtons = document.querySelectorAll('.tab-btn');
  const tabContents = document.querySelectorAll('.tab-content');

  function switchTab(tabId) {
    // Remove active class from all buttons and contents
    tabButtons.forEach(btn => btn.classList.remove('active'));
    tabContents.forEach(content => content.classList.remove('active'));

    // Add active class to clicked button and corresponding content
    const selectedButton = document.querySelector(`[data-tab="${tabId}"]`);
    const selectedContent = document.querySelector(`#${tabId}`);
    
    if (selectedButton && selectedContent) {
      selectedButton.classList.add('active');
      selectedContent.classList.add('active');
    }
  }

  // Add click event listeners to all tab buttons
  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      const tabId = button.getAttribute('data-tab');
      switchTab(tabId);
    });
  });

  // Set first tab as active by default
  if (tabButtons.length > 0) {
    const firstTabId = tabButtons[0].getAttribute('data-tab');
    switchTab(firstTabId);
  }
});

// Tab switching functionality for rules-low section
document.addEventListener('DOMContentLoaded', function() {
  const tabButtons = document.querySelectorAll('.legal-tabs .tab-btn');
  const tabContents = document.querySelectorAll('.legal-tabs .tab-content');

  tabButtons.forEach(button => {
    button.addEventListener('click', () => {
      // Remove active class from all buttons and contents
      tabButtons.forEach(btn => btn.classList.remove('active'));
      tabContents.forEach(content => content.classList.remove('active'));

      // Add active class to clicked button and corresponding content
      button.classList.add('active');
      const tabId = button.getAttribute('data-tab');
      document.getElementById(tabId).classList.add('active');
    });
  });
});



const tabs = document.querySelectorAll('.tab');
const images = document.querySelectorAll('.image-container');

tabs.forEach(tab => {
  tab.addEventListener('click', () => {
    const tabId = tab.getAttribute('data-tab');

    // Toggle tab active state
    tabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');

    // Toggle image visibility
    images.forEach(img => {
      img.classList.remove('active');
      if (img.getAttribute('data-tab') === tabId) {
        img.classList.add('active');
      }
    });
  });
});

// function initializeTimelineItems() {
//   // Get all timeline items
//   const timelineItems = document.querySelectorAll('.timeline-item');
  
//   timelineItems.forEach((item) => {
//     gsap.fromTo(item,
//       {
//         opacity: 0,
//         y: 0
//       },
//       {
//         opacity: 1,
//         y: 0,
//         duration: 0.8,
//         scrollTrigger: {
//           trigger: item,
//           start: "top 90%", // Start when element is almost in view (90% from top of viewport)
//           end: "bottom 10%", // End when element is almost out of view
//           toggleActions: "play none none none", // Changed to keep elements visible
//           scrub: false, // No scrubbing effect
//           once: true // Animation plays only once
//         }
//       }
//     );
//   });
// }
// gsap.fromTo(".timeline-container::before",
//   { height: 0 },
//   {
//     height: "100%",
//     duration: 1,
//     scrollTrigger: {
//       trigger: ".timeline-container",
//       start: "top 90%", // Match with items
//       end: "bottom 10%",
//       toggleActions: "play none none none",
//       once: true // Line appears once and stays
//     }
//   }
// );
// document.addEventListener('DOMContentLoaded', function() {
//   initializeTimelineItems();
// });

// Initialize timeline items animation


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




// document.querySelectorAll('.box-cases-col').forEach(trigger => {
//   trigger.addEventListener('click', () => {
//     const targetId = trigger.getAttribute('data-modal');
//     const modal = document.getElementById(targetId);
//     modal.classList.add('show');
//     document.body.style.overflow = 'hidden'; // 🚫 disable scroll
//   });
// });

// document.querySelectorAll('.modal .close').forEach(closeBtn => {
//   closeBtn.addEventListener('click', () => {
//     const modal = closeBtn.closest('.modal');
//     modal.classList.remove('show');
//     document.body.style.overflow = ''; // ✅ restore scroll
//   });
// });

// window.addEventListener('click', e => {
//   if (e.target.classList.contains('modal')) {
//     e.target.classList.remove('show');
//     document.body.style.overflow = ''; // ✅ restore scroll
//   }
// });



// Store the original overflow value
let originalOverflow = '';

document.querySelectorAll('.box-cases-col').forEach(trigger => {
  trigger.addEventListener('click', () => {
    const targetId = trigger.getAttribute('data-modal');
    const modal = document.getElementById(targetId);
    
    // Store original overflow before disabling
    originalOverflow = document.body.style.overflow || getComputedStyle(document.body).overflow;
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Additional mobile fix - prevent scrolling on the html element too
    document.documentElement.style.overflow = 'hidden';
  });
});

// Function to restore scroll
function restoreScroll() {
  document.body.style.overflow = originalOverflow || 'auto';
  document.documentElement.style.overflow = '';
  
  // Force reflow on mobile
  if (/Mobi|Android/i.test(navigator.userAgent)) {
    setTimeout(() => {
      document.body.style.overflow = originalOverflow || 'visible';
    }, 10);
  }
}

document.querySelectorAll('.modal .close').forEach(closeBtn => {
  closeBtn.addEventListener('click', () => {
    const modal = closeBtn.closest('.modal');
    modal.classList.remove('show');
    restoreScroll();
  });
});

window.addEventListener('click', e => {
  if (e.target.classList.contains('modal')) {
    e.target.classList.remove('show');
    restoreScroll();
  }
});

// Additional safeguard - restore scroll when pressing escape
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    const openModal = document.querySelector('.modal.show');
    if (openModal) {
      openModal.classList.remove('show');
      restoreScroll();
    }
  }
});