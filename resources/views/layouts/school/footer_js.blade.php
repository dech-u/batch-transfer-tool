<script>
//    let toast_position = 'top-right';
</script>
<script src="{{ asset('/assets/school/js/script.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<!-- bootstrap  -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

<!-- fontawesome icons   -->
<script src="https://kit.fontawesome.com/1d2a297b20.js" crossorigin="anonymous"></script>

<!-- swiper  -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>

<!-- swiper  -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>


{{-- <script src="{{ asset('/assets/js/ekko-lightbox.min.js') }}"></script> --}}

<script src="{{ asset('/assets/js/jquery.repeater.js') }}"></script>
<script src="{{ asset('/assets/school/js/script-js.js') }}"></script>

<script src="{{ asset('/assets/jquery-toast-plugin/jquery.toast.min.js') }}"></script>
<script src="{{ asset('/assets/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('/assets/js/jquery-additional-methods.min.js')}}"></script>
<script src="{{ asset('/assets/js/custom/validate.js') }}"></script>


<script src="{{ asset('/assets/js/custom/function.js') }}"></script>
<script>
    // annaouncementSlider
    $(document).ready(function() {
        // Initialize each carousel separately
        $(".announcementSwiper").each(function() {
            var owl = $(this).owlCarousel({
                loop: true,
                margin: 10,
                nav: false,
                responsive: {
                    0: {
                        items: 1,
                    },
                    600: {
                        items: 1.5,
                    },
                    1000: {
                        items: 1.5,
                    },
                },
            });

            // Custom navigation buttons for this specific carousel
            $(this)
                .closest(".annaouncementSection")
                .find(".prev")
                .click(function() {
                    owl.trigger("prev.owl.carousel");
                });

            $(this)
                .closest(".annaouncementSection")
                .find(".next")
                .click(function() {
                    owl.trigger("next.owl.carousel");
                });
        });
    });

    // hero-slider
    $(document).ready(function() {
        var itemCount = $(".hero-carousel .item").length;
        $(".hero-carousel").owlCarousel({
            items: 1,
            loop: itemCount > 1,
            autoplay: true,
            autoplayTimeout: 2000, // Set autoplay interval in milliseconds
            autoplayHoverPause: true, // Pause autoplay when mouse hovers over the carousel
            nav: true,
            navText: [
                "<i class='fa-solid fa-arrow-left'></i>",
                "<i class='fa-solid fa-arrow-right'></i>",
            ],
        });
    });

    // commonSlider
    $(document).ready(function() {
        // Initialize each carousel separately
        $(".slider-content.owl-carousel").each(function() {
            var owl = $(this).owlCarousel({
                loop: false,
                // margin: 10,
                nav: false,
                responsive: {
                    0: {
                        items: 1,
                    },
                    470: {
                        items: 2,
                    },
                    792: {
                        items: 3,
                    },
                    1000: {
                        items: 4,
                    },
                },
            });

            // Custom navigation buttons for this specific carousel
            $(this)
                .closest(".commonSlider")
                .find(".prev")
                .click(function() {
                    owl.trigger("prev.owl.carousel");
                });

            $(this)
                .closest(".commonSlider")
                .find(".next")
                .click(function() {
                    owl.trigger("next.owl.carousel");
                });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#create-form').on('submit', function (e) {
            e.preventDefault();
            let formElement = $(this);
            let submitButtonElement = $(this).find(':submit');
            let url = $(this).attr('action');
            let data = new FormData(this);

            function successCallback() {
                setTimeout(function () {
                    window.location.reload();
                }, 1000)
            }

            formAjaxRequest('POST', url, data, formElement, submitButtonElement, successCallback);
        })
    });
</script>

