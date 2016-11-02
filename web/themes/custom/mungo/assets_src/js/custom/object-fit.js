/**
 * @file
 * Trigger polyfill to support object-fit on IE.
 *
 * Polyfill from https://github.com/bfred-it/object-fit-images/.
 */
(function ($, Drupal) {
  var images = $('.full-width-image-container img');
  objectFitImages(images);
})(jQuery, Drupal);
