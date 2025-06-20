

// for swiperSection
$(document).ready(function () {
    let rtlDir;
    if ($('html').attr('dir') === 'rtl') {
        rtlDir = true
    }
    else {
        rtlDir = false;
    }
  // Initialize each carousel separately
  $(".swiperSect .slider-content.owl-carousel").each(function () {
      var owl = $(this).owlCarousel({
          loop: true,
          rtl: rtlDir,
          autoplay: true,
          autoplayTimeout: 1500,
          autoplaySpeed: 2000,
          margin: 30,
          nav: false,
          responsive: {
              0: {
                  items: 1,
              },
              600: {
                  items: 3,
              },
              1000: {
                  items: 5,
              },
          },
      });

      // Custom navigation buttons for this specific carousel
      $(this)
          .closest(".commonSlider")
          .find(".prev")
          .click(function () {
              owl.trigger("prev.owl.carousel");
          });

      $(this)
          .closest(".commonSlider")
          .find(".next")
          .click(function () {
              owl.trigger("next.owl.carousel");
          });
  });
});

// for pricingSection
$(document).ready(function () {
    let rtlDir;
    if ($('html').attr('dir') === 'rtl') {
        rtlDir = true
    }
    else {
        rtlDir = false;
    }
  // Initialize each carousel separately
  $(".pricing .slider-content.owl-carousel").each(function () {
      var owl = $(this).owlCarousel({
          loop: false,
          rtl: rtlDir,
          autoplay: false,
          autoplayTimeout: 1000,
          autoplaySpeed: 2000,
          margin: 30,
          nav: false,
          dots: true,
          responsive: {
              0: {
                  items: 1,
              },
              600: {
                  items: 2,
              },
              1000: {
                  items: 3,
              },
          },
      });

      // Custom navigation buttons for this specific carousel
      $(this)
          .closest(".commonSlider")
          .find(".prev")
          .click(function () {
              owl.trigger("prev.owl.carousel");
          });

      $(this)
          .closest(".commonSlider")
          .find(".next")
          .click(function () {
              owl.trigger("next.owl.carousel");
          });
  });
});




// for counter

document.addEventListener("DOMContentLoaded", function () {
  const counters = document.querySelectorAll('.numb');

  const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
          if (entry.isIntersecting) {
              const target = +entry.target.getAttribute('data-target');
              entry.target.innerText = 0;
              const updateCounter = () => {
                  const value = +entry.target.innerText;
                  const increment = target / 150; // Adjust the speed of the counter by changing the denominator

                  if (value < target) {
                      entry.target.innerText = Math.ceil(value + increment);
                      setTimeout(updateCounter, 10); // Adjust the interval for smoother animation
                  } else {
                      entry.target.innerText = target;
                  }
              };

              updateCounter();
              observer.unobserve(entry.target);
          }
      });
  });

  counters.forEach(counter => {
      observer.observe(counter);
  });
});
var buttonClicked = false;

$('.view-more-feature').click(function (e) { 
    e.preventDefault();
    $('.default-feature-list').slideToggle(500);
    if (buttonClicked) {
        this.textContent = lang_view_more_features;
        buttonClicked = false;
    } else {
        this.textContent = lang_view_less_features;
        buttonClicked = true;
    }
});

// landing page school logos display slider
$(document).ready(function () {
    $('.school-logo-owl-carousel').owlCarousel({
        loop:true,
        margin:10,
        nav:false,
        autoplaySpeed:1000,
        items:5,
        autoplay:true,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:3
            },
            1000:{
                items:5
            }
        }
    });
});