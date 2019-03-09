# Divi GDPR Child Theme

This is a WordPress child theme boilerplate for Divi, which aims to secure the site and to configure it to meet the GDPR requirements. The child theme performs the following tasks:

* Localize Google Fonts (or in fact any web font)
* Make links in the comments truely external
* Remove the commentor's IP (old ones have to be removed by hand)
* Disable oEmbeds (old ones have to be removed by hand)
* Disable WordPress Emojis (in every modern browser Emojis will be displayed anyway)
* Remove global DNS Prefetching
* Hide WordPress REST API meta data for security reasons
* Remove Divi support center scripts from frontend (Divi 3.20.1 and up)

## Instructions

This is a child theme adjusted to the [Divi theme](https://www.elegantthemes.com/gallery/divi/) by ElegantThemes **only**! If you want to create your own child theme for any other WordPress theme, please use my [GDPR theme](https://github.com/mirkoschubert/gdpr-child/) as a boilerplate.

### CSS

In order to create clean code and a descent inheritance, the child theme uses a `.child` body class once you activated the child theme. If you want to append your own CSS code to the `style.css`, you should use this class as a prefix, e.g.:

```css
.child p {
  line-height: 1.6
}
```

### Fonts

In Divi you actually can turn off Google Fonts by switching off `Use Google Fonts` under `/Settings/Theme Options/General/`. Then you only see fonts from the [CSS font stack](https://www.cssfontstack.com/), but you can upload your own fonts manually.

But you can also use this child theme to organize your fonts. For Google Fonts simply use the [google-webfonts-helper](https://google-webfonts-helper.herokuapp.com/fonts), copy the downloaded fonts to the `/fonts/` directory and edit the `/css/fonts.css`.

**NOTE:** To use the `TODO` file you should edit it with VSCode and the `Todo+` extension. Please read their documentation for usage information.