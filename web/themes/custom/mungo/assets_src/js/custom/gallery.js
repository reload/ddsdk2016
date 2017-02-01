/**
 * @file
 * Inline article gallery.
 */

(function ($, Drupal) {
  Drupal.behaviors.inlineGallery = {
    attach: function (context, settings) {

      var gallery = $('.dds-gallery', context);

      $('.dds-gallery', context).each(function () {
        // Find the inner list of images and have flexslider process it.
        var slider = $('.dds-gallery__slider', $(this));
        slider.flexslider({
          animation: "slide",
          // Don't show bullets for each image.
          controlNav: false,
          // When reaching the end, loop (ie. show an infinite slideshow).
          animationLoop: true,
          // Don't autoplay.
          slideshow: false
        });
      });
    }
  };
})(jQuery, Drupal);
