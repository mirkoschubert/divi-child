/**
 * A11y Module JavaScript
 * Modularer Ansatz mit Feature-Aktivierung basierend auf a11yOptions
 */
;(function ($) {
  "use strict"

  // Prüfe, ob Feature aktiviert ist
  function isFeatureEnabled(feature) {
    return a11yOptions && a11yOptions[feature] === "1"
  }

  // Hilfsfunktion zum Verstecken von ARIA-Elementen
  function hideAriaElement(element) {
    const $element = $(element)
    $(element).attr("aria-hidden", "true")

    for (const child of $element.children()) {
      hideAriaElement(child)
    }
  }

  /**
   * Skip Link Funktionalität
   */
  function setupSkipLink() {
    $("a.skip-link").click(function (e) {
      e.preventDefault()
      const targetId = this.getAttribute("href").substring(1)
      const targetElement = document.getElementById(targetId)
      if (targetElement) {
        targetElement.setAttribute("tabindex", "0")
        targetElement.focus()
      }
    })
  }

  /**
   * Scroll-To-Top Button Funktionalität
   */
  function setupScrollToTop() {
    const scrollToTopButton = document.getElementById("js-top")
    if (scrollToTopButton) {
      const $scrollToTopButton = $("#js-top")

      // Show/hide button on scroll
      const scrollFunc = () => {
        let y = $(window).scrollTop()
        if (y > 0) {
          $scrollToTopButton.attr("class", "top-link show")
        } else {
          $scrollToTopButton.attr("class", "top-link hide")
        }
      }

      $(window).on("scroll", scrollFunc)

      // Scroll to top function
      scrollToTopButton.onclick = function (e) {
        e.preventDefault()

        const scrollToTop = () => {
          const c =
            document.documentElement.scrollTop || document.body.scrollTop
          if (c > 0) {
            window.requestAnimationFrame(scrollToTop)
            window.scrollTo(0, c - c / 10)
          }
        }
        scrollToTop()
      }
    }
  }

  /**
   * Optimized Fokus Elements
   */
  function setupFocusElements() {
    let lastKey = new Date()
    let lastClick = new Date()

    $(document).on("focusin", function (e) {
      $(".keyboard-outline").removeClass("keyboard-outline")
      const wasByKeyboard = lastClick < lastKey
      if (wasByKeyboard) {
        $(e.target).addClass("keyboard-outline")
      }
    })

    $(document).on("mousedown", function () {
      lastClick = new Date()
    })

    $(document).on("keydown", function () {
      lastKey = new Date()
    })

    // Fix clickable elements
    $(".et_pb_module.et_clickable").attr("role", "button").attr("tabindex", "0")
    $(".et_pb_blurb.et_clickable .et_pb_blurb_container").attr("tabindex", "0")

    $(".et_clickable").on("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault()
        $(this).click()
      }
    })

    // Custom outline color for keyboard navigation
    const outline = ((window || {})._a11y || {}).active_outline_color || false
    if (outline) {
      $(document).on("focusin", 'input,button,a[role="tab"]', function () {
        const $me = $(this)
        setTimeout(function () {
          if ($me.is(".keyboard-outline")) {
            $me.css("outline-color", outline)
          }
        })
      })
    }
  }

  /**
   * Keyboard Navigation for the Main Menu
   */
  function setupKeyboardNavigation() {
    $(".et-menu > li").on("focusout", function () {
      $(this).removeClass("et-hover")
    })

    if ($(".menu-item-has-children > a").length) {
      $(".menu-item-has-children > a").addClass("a11y-submenu")
      $(".menu-item-has-children > a").attr({
        "aria-expanded": "false",
        "aria-haspopup": "true",
      })
    }

    $(".menu-item a").on("focus", function () {
      $(this).siblings(".a11y-submenu").attr("aria-expanded", "true")
      $(this).parents(".menu-item-has-children").addClass("et-hover")
      $(this).siblings(".sub-menu").addClass("a11y-submenu-show")
      $(this).trigger("mouseenter")
    })

    $(".menu-item-has-children a").on("focusout", function () {
      if ($(this).parent().not(".menu-item-has-children").is(":last-child")) {
        $(this)
          .parents(".menu-item-has-children")
          .children(".a11y-submenu")
          .attr("aria-expanded", "false")
          .trigger("mouseleave")
          .siblings(".sub-menu")
          .removeClass("a11y-submenu-show")
      }
    })

    // ESC key to close submenu
    $(".menu-item-has-children a").keyup(function (event) {
      if (event.keyCode === 27) {
        var menuParent = $(this).parents(".menu-item-has-children").last()
        if (menuParent.length) {
          menuParent.children("a").focus()
          menuParent
            .find(".a11y-submenu")
            .attr("aria-expanded", "false")
            .trigger("mouseleave")
            .siblings(".sub-menu")
            .removeClass("a11y-submenu-show")
        }
      }
    })

    // Mobile menu Aria support
    $(".mobile_menu_bar").attr({
      role: "button",
      "aria-expanded": "false",
      "aria-label": "Menu",
      tabindex: 0,
    })

    $(".mobile_menu_bar").on("click", function () {
      if ($(this).hasClass("a11y-mobile-menu-open")) {
        $(this)
          .removeClass("a11y-mobile-menu-open")
          .attr("aria-expanded", "false")
      } else {
        $(this).addClass("a11y-mobile-menu-open").attr("aria-expanded", "true")
      }
    })

    // Allows mobile menu to be opened with keyboard
    $(".mobile_menu_bar").keyup(function (event) {
      if (event.keyCode === 13 || event.keyCode === 32) {
        $(".mobile_menu_bar").click()
      }
    })

    // Allows mobile menu to be closed with keyboard (ESC)
    $(document).keyup(function (event) {
      if (event.keyCode === 27) {
        if ($("#et_mobile_nav_menu .mobile_nav").hasClass("opened")) {
          $(".mobile_menu_bar").click()
        }
      }
    })

    // Closes mobile menu when it loses focus
    $(document).on("focusin", function () {
      if ($("#et_mobile_nav_menu .mobile_nav").hasClass("opened")) {
        if (!$("#et_mobile_nav_menu .et_mobile_menu :focus").length) {
          $("#et_mobile_nav_menu .mobile_menu_bar").click()
        }
      }
    })
  }

  /**
   * ARIA support for various modules
   */
  function setupAriaSupport() {
    // Aria hidden icons
    $("#et_top_search, .et_close_search_field, .et_pb_main_blurb_image").attr(
      "aria-hidden",
      "true"
    )

    /**
     * Tabs Tweaks
     * @divi-module Tabs
     */
    $(".et_pb_tabs_controls").each(function () {
      $(this).attr("role", "tablist")
    })

    $(".et_pb_tabs_controls li").each(function () {
      $(this).attr("role", "presentation")
    })

    $(".et_pb_tabs_controls a").each(function () {
      $(this).attr("role", "tab")
    })

    $(".et_pb_tab").each(function () {
      $(this).attr("role", "tabpanel")
    })

    $(".et_pb_tabs_controls li:not(.et_pb_tab_active) a").each(function () {
      $(this).attr({
        "aria-selected": "false",
        "aria-expanded": "false",
        tabindex: -1,
      })
    })

    $(".et_pb_tabs_controls li.et_pb_tab_active a").each(function () {
      $(this).attr({
        "aria-selected": "true",
        "aria-expanded": "true",
        tabindex: 0,
      })
    })

    // Add unique ID to tab controls.
    $(".et_pb_tabs_controls a").each(function (e) {
      $(this).attr({
        id: "et_pb_tab_control_" + e,
        "aria-controls": "et_pb_tab_panel_" + e,
      })
    })

    // Add unique ID to tab panels.
    $(".et_pb_tab").each(function (e) {
      $(this).attr({
        id: "et_pb_tab_panel_" + e,
        "aria-labelledby": "et_pb_tab_control_" + e,
      })
    })

    // Set initial inactive tab panels to aria-hidden="false".
    $(".et_pb_tab.et_pb_active_content").each(function () {
      $(this).attr("aria-hidden", "false")
    })

    // Set initial inactive tab panels to aria-hidden="true".
    $(".et_pb_tab:not(.et_pb_active_content)").each(function () {
      $(this).attr("aria-hidden", "true")
    })

    // Add unique ID to tab module.
    // Need to use data attribute because a regular ID somehow interferes with Divi.
    $(".et_pb_tabs").each(function (e) {
      $(this).attr("data-a11y-id", "et_pb_tab_module_" + e)
    })

    // Update aria-selected attribute when tab is clicked
    $(".et_pb_tabs_controls a").on("click", function () {
      const id = $(this).attr("id")
      const namespace = $(this).closest(".et_pb_tabs").attr("data-a11y-id") // Used as a selector to scope changes to current module.
      // Reset all tab controls to be aria-selected="false" & aria-expanded="false".
      $('[data-a11y-id="' + namespace + '"] .et_pb_tabs_controls a').attr({
        "aria-selected": "false",
        "aria-expanded": "false",
        tabindex: -1,
      })
      // Make active tab control aria-selected="true" & aria-expanded="true".
      $(this).attr({
        "aria-selected": "true",
        "aria-expanded": "true",
        tabindex: 0,
      })
      // Reset all tabs to be aria-hidden="true".
      $("#" + namespace + " .et_pb_tab").attr("aria-hidden", "true")
      // Label active tab panel as aria-hidden="false".
      $('[aria-labelledby="' + id + '"]').attr("aria-hidden", "false")
    })

    // Arrow navigation for tab modules
    $(".et_pb_tabs_controls a").keyup(function (e) {
      const namespace = $(this).closest(".et_pb_tabs").attr("data-a11y-id")
      const module = $('[data-a11y-id="' + namespace + '"]')
      if (e.which === 39) {
        // Right.
        const next = module.find("li.et_pb_tab_active").next()
        if (next.length > 0) {
          next.find("a").trigger("click")
        } else {
          module.find("li:first a").trigger("click")
        }
      } else if (e.which === 37) {
        // Left.
        const next = module.find("li.et_pb_tab_active").prev()
        if (next.length > 0) {
          next.find("a").trigger("click")
        } else {
          module.find("li:last a").trigger("click")
        }
      }
      $(".et_pb_tabs_controls a").removeClass("keyboard-outline")
      module.find("li.et_pb_tab_active a").addClass("keyboard-outline")
    })

    // Add aria-haspopup="true" support to submenus
    $("ul.sub-menu .menu-item a").each(function () {
      $(this).attr({
        "aria-haspopup": "true",
      })
    })

    /**
     * Add unique ID to search module.
     * Need to use data attribute because a regular ID somehow interferes with Divi.
     * @divi-module Search
     */
    $(".et_pb_search").each(function (e) {
      $(this).attr("data-a11y-id", "et_pb_search_module_" + e)
    })

    /**
     * Add aria-support to reCAPTCHA
     * @divi-module Contact Form
     */
    $("#g-recaptcha-response").each(function () {
      $(this).attr({
        "aria-hidden": "true",
        "aria-label": "do not use",
        "aria-readonly": "true",
      })
    })

    /**
     * Add aria-required="true" to inputs.
     * @divi-module Contact Form
     */
    $('[data-required_mark="required"]').each(function () {
      $(this).attr("aria-required", "true")
    })

    /**
     * Hide hidden error field on contact form.
     * @divi-module Contact Form
     */
    $(".et_pb_contactform_validate_field").attr("type", "hidden")

    /**
     * Add alert role to error or success contact form message
     * @divi-module Contact Form
     */
    $(".et-pb-contact-message").attr("role", "alert")

    /**
     * Add main role to main-content
     */
    $("#main-content").attr("role", "main")

    /**
     * Add contentinfo role to footer
     */
    $("footer.et-l--footer").attr("role", "contentinfo")

    /**
     * Add aria-label="x".
     * @divi-module Fullwidth Header, comment-wrap
     */
    $(".et_pb_fullwidth_header").each(function (e) {
      $(this).attr("aria-label", "Wide Header" + e)
    })
    $("#comment-wrap").attr("aria-label", "Comments")

    // Add role="link" to all links
    $(
      "a:not(.et-social-icon a, .wp-block-button__link, figure a, .et_pb_button, .et_pb_video_play a, .et_pb_tabs_controls a)"
    ).each(function () {
      $(this).attr({
        role: "link",
      })
    })

    // Add role="button" to clickable elements
    $(
      "#et_search_icon, .et_close_search_field, #et_mobile_nav_menu, #searchsubmit, .icon, .wp-block-button__link, .et_pb_button, .et_pb_video_play a"
    ).each(function () {
      $(this).attr({
        role: "button",
      })
    })

    /**
     * Hide manually disabled ARIA elements
     */
    $(".aria-hidden").each(function (index, element) {
      hideAriaElement(element)
    })
  }

  /**
   * External Links with aria-label
   * @divi-module Blog, Post, Comments
   */
  function setupExternalLinks() {
    $("a").each(function () {
      let $el = $(this)
      let target = $el.attr("target")
      let text = $el.text().trim() || $el.attr("title") || "unknown"

      if (target == "_blank" || target == "blank") {
        $el.attr("aria-label", "Link to " + text + " opens in a new window")
      }
    })
  }

  /**
   * Formular-Optimierungen
   */
  function setupFormOptimizations() {
    setTimeout(function () {
      $("form#commentform label").removeAttr("style")
      $(
        "div#minimal-contact-form form input#name, div#minimal-contact-form form input#email, div#minimal-contact-form form textarea#message"
      ).attr("aria-required", "true")
    }, 100)

    $('div.forminator-field-radio div[role="radiogroup"]')
      .removeAttr("aria-labelledby")
      .removeAttr("aria-describedby")
    $('div.forminator-field-radio div[role="radiogroup"] input[type="radio"]')
      .removeAttr("aria-labelledby")
      .removeAttr("aria-describedby")
    $('div.forminator-field-checkbox input[type="checkbox"]').removeAttr(
      "aria-labelledby"
    )
    $("div.forminator-field-checkbox div.forminator-field").removeAttr(
      "aria-labelledby"
    )
    $('div.forminator-field-radio div[role="radiogroup"] span').removeAttr("ID")
  }

  /**
   * Enhanced ARIA Labeling for Social Icons and Sliders
   */
  function setupEnhancedAriaLabeling() {
    // Social Icons Module Enhancement
    $(".et-social-icon").each(function(){  
      const $this = $(this)
      const classList = $this.attr("class")
      
      if (classList) {
        // Extract social network name
        let socialName = classList.replace("et-social-icon", "").trim()
                                 .replace("et-social-", "").trim()
        
        if (socialName) {
          // Clean up network name for better readability
          socialName = socialName.replace("-", " ")
          socialName = socialName.replace("google plus", "Google Plus")
          
          const ariaLabel = wp.i18n.sprintf(
            wp.i18n.__('Visit our %s page', 'divi-child'),
            socialName
          )
          $this.find("a").attr("aria-label", ariaLabel)
        }
      }
    })

    // Individual Social Media Icons in Social Media Module
    $(".et_pb_social_icon > a.icon").each(function(){
      const $this = $(this)
      const titleText = $this.attr("title")
      
      if (titleText) {
        $this.attr("aria-label", titleText)
      }
    })

    // Slider Navigation Controls
    $("a.et-pb-arrow-prev").each(function(){
      $(this).attr({
        "aria-label": wp.i18n.__('Previous slide', 'divi-child'),
        "role": "button"
      })
    })

    $("a.et-pb-arrow-next").each(function(){
      $(this).attr({
        "aria-label": wp.i18n.__('Next slide', 'divi-child'), 
        "role": "button"
      })
    })

    // Swiper Controls
    $(".swiper-button-prev").attr("aria-label", wp.i18n.__('Previous slide', 'divi-child'))
    $(".swiper-button-next").attr("aria-label", wp.i18n.__('Next slide', 'divi-child'))

    // Video Play Buttons
    $("a.et_pb_video_play").each(function(){
      $(this).attr("aria-label", wp.i18n.__('Play video', 'divi-child'))
    })
  }

  /**
   * Fix Duplicate Menu IDs
   */
  function fixDuplicateMenuIds() {
    const seenIds = new Set()
    
    $("[id]").each(function() {
      const currentId = $(this).attr("id")
      
      if (seenIds.has(currentId)) {
        // Remove duplicate ID
        $(this).removeAttr("id")
      } else {
        seenIds.add(currentId)
      }
    })
  }

  /**
   * Accordion & Toggle Module Support
   */
  function setupAccordionAndToggle() {
    $(".et_pb_toggle").each(function (index) {
      var $toggle = $(this)
      var $title = $toggle.find(".et_pb_toggle_title")
      var $panel = $toggle.find(".et_pb_toggle_content")
      var isAccordion = $toggle.hasClass("et_pb_accordion_item")

      $title.attr("role", "button")
      $title.attr("tabindex", 0)
      $title.attr("aria-controls", "et_pb_toggle_content_" + index)
      $panel.attr("id", "et_pb_toggle_content_" + index)

      if ($toggle.hasClass("et_pb_toggle_open")) {
        $title.attr("aria-expanded", true)

        if (isAccordion) $title.attr("aria-disabled", true)
      } else {
        $title.attr("aria-expanded", false)

        if (isAccordion) $title.removeAttr("aria-disabled")
      }
    })

    // Prevent spacebar from scolling page when toggle & accordion have focus.
    $(".et_pb_toggle_title").on("keydown", function (e) {
      if (e.which === 32) {
        e.preventDefault()
      }
    })

    // Expand Accordion & Toggle modules when enter or spacebar are pressed while focused.
    $(document).on("keyup", function (e) {
      // Spacebar & Enter.
      if (e.which === 13 || e.which === 32) {
        $(".et_pb_toggle_title:focus").trigger("click")
      }
    })

    // Set aria attributes of Accordion & Toggle modules when one is clicked.
    $(".et_pb_toggle_title").on("click", function () {
      var $clickedToggleTitle = $(this)
      var $clickedToggle = $clickedToggleTitle.parent()
      var isAccordion = $clickedToggle.hasClass("et_pb_accordion_item")

      if (isAccordion) {
        if (!$clickedToggle.hasClass("et_pb_toggle_open")) {
          var $allSiblingToggles = $clickedToggleTitle
            .closest(".et_pb_accordion")
            .find(".et_pb_toggle")

          $allSiblingToggles.each(function () {
            $toggle = $(this)
            if ($toggle.hasClass("et_pb_toggle_open")) {
              var $openToggleTitle = $toggle.find(".et_pb_toggle_title")

              $openToggleTitle.attr("aria-expanded", false)
              $openToggleTitle.removeAttr("aria-disabled")
            }
          })

          setTimeout(function () {
            $clickedToggleTitle.attr("aria-expanded", true)
            $clickedToggleTitle.attr("aria-disabled", true)
          }, 500)
        }
      } else {
        if ($clickedToggle.hasClass("et_pb_toggle_open")) {
          $clickedToggleTitle.attr("aria-expanded", false)
        } else {
          $clickedToggleTitle.attr("aria-expanded", true)
        }
      }
    })
  }

  // Document Ready - Initialize Features
  $(document).ready(function () {
    if (isFeatureEnabled("skip_link")) {
      setupSkipLink()
    }

    if (isFeatureEnabled("scroll_top")) {
      setupScrollToTop()
    }

    if (isFeatureEnabled("focus_elements")) {
      setupFocusElements()
    }

    if (isFeatureEnabled("nav_keyboard")) {
      setupKeyboardNavigation()
    }

    if (isFeatureEnabled("aria_support")) {
      setupAriaSupport()
      setupAccordionAndToggle()
      setupEnhancedAriaLabeling()
      fixDuplicateMenuIds()
    }

    if (isFeatureEnabled("external_links")) {
      setupExternalLinks()
    }

    if (isFeatureEnabled("optimize_forms")) {
      setupFormOptimizations()
    }
  })
})(jQuery)
