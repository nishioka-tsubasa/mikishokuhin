; (function ($) {
    jQuery('body.error404 .contact-header p').text('404 Not Found');
})(jQuery);;

jQuery(function ($) {
  
    /*-------------------------------------------*/
     /*  先生のスライダー（slick slider）
     /*-------------------------------------------*/
     window.addEventListener('load', function() {
       var maxSliderHeight = 0;
       $('.employee').each(function(idx, elem) {
         var sliderHeight = $(elem).height();
         if(maxSliderHeight < sliderHeight) {
           maxSliderHeight = sliderHeight;
         }
       });
       $('.employee').height(maxSliderHeight);
     });
     
    $('#slick').on('init', function(event, slick) {
        $(this).append('<div class="slick-counter"><span class="current"></span> / <span class="total"></span></div>');
        $('.current').text(slick.currentSlide + 1);
        $('.total').text(slick.slideCount);
      })
      $('.employee-list').slick({
        infinite: true,
        autoplay: true,
        centerMode: true,
        slidesToScroll: 1,
        adaptiveHeight: true,
        arrows: true,
        dots: false,
        slidesToShow: 5,
        pauseOnHover: false,
        responsive: [
          {
            breakpoint: 991,
            settings: {
              slidesToShow: 3,
            }
          },
          {
            breakpoint: 575,
            settings: {
              slidesToShow: 1,
            }
          },
        ]
      })
      .on('beforeChange', function(event, slick, currentSlide, nextSlide) {
        $('.current').text(nextSlide + 1);
      });
  
  }); //jQuery(function ($)
  