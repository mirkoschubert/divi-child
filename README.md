# Divi Child Theme

<p align="center" style="margin-bottom: 2rem;"><img src="divi-child-3-logo.webp" alt="Divi Child Theme 3" width=200 height="200"></p>

<p align="center"> A feature-rich WordPress child theme for <a href="https://www.elegantthemes.com/gallery/divi/" target="_blank">Divi</a> by ElegantThemes. It provides privacy and security hardening, page speed optimizations, accessibility improvements, Divi bug fixes, local font management, analytics integration, and much more — all configurable through a modern React-based admin panel.</p>

<p align="center"><a href="https://www.buymeacoffee.com/musikuss" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-green.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" ></a></p>

## Requirements

- PHP 8.0+
- WordPress 6.3+
- [Divi Theme](https://www.elegantthemes.com/gallery/divi/) by ElegantThemes

## Features

All features are organized into modules and can be toggled individually in the admin panel under **Divi Child Settings**.

### Privacy & Security

- Make links in comments truly external (`target="_blank"` with proper `rel` attributes)
- Remove commentor's IP address from stored comments
- Disable WordPress Emojis and related DNS prefetching
- Disable oEmbeds
- Remove global DNS Prefetching
- Disable WordPress REST API metadata
- Disable XML-RPC for security
- Track and display last login time in the users table 𝗡𝗘𝗪
- Disable author archives (redirects to 404) 𝗡𝗘𝗪
- Obfuscate author slugs to prevent user enumeration 𝗡𝗘𝗪

### Page Speed

- Disable page pingback
- Remove Dashicons from the frontend (except for logged-in admins)
- Remove CSS and JS version query strings
- Remove Shortlink from head
- Preload fonts with configurable font list

### Accessibility

- Add ARIA support to all relevant elements
- Make main navigation fully keyboard accessible
- Focus management for all clickable elements
- Tag external links for assistive technology
- Add a skip link to the page
- Accessible scroll-to-top button (turn off the Divi back-to-top button!)
- Fix the viewport meta tag
- Fix WordPress screenreader text
- Underline all links except headlines and social icons
- Optimize forms for accessibility (Contact Form, Comment Form, Forminator)
- Respect `prefers-reduced-motion` user preference 𝗡𝗘𝗪
- Customizable text selection highlight colors 𝗡𝗘𝗪
- Improved slider navigation spacing 𝗡𝗘𝗪

### Divi Bug Fixes

- Remove Divi support center scripts from frontend (Divi 3.20.1)
- Fix display errors in Theme Builder (Divi 4.0 up to 4.12)
- Re-enable fixed navigation bar option for global Theme Builder headers (Divi 4.0+)
- Customizable fixed header height in pixels
- Fix logo image sizing in Theme Builder (Divi 4.6.6)
- **Split Section Fix is now deprecated and will be removed at v3.2!** 𝗡𝗘𝗪

### Administration

- Duplicate posts and pages with one click 𝗡𝗘𝗪
- Duplicate Divi library items 𝗡𝗘𝗪
- Disable Projects custom post type including categories and tags
- Disable email notifications for plugin and theme auto-updates
- Enable infinite scroll in the media library 𝗡𝗘𝗪
- Upload SVG files (with sanitization)
- Upload WebP files
- Upload AVIF files 𝗡𝗘𝗪
- Enable hyphenation for the whole website
- Set the mobile menu breakpoint to 1280px (landscape tablet)
- Enable fullscreen mode for the mobile menu
- Disable Divi upsell promotions 𝗡𝗘𝗪
- Disable Divi AI features 𝗡𝗘𝗪
- Set Divi Builder as default editor for new posts/pages 𝗡𝗘𝗪
- Open external links in new tab with configurable `rel` attributes 𝗡𝗘𝗪

### Local Fonts

- Disable Divi Google Fonts and serve fonts locally for GDPR compliance 𝗡𝗘𝗪
- Select and download Google Fonts directly from the admin panel 𝗡𝗘𝗪
- Configure `font-display` CSS property (auto, block, swap, fallback, optional) 𝗡𝗘𝗪
- Automatic weekly font updates via cron job 𝗡𝗘𝗪
- Support for multiple font weights and styles 𝗡𝗘𝗪
- Automatic cleanup of orphaned font files 𝗡𝗘𝗪

### Umami Analytics

- Integrate [Umami](https://umami.is/) as a privacy-focused analytics solution 𝗡𝗘𝗪
- Configure website ID and Umami instance domain 𝗡𝗘𝗪
- Exclude logged-in users from tracking 𝗡𝗘𝗪
- Enable custom event tracking 𝗡𝗘𝗪
- Track elements by CSS ID 𝗡𝗘𝗪

### System

- Display environment badge in the admin bar (Local / Dev / Staging / Live) 𝗡𝗘𝗪
- Show a warning when search engine visibility is blocked 𝗡𝗘𝗪
- System status in the WordPress dashboard ("At a Glance") 𝗡𝗘𝗪
- Display PHP, WordPress, and Divi version info 𝗡𝗘𝗪
- Detect image format support (WebP, AVIF, SVG) 𝗡𝗘𝗪

### Login Customization

- Replace the WordPress logo on the login page with your site icon 𝗡𝗘𝗪
- Customize the login logo width 𝗡𝗘𝗪
- Link the login logo to your homepage 𝗡𝗘𝗪
- Set a custom background image for the login page 𝗡𝗘𝗪

## Installation

1. Download the [latest release](https://github.com/mirkoschubert/divi-child/releases/latest/) or clone this repository into `/wp-content/themes/`.
2. Run `composer install` in the theme directory to install dependencies.
3. Activate the child theme in WordPress under **Appearance > Themes**.
4. Configure the modules under **Divi Child Settings** in the admin panel.

**Upgrading from v2.x:** The theme automatically migrates your settings when you activate v3.0.0. It is still recommended to back up your settings before upgrading.

## CSS

The child theme adds a `.child` body class. Use this class as a prefix for your custom CSS in `style.css`:

```css
.child p {
  line-height: 1.6;
}
```

## Updating

The Divi Child Theme does not support automatic updates. To update manually:

1. Download the [latest version](https://github.com/mirkoschubert/divi-child/releases/latest/).
2. Upload the new theme to `/wp-content/themes/` — **do not overwrite the old version yet**.
3. Copy your custom CSS from `style.css` and any custom JavaScript from `/assets/js/main.js`.
4. Activate the new version. Your module settings will be migrated automatically.
5. Verify everything works, then delete the old version.

You may need the [Customizer Export/Import](https://wordpress.org/plugins/customizer-export-import/) plugin to transfer customizer settings.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a detailed history of all versions.

## License

GNU General Public License v3.0 — see [LICENSE](https://www.gnu.org/licenses/gpl-3.0.html).
