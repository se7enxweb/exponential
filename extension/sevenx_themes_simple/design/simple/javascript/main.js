(() => {
  const GB = {};

  // manage navbar
  GB.navbarToggle = () => {
    const toggler = document.querySelector("[data-collapse-menu]");
    const navbar = document.querySelector("#navbarCollapse");

    if (!toggler) return;

    const resetDropdwon = function () {
      document.body.classList.remove("overflow-y-hidden");
      toggler.classList.remove("active");
      navbar.classList.remove("show");
    };

    const toggleCallback = function () {
      if (!toggler.classList.contains("active")) {
        document.body.classList.add("overflow-y-hidden");
        toggler.classList.add("active");
        navbar.classList.add("show");
        return;
      }

      resetDropdwon();
    };

    toggler.addEventListener("click", toggleCallback);

    // window on resize
    window.addEventListener("resize", () => {
      if (window.innerWidth >= 1024) {
        resetDropdwon();
      }
    });
  };

  // sticky header
  GB.headerSticky = () => {
    const header = document.getElementById("headerMain");
    const navbar = document.getElementById("navbarCollapse");
    const scorllPosition = window.scrollY;

    // set navbar height
    navbar.style.height = `calc(100vh - ${header.clientHeight}px)`;

    if (scorllPosition >= header.clientHeight) {
      header.classList.add("sticky");
      return;
    }

    header.classList.remove("sticky");
  };

  // dropdown menu
  GB.dropdown = () => {
    // reset Dropdown Menus
    const resetDropdwon = () => {
      const dropdowns = Array.from(document.getElementsByClassName("dropdown"));

      dropdowns.forEach((dropdown) => {
        dropdown.classList.remove("active");
      });
    };

    // toggler callback
    const toggler = function (event) {
      const toggleBtn = event.target.closest(".dropdown-toggle");
      const dropdownMenu = event.target.closest(".dropdown-menu");

      if (!toggleBtn || dropdownMenu) {
        // reset all dropdown menu
        return resetDropdwon();
      }

      const dropdown = toggleBtn.closest(".dropdown");

      if (dropdown.classList.contains("active")) {
        return dropdown.classList.remove("active");
      }

      // reset all dropdown menu
      resetDropdwon();
      dropdown.classList.add("active");
    };

    window.addEventListener("click", toggler);
  };

  // magnific popup
  GB.initMagnificPopup = (selector = 'a', options = {}) => {
    if(!$(selector).length) return;

    $(selector).magnificPopup(options);
  }

  // all the popups
  GB.popups = () => {
    // image gallery
    (() => {
      GB.initMagnificPopup('.myGallery', {
        markup: '<div class="mfp-figure gb-figure">'+
            '<div class="mfp-close"></div>'+
            '<div class="mfp-img"></div>'+
            '<div class="mfp-bottom-bar">'+
              '<div class="mfp-title"></div>'+
              '<div class="mfp-counter"></div>'+
            '</div>'+
          '</div>', 
        delegate: 'a.open',
        type: 'image',
        gallery: {
          enabled:true
        },
        removalDelay: 300,
        mainClass: 'mfp-fade',
        titleSrc: 'title',
      })
    })();
  } 

// scroll to top
  GB.scrollToTop = () => {
    const trigger = document.getElementById("scroll-to-top");

    const goTop = () => {
      const scrollPosition = window.scrollY;

      if (scrollPosition >= 10) {
        setTimeout(function () {
          window.scrollTo(0, scrollPosition - 30);
          goTop();
        }, 5);
      }
    };

    trigger.addEventListener("click", goTop);
  };

  GB.revealTrigger = () => {
    const trigger = document.getElementById("scroll-to-top");
    const scrollPosition = window.scrollY;

    if (scrollPosition >= 400) {
      return trigger.classList.add("active");
    }

    trigger.classList.remove("active");
  };


  // window on load
  document.addEventListener("DOMContentLoaded", () => {
    GB.navbarToggle();
    GB.headerSticky();
    GB.dropdown();
    GB.scrollToTop();
    GB.revealTrigger();
    GB.popups();
  });

  // window on resize
  window.addEventListener("resize", () => {
    GB.headerSticky();
  });

  // window on scroll
  window.addEventListener("scroll", () => {
    GB.headerSticky();
    GB.revealTrigger();
  });
})();
