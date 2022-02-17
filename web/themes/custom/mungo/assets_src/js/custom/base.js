/**
 * @file
 * General javascript functionality for the site.
 */

(function ($, Drupal) {
  Drupal.behaviors.move_header = {
    // eslint-disable-next-line no-unused-vars
    attach: function (context, settings) {
      /**
       * Position the site menu when toolbar is active.
       */
      $(document).on(
        "drupalViewportOffsetChange.toolbar",
        function (event, offsets) {
          $("header.header-fixed").css("top", offsets.top);
          $("header.header-fixed").css("left", offsets.left);
        }
      );
    },
  };
  /**
   * When found dymo-text class wrap each word in a span tag.
   */
  Drupal.behaviors.dymo_text = {
    // eslint-disable-next-line no-unused-vars
    attach: function (context, settings) {
      $(".dymo-text")
        .each(function () {
          var text = $(this).text().split(" ");
          text = "<span>" + text.join("</span><span>") + "</span>";
          $(this).html(text);
        })
        .addClass("dymo-text-processed");
    },
  };

  Drupal.behaviors.hero_banner_blur = {
    // eslint-disable-next-line no-unused-vars
    attach: function (context, settings) {
      $(".paragraph--type--hero-banner").each(function () {
        var image = $("img", this);
        var links = $("a", this);
        links
          .on("mouseover", function () {
            image.addClass("blured");
          })
          .on("mouseout", function () {
            image.removeClass("blured");
          });
      });
    },
  };

  Drupal.behaviors.browserDetect = {
    // eslint-disable-next-line no-unused-vars
    attach: function (context, settings) {
      var is_chrome = navigator.userAgent.indexOf("Chrome") > -1;
      var is_safari = navigator.userAgent.indexOf("Safari") > -1;

      // Chrome has the string "Safari" in it's user-agent string, but
      // Safari does not have "Chrome" in it's. So, we assume we're looking
      // at a safari-browser if the browser has been identified as safari
      // AND not chrome.
      if (is_chrome && is_safari) {
        is_safari = false;
      }

      // Inject a class we can use to make some safari-specific flexbox
      // fixes.
      if (is_safari) {
        $("body").addClass("is-safari");
      }
    },
  };
})(jQuery, Drupal);
