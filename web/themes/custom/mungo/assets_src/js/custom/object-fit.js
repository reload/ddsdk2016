/**
 * @file
 * Trigger polyfill to support object-fit on IE.
 *
 * Polyfill from https://github.com/anselmh/object-fit.
 */

objectFit.polyfill({
  selector: 'img',
  fittype: 'cover',
  disableCrossDomain: 'true'
});
