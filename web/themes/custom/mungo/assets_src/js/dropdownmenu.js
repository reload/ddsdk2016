/**
 * @file
 * Handle dropdown specific click on first menu level.
 */

(function ($, Drupal) {
  Drupal.behaviors.dropdownmenu = {
    // If set to true the named functionality won't run.
    killSwitches: {
      scrollClose: false,
    },

    // Close main menu and search-box.
    closeAllMenus: function (context) {
      // Close all menus.
      $("ul.menu--main .menu-item--expanded", context).removeClass("opened");
      // Close the search-box.
      $("#search-field-expose").prop("checked", false).change();
    },

    // eslint-disable-next-line no-unused-vars
    attach: function (context, settings) {
      // When the user click outside an active menu we want all menus to close.
      // An important note here: If you click any menu-item make sure the click
      // event does not propagate, otherwise the window-handler will close it.
      $(window).click(function () {
        Drupal.behaviors.dropdownmenu.closeAllMenus(context);
      });

      // Disable menus on scroll.
      $(window).scroll(function () {
        // Close all menus unless the kill-switch is enabled.
        if (!Drupal.behaviors.dropdownmenu.killSwitches.scrollClose) {
          Drupal.behaviors.dropdownmenu.closeAllMenus(context);
        }
      });

      // When the user clicks a top-level menu we want to toggle it's open-state
      // and close all other menus.
      $("ul.menu--main .menu-item--expanded", context)
        .on("click", function (e) {
          // Close the search-box.
          $("#block-mungo-search input#search-field-expose")
            .prop("checked", false)
            .change();

          // Close all other main-menu items.
          $(this).siblings().removeClass("opened");

          // Toggle the state of the clicked menu.
          $(this).toggleClass("opened");
          // Make sure the click-event does not make it to the window and closes
          // the menu again.
          e.stopPropagation();
        })
        .children("a")
        .on("click", function (e) {
          // Disable click for any immediate A-tag children, which only can be the
          // first level.
          e.preventDefault();
        });

      // Toggle main-menu items on hover.
      $("ul.menu--main .menu-item--expanded", context).hover(
        // Hover-in.
        function () {
          // Menu is only hoverable when the search-field is not visible.
          if (
            $("#block-mungo-search input#search-field-expose").prop("checked")
          ) {
            return;
          }
          // On hover on a top-level item (or any decendants) add the open class
          // to the hovered item and remove it from all of its siblings.
          $(this).addClass("opened");
        },

        // Hover-out.
        function () {
          // Menu is only hoverable when the search-field is not visible.
          if (
            $("#block-mungo-search input#search-field-expose").prop("checked")
          ) {
            return;
          }
          // When leaving a top-level item (or any decendants) remove the open
          // class from the top-level item and.
          $(this).removeClass("opened");
        },
      );

      // React on the main menu being opened on mobile where the trigger is a
      // checkbox based burger-icon.
      $("#main-menu-toggle").on("change", function () {
        if ($(this).prop("checked")) {
          // Menu is being enabled, disable auto-close on scroll.
          Drupal.behaviors.dropdownmenu.killSwitches.scrollClose = true;
          $("body").addClass("is-modal-open");
        } else {
          $("body").removeClass("is-modal-open");
          // Menu is being closed, renable auto-close on scroll.
          Drupal.behaviors.dropdownmenu.killSwitches.scrollClose = true;
        }
      });

      // React on changes to the search-menu toggle.
      $("#search-field-expose").on("change", function () {
        if ($(this).prop("checked")) {
          // When toggled on:
          // First disable the scroll-close functionallity temporarily - we're
          // going to put focus on a field which may trigger a scroll event.
          Drupal.behaviors.dropdownmenu.killSwitches.scrollClose = true;
          window.setTimeout(function () {
            Drupal.behaviors.dropdownmenu.killSwitches.scrollClose = false;
          }, 1000);

          // Make the search-field active.
          $("input.form-search").focus();

          // Close all other menus.
          $("ul.menu--main .menu-item--expanded").removeClass("opened");
          $("#main-menu-toggle").prop("checked", false).change();
        } else {
          // When toggled off, make sure the search-form is no longer active.
          $("input.form-search").blur();
        }
      });

      // Disable event-propagation on anything that we want to be clickable
      // without closing menus. That is, all menu-items, toggle-elements, etc.
      $(
        "label[for='search-field-expose'],#search-block-form" +
          ",#search-field-expose",
      ).on("click", function (e) {
        e.stopPropagation();
      });
    },
  };
})(jQuery, Drupal);
