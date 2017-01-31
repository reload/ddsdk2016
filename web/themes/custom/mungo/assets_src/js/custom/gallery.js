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
          // When reaching the end, bounch back to the start instead of looping.
          animationLoop: false
        });
      });
    }
  };
})(jQuery, Drupal);
