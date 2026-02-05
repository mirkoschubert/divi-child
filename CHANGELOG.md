# Changelog

All notable changes to the Divi Child Theme will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.0] - Unreleased

### Added

- Completely new modular architecture with 10 feature modules
- React-based admin panel with TypeScript
- REST API for module management and settings
- Composer-based PSR-4 autoloading with PHP namespaces
- Automatic migration from v2.x to v3.0.0 with backward compatibility
- **New Local Fonts module:** Download and manage Google Fonts locally, automatic font updates via cron, font-display CSS configuration, font variant support, orphaned file cleanup
- **New Umami Analytics module:** Privacy-focused analytics integration, custom event tracking, element-based tracking via CSS IDs, option to exclude logged-in users
- **New System Dashboard module:** Environment badge in admin bar (Local/Dev/Staging/Live), search engine visibility warning, system status widget with PHP/WordPress/Divi version info, image format support detection (WebP, AVIF, SVG)
- **New Login Customization module:** Replace WordPress logo with site icon, customizable logo width, link login logo to homepage, custom login background image
- **New UI Kit module:** Developer reference for all available form field types and dependencies
- **Privacy module (extended):** Disable XML-RPC, track last login and display in users table, disable author archives (redirect to 404), obfuscate author slugs with HMAC-SHA256 encryption
- **Administration module (extended):** Duplicate posts and pages, duplicate Divi library items, infinite scroll in media library, AVIF upload support, disable Divi upsells, disable Divi AI, set Divi Builder as default for new posts/pages, open external links in new tab with configurable rel attributes
- **Accessibility module (extended):** Respect `prefers-reduced-motion` setting, customizable text highlight colors, slider navigation spacing
- **Bugs module (extended):** Logo image sizing fix for Theme Builder (Divi 4.6.6), customizable fixed header height in pixels

### Changed

- Complete code restructuring with OOP architecture and PHP namespaces
- Options now use native booleans instead of `on`/`off` strings
- Minimum PHP version raised to 8.0
- Minimum WordPress version raised to 6.3
- "GDPR" module renamed to "Privacy"
- "Bug Fixes" module renamed to "Bugs"
- "Miscellaneous" module renamed to "Administration"
- Admin panel completely rebuilt with React and TypeScript
- Font management is now fully automated (no more manual font file handling)

## [2.3.0] - 2024-11-28

### Added

- ARIA support for all relevant elements
- Fully keyboard-accessible main navigation
- Focus management for all clickable elements
- Skip link for keyboard navigation
- Accessible scroll-to-top button (turn off the Divi back-to-top button!)
- Fix for WordPress screenreader text
- Underline all links except headlines and social icons
- Form accessibility optimization (Contact Form, Comment Form, Forminator)
- All features are toggleable in the options page

### Changed

- Settings object changed completely — **backup your settings before updating!**

## [2.2.0] - 2023-11-18

### Fixed

- SVG file upload issue resolved

## [2.1.0] - 2023-02-23

### Added

- Every CSS fix is now optional and toggleable
- Option to remove Projects custom post type
- Viewport meta option for better Lighthouse scores

### Fixed

- Fullscreen mobile menu colors can now be customized
- Updated default `modules.woff` location for font preloading
- Theme Builder display error fix is now off by default

### Deprecated

- `.split-section-fix` CSS class (to be removed in next major release)

## [2.0.6] - 2022-07-13

### Added

- CSS class for styling the scrolled mobile navigation menu

### Changed

- Visual improvements to the mobile navigation menu

## [2.0.5] - 2021-03-02

### Fixed

- Hyphenation implementation corrected

## [2.0.4] - 2021-03-02

### Changed

- Better default hyphenation coverage for Safari and Edge

## [2.0.3] - 2021-02-07

### Fixed

- WP CLI compatibility issue resolved

## [2.0.2] - 2021-02-07

### Fixed

- Auto core update email suppression now works correctly

## [2.0.1] - 2020-12-16

### Fixed

- Minor bugfixes

## [2.0.0] - 2020-12-16

### Added

- New admin panel to toggle all features on and off
- Page speed optimizations for better Lighthouse scores
- Email notification controls for auto-updates
- CSS bug fixes for Divi mobile menu

### Changed

- Modularized `functions.php` into separate include files

## [1.3.0] - 2020-03-29

### Added

- Custom JavaScript code file setup

### Fixed

- Wireframe module flickering in Theme Builder Fixed Header
- Main content displacement in Visual Builder
- Unreachable buttons in Visual/Theme Builder
- Fixed header placement issues

## [1.2.4] - 2020-01-30

### Fixed

- CSS issue hotfix

## [1.2.3] - 2020-01-30

### Fixed

- Wireframe mode behavior since Divi 4.2.2

## [1.2.2] - 2020-01-16

### Fixed

- Automatic translation updates

## [1.2.1] - 2020-01-16

### Added

- Set up automatic updates for all components by default

## [1.2.0] - 2019-12-12

### Added

- Theme Builder Global Header Hack
- Updated README for easier implementation

[3.0.0]: https://github.com/mirkoschubert/divi-child/compare/v2.3.0...HEAD
[2.3.0]: https://github.com/mirkoschubert/divi-child/compare/v2.2.0...v2.3.0
[2.2.0]: https://github.com/mirkoschubert/divi-child/compare/v2.1.0...v2.2.0
[2.1.0]: https://github.com/mirkoschubert/divi-child/compare/v2.0.6...v2.1.0
[2.0.6]: https://github.com/mirkoschubert/divi-child/compare/v2.0.5...v2.0.6
[2.0.5]: https://github.com/mirkoschubert/divi-child/compare/v2.0.4...v2.0.5
[2.0.4]: https://github.com/mirkoschubert/divi-child/compare/v2.0.3...v2.0.4
[2.0.3]: https://github.com/mirkoschubert/divi-child/compare/v2.0.2...v2.0.3
[2.0.2]: https://github.com/mirkoschubert/divi-child/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/mirkoschubert/divi-child/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/mirkoschubert/divi-child/compare/v1.3.0...v2.0.0
[1.3.0]: https://github.com/mirkoschubert/divi-child/compare/v1.2.4...v1.3.0
[1.2.4]: https://github.com/mirkoschubert/divi-child/compare/v1.2.3...v1.2.4
[1.2.3]: https://github.com/mirkoschubert/divi-child/compare/v1.2.2...v1.2.3
[1.2.2]: https://github.com/mirkoschubert/divi-child/compare/v1.2.1...v1.2.2
[1.2.1]: https://github.com/mirkoschubert/divi-child/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/mirkoschubert/divi-child/releases/tag/v1.2.0
