/**
 * @file
 * Trigger polyfill to support object-fit on IE.
 *
 * Polyfill from https://github.com/bfred-it/object-fit-images/.
 */

// eslint-disable-next-line no-unused-vars
(function ($, Drupal) {
  var images = $(".full-width-image-container img");
  // eslint-disable-next-line no-undef
  objectFitImages(images);
})(jQuery, Drupal);
