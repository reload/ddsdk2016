/**
 * @file
 * Bootstrap typekit.
 */
window.addEventListener("load", () => {
  try {
    // eslint-disable-next-line no-undef
    Typekit.load();
  } catch (e) {
    console.error(e);
  }
});