<script>
    // Logo
    var nav_logo = "{{ $schoolSettings['horizontal_logo'] ?? '' }}";
    if (nav_logo == null || nav_logo == '') {
        nav_logo = "{{ $systemSettings['horizontal_logo'] ?? asset('assets/landing_page_images/Logo1.svg') }}";
    }

    var footer_logo = "{{ $schoolSettings['footer_logo'] ?? '' }}";
    footer_logo = footer_logo !== '' ? footer_logo : nav_logo;

    $('.nav-logo').attr("src", nav_logo);
    $('.footer-logo').attr("src", footer_logo );

    // Favicon
    $('.school-favicon').attr('href', "{{ $schoolSettings['favicon'] ?? $systemSettings['favicon'] ?? asset('assets/favicon.svg') }}");

    $('.redirect-login').click(function(e) {
        e.preventDefault();
        window.location.href = "{{ url('login') }}"
    });

    window.addEventListener('scroll', function () {
        const header = document.querySelector('header.navbar');
        const navbar = document.querySelector('.navbarWrapper');
        if (window.scrollY > 0) {
            header.classList.add('stickyNav');
            navbar.classList.add('stickyNavActive');
        } else {
            header.classList.remove('stickyNav');
            navbar.classList.remove('stickyNavActive');
        }
    });

    // upperFileUpload

    document.querySelector('.upperFileUpload button').addEventListener('click', function () {
        document.querySelector('.upperFileUpload input[type="file"]').click();
    });

    document.querySelector('.upperFileUpload input[type="file"]').addEventListener('change', function () {
        var file = this.files[0];
        var fileName = file ? file.name : 'No File Selected.';
        document.querySelector('.upperFileUpload span').textContent = fileName;

        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.querySelector('.upperImgPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // ====================================================== //

    // lowerFileUpload

    document.querySelector('.lowerFileUpload button').addEventListener('click', function () {
        document.querySelector('.lowerFileUpload input[type="file"]').click();
    });

    document.querySelector('.lowerFileUpload input[type="file"]').addEventListener('change', function () {
        var file = this.files[0];
        var fileName = file ? file.name : 'No File Selected.';
        document.querySelector('.lowerFileUpload span').textContent = fileName;

        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.querySelector('.lowerImgPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // =================================================================== //

    // studentGender 

    const genderRadios = document.querySelectorAll('input[name="gender"]');

    genderRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            // Remove selectedGender class from all parent divs
            genderRadios.forEach(radio => {
                radio.parentElement.classList.remove('selectedGender');
            });

            // Add selectedGender class to the parent div of the checked radio
            if (this.checked) {
                this.parentElement.classList.add('selectedGender');
            }
        });
    });

    // =================================================================== //

    // parentGender 

    const parentGenderRadios = document.querySelectorAll('input[name="guardian_gender"]');

    parentGenderRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            // Remove selectedGender class from all parent divs
            parentGenderRadios.forEach(radio => {
                radio.parentElement.classList.remove('selectedGender');
            });

            // Add selectedGender class to the parent div of the checked radio
            if (this.checked) {
                this.parentElement.classList.add('selectedGender');
            }
        });
    });

        // ==================================================================================


    // date input 
    const dobInput = document.getElementById('dob');

    dobInput.addEventListener('change', function () {
        if (!this.value) {
            this.classList.add('invalid');
        } else {
            this.classList.remove('invalid');
        }
    });

    // ==================================================================================

    // for select 

    const classMediumSelect = document.getElementById('class_id');

    classMediumSelect.addEventListener('change', function () {
        if (this.value === '') {
            this.classList.add('invalid');
            this.classList.remove('valid');
        } else {
            this.classList.remove('invalid');
            this.classList.add('valid');
        }
    });

    // Initial check
    if (classMediumSelect.value === '') {
        classMediumSelect.classList.add('invalid');
    } else {
        classMediumSelect.classList.add('valid');
    }


</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get the lightbox elements
        setTimeout(() => {
            var lightbox = document.getElementById("lightbox");
            var lightboxImg = document.getElementById("lightbox-img");
            var lightboxVideo = document.getElementById("lightbox-video");
            var captionText = document.getElementById("caption");

            if (lightbox) {


                // Function to open the lightbox
                function openLightbox(contentType, src, caption) {
                    lightbox.style.display = "block";
                    disableScroll();
                    hideNavbar();
                    if (contentType === 'image') {
                        lightboxImg.style.display = "block";
                        lightboxVideo.style.display = "none";
                        lightboxImg.src = src;
                    } else if (contentType === 'video') {
                        lightboxImg.style.display = "none";
                        lightboxVideo.style.display = "block";
                        lightboxVideo.src = src;
                    }
                    captionText.innerHTML = caption;
                }

                // Function to close the lightbox
                function closeLightbox() {
                    enableScroll();
                    showNavbar();
                    lightbox.style.display = "none";
                    lightboxImg.src = "";
                    lightboxVideo.src = "";
                }

                // Get all thumbnails and add click events
                var thumbnails = document.getElementsByClassName('detailArr');
                for (var i = 0; i < thumbnails.length; i++) {
                    thumbnails[i].onclick = function() {
                        var img = this.parentNode.querySelector('.thumbnail');

                        if (img.classList.contains('video-thumbnail')) {
                            // Handle video thumbnail click
                            var videoUrl = img.dataset.video;
                            openLightbox('video', videoUrl, '');
                        } else {
                            // Handle image thumbnail click
                            openLightbox('image', img.src, img.alt);
                        }
                    }
                }

                // Get the close button and add click event
                var closeBtn = document.getElementsByClassName("close")[0];
                if (closeBtn) {
                    closeBtn.onclick = function() {
                        closeLightbox();
                    }
                }


                // Close lightbox when clicking outside of it or pressing Esc key
                window.addEventListener('click', function(event) {
                    if (event.target === lightbox) {
                        closeLightbox();
                    }
                });

                window.addEventListener('keydown', function(event) {
                    if (event.key === "Escape") {
                        closeLightbox();
                    }
                });
            }
        }, 500);

        // Function to disable scroll
        function disableScroll() {
            document.body.style.overflow = 'hidden';
        }

        // Function to enable scroll
        function enableScroll() {
            document.body.style.overflow = '';
        }

        function hideNavbar() {
            document.querySelector('.navbar').style.display = 'none';
        }

        // Function to show the navbar
        function showNavbar() {
            document.querySelector('.navbar').style.display = 'block';
        }
    });
</script>
