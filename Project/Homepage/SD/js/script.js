$(document).ready(function(){

    $('#menu').click(function(){
        $(this).toggleClass('fa-times');
        $('.navbar').toggleClass('active');
    });

    $(window).on('scroll load', function(){

        $('#menu').removeClass('fa-times');
        $('.navbar').removeClass('active');

        if($(window).scrollTop() > 60){
            $('.header').addClass('active');
        }
        else{
            $('.header').removeClass('active');
        }

        $('section').each(function(){

            let top = $(window).scrollTop();
            let height = $(this).height();
            let offset = $(this).offset().top - 200;
            let id = $(this).attr('id');

            if(top >= offset && top < offset + height){
                $('.navbar a').removeClass('active');
                $('.navbar').find(`[href="#${id}"]`).addClass('active');
            }

        });

    });

})

document.addEventListener('DOMContentLoaded', function () {
    var brandSwiper = new Swiper(".brand-slider", {
        loop: true,
        grabCursor: true,
        spaceBetween: 20,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        breakpoints: {
            0: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 3,
            },
            991: {
                slidesPerView: 5,
            },
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
});

//Show privacy policy popup

document.addEventListener('DOMContentLoaded', function() {
    var privacyPolicyLink = document.getElementById('privacyPolicyLink');
    var popupContainer = document.getElementById('popupContainer');
    var agreeButton = document.getElementById('agreeButton');

    // Function to show the privacy policy popup
    function showPrivacyPolicyPopup() {
      popupContainer.style.display = 'block';
      document.body.style.overflow = 'hidden'; // Disable scrolling on the body
    }

    // Function to hide the privacy policy popup
    function hidePrivacyPolicyPopup() {
      popupContainer.style.display = 'none';
      document.body.style.overflow = 'auto'; // Enable scrolling on the body
    }

    // Event listener for clicking the Privacy Policy link
    privacyPolicyLink.addEventListener('click', function(event) {
      event.preventDefault(); // Prevents the default behavior of the link
      showPrivacyPolicyPopup();
    });

    // Event listener for clicking the "I Agree" button in the popup
    agreeButton.addEventListener('click', function() {
      hidePrivacyPolicyPopup();
      // Perform any actions needed after the user agrees to the terms
      // ...
    });
  });