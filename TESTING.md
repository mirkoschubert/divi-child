# Divi Child Theme - Testing Checklist

## 🛡️ A11y Module Tests

### Skip Link
**Look for:** `<a class="screen-reader-text" href="#main">` in HTML
**DevTools:** Press Tab → Link should become visible

### Focus Elements  
**Look for:** `.a11y-focus-elements` CSS class in `<head>`
**Test:** Tab navigation → all elements have visible focus ring

### Underline Links
**Look for:** `.a11y-underline-links` CSS in `<head>`
**Test:** Links in text blocks have underlines

### Enhanced ARIA Labels
**Look for:** `aria-label="Previous slide"` and `aria-label="Next slide"` on sliders
**Look for:** `aria-label="Visit our [Social] page"` on social icons
**JavaScript:** `setupEnhancedAriaLabeling()` function runs

### Duplicate Menu IDs Fix
**Look for:** No duplicate `id="menu-item-XXX"` in HTML
**Console:** `fixDuplicateMenuIds()` runs without errors

### Stop Animations
**Look for:** `@media (prefers-reduced-motion: reduce)` CSS rules
**Test:** OS setting "Reduce motion" → animations stopped

### Text Selection Colors
**Look for:** Custom `::selection` CSS with configured colors
**Test:** Select text → custom colors active

### Slider Navigation Spacing
**Look for:** `.et_pb_slider .et-pb-arrow` with extended spacing

---

## 🐛 Bugs Module Tests

### Fixed Navigation
**Look for:** `.bugs-fixed-navigation` CSS class in `<head>`

### Logo Image Sizing  
**Look for:** `.bugs-logo-image-sizing` CSS class in `<head>`

### Split Section Fix
**Look for:** `.bugs-split-section` CSS class in `<head>`

### Display Errors (Dev only)
**Look for:** `wp_debug_mode()` JavaScript function in footer
**Console:** PHP errors are displayed (development only)

---

## 🎨 LocalFonts Module Tests

### Font CSS Loading
**Look for:** `<link rel="stylesheet" href="...fonts.css">` in `<head>`
**DevTools Network:** `fonts.css` loads (if fonts selected)

### Font Files
**Look for:** `.woff2` font files in Network tab
**DevTools:** `font-display: swap` in CSS font-faces

### REST API
**Network Tab:** `wp-json/divi-child/v1/fonts/*` endpoints work
**Test:** Admin interface → API calls successful

---

## 🔧 Misc Module Tests

### Duplicate Posts
**Admin:** "Duplicate" link in post/page actions
**URL:** `?action=duplicate_post` works

### Disable Projects  
**Admin:** No "Projects" menu item visible
**URL:** `/wp-admin/edit.php?post_type=project` → 404 or redirect

### SVG Support
**Test:** SVG upload possible
**MIME:** `image/svg+xml` allowed

### WebP/AVIF Support  
**Test:** WebP/AVIF upload possible (depending on WP version)

### Hyphenation
**Look for:** `.misc-hyphens` CSS class in `<head>`
**CSS:** `hyphens: auto` on body/text elements

### Mobile Menu Breakpoint
**Look for:** `.misc-mobile-menu-breakpoint` CSS with `@media (max-width: 1280px)`

### Fullscreen Mobile Menu
**Look for:** `.misc-mobile-menu-fullscreen` CSS class in `<head>`

### Disable Divi Upsells
**Look for:** `.misc-disable-divi-upsells` CSS in `<head>`
**Admin:** No Divi promotional banners

### Disable Divi AI
**Look for:** `.misc-disable-divi-ai` CSS in `<head>`  
**Divi Builder:** AI features hidden

### Media Infinite Scroll
**Test:** Media Library → infinite scroll active
**JavaScript:** Pagination removed

---

## 📊 Pagespeed Module Tests

**DevTools:** Reduced CSS/JS requests
**Test:** PageSpeed Insights score improvement

---

## 📈 Umami Module Tests

**Look for:** `<script async defer data-website-id="..." src="...umami.js">` in `<head>`
**Network:** Umami script loads successfully

---

## 🧩 Admin App Tests

### Module Cards
**URL:** `/wp-admin/admin.php?page=divi-child-settings`
**Look for:** `.dvc-module-card` elements
**JavaScript:** React app loads without console errors

### Settings Modal
**Test:** Settings button → modal opens
**Look for:** `.dvc-settings-modal` element
**JavaScript:** No React errors in console

### Grouped Settings
**Look for:** `.group-field` elements in modal
**Test:** Groups expand/collapse with animation
**JavaScript:** Accordion functionality

### Form Fields
**Look for:** `.toggle-field`, `.text-field`, `.color-field` etc.
**Test:** All field types work
**Validation:** Invalid inputs are caught

### Dependencies
**Look for:** `.unsupported` CSS class on unsupported fields
**Test:** Grayed out fields not interactive

### Save Functionality
**Network:** `POST /wp-json/divi-child/v1/modules/[slug]/settings` successful
**Test:** Settings persist after page reload

---

## 🚨 Quick Error Checks

**Browser Console:** No JavaScript errors
**PHP Logs:** No PHP warnings/notices  
**Network Tab:** All requests successful (200/304)
**CSS:** No 404s for stylesheets
**Fonts:** Font loading without errors

---

## 📱 Mobile Testing

**DevTools:** Device simulation for mobile/tablet
**Touch:** Touch targets at least 44px
**Viewport:** `<meta name="viewport">` correctly set