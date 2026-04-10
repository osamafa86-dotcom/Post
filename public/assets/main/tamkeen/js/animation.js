 // Animate all elements with class "section" as they come into view
  // This uses GSAP's ScrollTrigger to animate each .section when it reaches the viewport.
  gsap.utils.toArray('.section').forEach(section => {
    gsap.from(section, {
      opacity: 0,
      y: 50,
      duration: 1,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: section,
        start: 'top 80%',
        toggleActions: 'play none none reverse'
      }
    });
  });

  // GSAP timeline for the hero section elements
  // This controls how the hero content and buttons appear on page load.
  gsap.timeline({
      defaults: {
        duration: 1,
        ease: "power3.out"
      }
    })
    .from(".hero-section .content h1", {
      opacity: 0,
      y: -50
    })
    .from(".hero-section .content h4", {
      opacity: 0,
      y: -30
    }, "-=0.5")
    .from(".hero-section .buttons-group a", {
      opacity: 0,
      x: -30,
      stagger: 0.2
    }, "-=0.5")
    .from(".hero-section img", {
      opacity: 0,
      x: 30
    }, "-=0.5");

  // Button Hover Effect with GSAP (subtle scale and shadow)
  // Adds a hover animation to buttons with class .btn.
  document.querySelectorAll(".btn").forEach(btn => {
    gsap.set(btn, {
      transformOrigin: "center center"
    });

    // Create a timeline for the hover effect, initially paused
    let hoverTl = gsap.timeline({ paused: true });

    hoverTl.to(btn, {
      duration: 0.3,
      scale: 1.08,
      boxShadow: "0px 4px 12px rgba(0, 0, 0, 0.15)",
      ease: "power2.out"
    });

    // On mouse enter, play the hover timeline
    // Also rotate the icon if present
    btn.addEventListener("mouseenter", () => {
      hoverTl.play();
      let icon = btn.querySelector("i");
      if (icon) {
        gsap.to(icon, {
          duration: 0.5,
          rotation: 360,
          ease: "power2.out"
        });
      }
    });

    // On mouse leave, reverse the hover timeline
    // Reset icon rotation if present
    btn.addEventListener("mouseleave", () => {
      hoverTl.reverse();
      let icon = btn.querySelector("i");
      if (icon) {
        gsap.to(icon, {
          duration: 0.5,
          rotation: 0,
          ease: "power2.out"
        });
      }
    });
  });

  // Filter functionality for gallery items
  // This allows filtering items by category when clicking filter buttons.
  const filterButtons = document.querySelectorAll(".filter-btn");
  const galleryItems = document.querySelectorAll(".gallery-item");

  filterButtons.forEach(btn => {
    btn.addEventListener("click", function () {
      // Remove active class from all filter buttons
      filterButtons.forEach(button => button.classList.remove("active"));
      this.classList.add("active");

      const filterValue = this.getAttribute("data-filter");
      galleryItems.forEach(item => {
        // Show the item if filter matches or if filter is "all"
        if (filterValue === "all" || item.getAttribute("data-category") === filterValue) {
          item.style.display = "block";
          gsap.fromTo(item, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.5 });
        } else {
          // Otherwise hide the item
          item.style.display = "none";
        }
      });
    });
  });

  // Load More functionality
  // Initially shows 6 items, then loads 3 more each time the button is clicked.
  let itemsToShow = 6;
  galleryItems.forEach((item, index) => {
    if (index >= itemsToShow) {
      item.style.display = "none";
    }
  });

  document.getElementById("loadMoreBtn").addEventListener("click", () => {
    itemsToShow += 3;
    galleryItems.forEach((item, index) => {
      if (index < itemsToShow) {
        item.style.display = "block";
        gsap.fromTo(item, { opacity: 0, y: 20 }, { opacity: 1, y: 0, duration: 0.5 });
      }
    });
  });
  
  // Note: A similar ScrollTrigger code for <section> elements was removed
  // to avoid potential double animations or conflicts with .section.