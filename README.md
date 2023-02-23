# Divi GDPR Child Theme

This piece of software is a WordPress child theme boilerplate for Divi. It aims to secure the site, to configure it to meet the GDPR requirements and to optimize it for a better page speed. In addition there are some bug fixes for the Divi Theme itself.

<p><a href="https://www.buymeacoffee.com/musikuss" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-green.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" ></a></p>

**GDPR Features:**

* Localize Google Fonts (or in fact any web font)
* Make links in the comments truely external
* Remove the commentor's IP (old ones have to be removed by hand)
* Disable oEmbeds (old ones have to be removed by hand)
* Disable WordPress Emojis (in every modern browser Emojis will be displayed anyway)
* Remove global DNS Prefetching
* Hide WordPress REST API meta data for security reasons

**Page Speed Tweaks:**

* Disable page pingback
* Remove Dashicons from the frontend
* Remove CSS and JS query strings
* Remove Shortlink from head
* Preload some fonts (or other files)

**Accessibility Tweaks:**

* Fix the viewport meta (for Lighthouse)

**Divi Bug Fixes:**

* Remove Divi support center scripts from frontend (Divi 3.20.1)
* CSS Split Section Fix for alternating sections of image and text (responsive)
* Fix display errors in Theme Builder (Divi 4.0 up to 4.12)
* Re-enable fixed navigation bar option when a global header in Theme builder is active (Divi 4.0 and up)
* Set a `.split-section-fix` class for swapping text and image for tablet or lower (see [Split Section Fix](#user-content-split-section-fix))

**Micellaneous features:**

* Disable Projects custom post type as well as categories and tags
* Disable email notifications for plugin and theme auto-updates
* Restrict email notifications for core updates (only errors will be sent)
* Enable to upload SVG files
* Enable to upload WebP files
* Enable hyphenation for the whole website
* Set the breakpoint to 1280px for using mobile menu in landscape mode on a tablet
* Enable fullscreen mode for the mobile menu

The next steps will be to bring some CSS hacks to the admin panel as well and to automate the Google Font localization. There will be more explanation for non technical users in the admin panel in the future. So have fun and stay tuned!

## Instructions

This is a child theme adjusted to the [Divi theme](https://www.elegantthemes.com/gallery/divi/) by ElegantThemes **only**! If you want to create your own child theme for any other WordPress theme, please use my [GDPR theme](https://github.com/mirkoschubert/gdpr-child/) as a boilerplate.

### CSS

In order to create clean code and a descent inheritance, the child theme uses a `.child` body class once you activated the child theme. If you want to append your own CSS code to the `style.css`, you should use this class as a prefix, e.g.:

```css
.child p {
  line-height: 1.6;
}
```

### Split Section Fix

If you have any split sections (two column rows with alternating image and text) on your Divi site you know the display errors on mobile devices. Instead of showing every text beneath the image it rotates those sections on mobile as well, leaving you with nasty text-text and image-image combinations.

With my split section fix you only have to set the class `.split-section-fix` in the affected section and everything looks fine. Even the images don't scale down and get a pleasing 16:9 aspect ration on mobile devices by default.

### Theme Builder Header Hack

With Divi 4 the all new theme builder has arrived! Now there's a possibility to set up a global header with a fixed height.

But ElegantThemes sadly disabled the fixed navigation bar option in the Divi theme options for those global headers.

With my Theme Builder Header Hack we bring this back and fix some display errors in the theme builder as well. You don't have to do anything once the child theme is activated.

Feel free to use one of my [Global Header Layouts](https://gist.github.com/mirkoschubert/05f938d6a5edc0001b7aa855d6d38ef6) to import a minimal global header with a fixed height!

**Update v1.3.0:** Since the release of Divi 4 many changes were made, bringing some bugs to my theme builder hack. With v1.3.0 I hopefully fixed all of them and changed my hack in a way, that minor changes in Divi don't affect the Theme Builder Hack that much. If you find any bugs or display failures, please create an [issue](https://github.com/mirkoschubert/divi-child/issues) here on Github!

### Fonts

In Divi you actually can turn off Google Fonts by switching off `Use Google Fonts` under `/Settings/Theme Options/General/`. Then you only see fonts from the [CSS font stack](https://www.cssfontstack.com/), but you can upload your own fonts manually.

But you can also use this child theme to organize your fonts. For Google Fonts simply use the [google-webfonts-helper](https://gwfh.mranftl.com/fonts), copy the downloaded fonts to the `/fonts/` directory and edit the `/assets/css/fonts.css`.

**NOTE:** To use the `TODO` file you should edit it with VSCode and the `Todo+` extension. Please read their documentation for usage information.

### Updating the Divi Child Theme

The purpose of a child theme is to maintain changes even if the parent theme gets updated. So the Divi Child Theme will never have automatic updates. But if you want to use new functions, you can update the child theme manually:

1. Download the [latest version](https://github.com/mirkoschubert/divi-child/releases/latest/) of the Divi child theme.
2. Unzip it and upload the child theme via FTP to your WordPress installation into the directory `/wp-content/themes/`. You should keep the old version for now - **do not overwrite the old child theme yet!**
3. Now you have to copy your custom code from the old to the new child theme. This will be mainly parts of the `styles.css` and `/assets/js/main.js`.
4. If you have localized any font you should copy the contents of the `/assets/css/fonts.css` file as well as every file in the `/assets/fonts/` folder.
5. If you used a version prior to `v2.0` you should also copy the font list for preloading from the `functions.php` to the list in the admin panel once you activated the new child theme.
6. Check everything and then activate the new version of the Divi child theme.
7. Check again if everything works and delete the old version of the child theme afterwards.

You may have to use the [Customizer Export/Import](https://wordpress.org/plugins/customizer-export-import/) plugin for copying the customizer settings to the new child theme.
