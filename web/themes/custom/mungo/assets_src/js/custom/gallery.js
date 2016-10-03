/**
 * @file
 * Inline article gallery.
 */

(function ($, Drupal) {
  Drupal.behaviors.inlineGallery = {
    attach: function (context, settings) {

      var gallery = $('.gallery', context);
      var activeElement = $('<div>', {'class': 'active-element'});
      gallery.prepend(activeElement);

      var items = gallery.find('li');

      var setActive = function (el) {
        items.removeAttr('class');
        el.addClass('active');
        activeElement.html(el.html());
      };

      items.on('click', function () {
        setActive($(this));
      });

      setActive(items.first());
    }
  };
})(jQuery, Drupal);
