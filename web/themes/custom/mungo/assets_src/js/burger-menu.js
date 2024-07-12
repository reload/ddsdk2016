/**
 * Burger-menu functionality.
 */
Drupal.behaviors.toybox_burger_menu = {
  // eslint-disable-next-line no-unused-vars
  attach: function (context, settings) {
    var that = this;

    this.burgerMenu = context.querySelector(
      ".js-burger-menu:not(.is-initialized)",
    );

    if (!this.burgerMenu) {
      return;
    }

    this.burgerMenu.classList.add("is-initialized");

    this.burgerMenu.addEventListener("click", function (e) {
      if (e.target.classList.contains("js-burger-menu-back")) {
        that.triggerBack(e.target, context);
      }
    });

    var burgerToggles = context.querySelectorAll(
      ".js-burger-menu-toggle:not(.is-initialized)",
    );

    burgerToggles.forEach(function (burgerToggle) {
      burgerToggle.classList.add("is-initialized");

      burgerToggle.addEventListener("click", function () {
        that.toggleBurger(context);
      });
    });

    var labels = this.burgerMenu.querySelectorAll("label:not(.is-initialized)");

    labels.forEach(function (label) {
      label.classList.add("is-initialized");

      label.addEventListener("keyup", function (e) {
        if (e.key === "Enter" || e.keyCode === 13) {
          this.click();
        }
      });
    });

    // Set initial tabindex.
    // We do this with JavaScript, instead of in the template, as we need
    // a good fallback if the browser doesnt have JS enabled.
    this.setTabindex();

    if (document.documentElement.classList.contains("is-burger-initialized")) {
      return;
    }

    document.documentElement.classList.add("is-burger-initialized");
    document.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        document.documentElement.classList.remove("has-open-burger-menu");
        that.setTabindex();
      }
    });
  },

  // Triggered when the user clicks on a "back" link in the menu.
  // When this happens, we'll close our current controller - and also
  // close any other active links below.
  triggerBack: function (trigger, context) {
    var targetID = trigger.getAttribute("data-target-controller-id");

    var targetController = context.querySelector(
      '.js-burger-menu-controller[id="' + targetID + '"]',
    );

    if (!targetController) {
      return;
    }

    // Closing our current controller.
    targetController.checked = false;

    // Finding any active childs below, and closing them too.
    var activeChildControllers = targetController.parentNode.querySelectorAll(
      ".js-burger-menu-menu .js-burger-menu-controller:checked",
    );

    activeChildControllers.forEach(function (childController) {
      childController.checked = false;
    });
  },

  // Toggles the whole burger menu on/off.
  toggleBurger: function (context) {
    document.documentElement.classList.toggle("has-open-burger-menu");

    this.setTabindex();

    this.collapseAllLinks(context);
  },

  // Finds all active links in the burgermenu, and closes them.
  // It does NOT close the actual burger menu.
  collapseAllLinks: function (context) {
    var activeControllers = context.querySelectorAll(
      ".js-burger-menu-controller:checked",
    );

    activeControllers.forEach(function (controller) {
      controller.checked = false;
    });
  },

  // Setting tabindex, by checking if the popup is open.
  // If we don't do this, keyboard-tabbing is still possible when the menu
  // is not visible.
  setTabindex: function () {
    if (!this.burgerMenu) {
      return;
    }

    var focusable = this.burgerMenu.querySelectorAll(
      "label, a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, *[tabindex], *[contenteditable]",
    );

    var isOpen = document.documentElement.classList.contains(
      "has-open-burger-menu",
    );

    var tabindex = isOpen ? 0 : "-1";

    focusable.forEach(function (focusElement) {
      focusElement.setAttribute("tabindex", tabindex);
    });

    // If the menu is open, we'll focus on the first toggle.
    // If the menu is closed, we'll focus on the menu itself, so the user
    // can tab around outside the menu.
    if (isOpen) {
      this.burgerMenu.querySelector(".js-burger-menu-toggle").focus({
        preventScroll: true,
      });
    } else {
      this.burgerMenu.focus({
        preventScroll: true,
      });
    }
  },
};
