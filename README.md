# BlogLogistics Maintenance Page

**Contributors:** rogerwheatley  
**Tags:** wordpress, maintenance  
**Requires at least:** 6.8  
**Tested up to:** 6.8.1  
**Stable tag:** 1.5.1  
**License:** GPLv3 or later  
**License URI:** [https://www.gnu.org/licenses/gpl-3.0.html](https://www.gnu.org/licenses/gpl-3.0.html)  

Displays a static maintenance page to non-logged-in admins on a WordPress website.

---

## Description

This plugin displays a static maintenance page on the front-end when activated, redirecting all requests to the maintenance page. This plugin shows a maintenance page to everyone except administrators who are logged in (logged-in administrators will see the regular site). All other users will see the maintenance page.

The plugin also allows post-level exclusions and site-wide customisation of what content is included and how it’s structured.

### Key Features

- **Simple Maintenance Mode Toggle:**  
  Easily enable or disable maintenance mode from the WordPress admin settings, no coding required.

- **Custom Maintenance Image Support:**  
  Upload and display your own branded maintenance image, or fall back to the included default image.

- **Administrator Bypass:**  
  Logged-in administrators can continue viewing and editing the site while visitors see the maintenance page.

- **Caching Plugin Compatibility:**  
  Fully compatible with popular caching plugins like WP Rocket. Sends proper 503 headers and disables caching for the maintenance view.

- **SEO-Friendly Response:**  
  Sends a 503 Service Unavailable status code to ensure search engines know the site is temporarily offline and not penalised.

- **Clean, Responsive Design:**  
  Built-in maintenance page layout is mobile-friendly, visually clean, and uses modern styling.

- **No Coding Necessary:**  
  Activate the plugin and configure it via the admin interface. Perfect for non-technical users.

- **Lightweight and Secure:**  
  Minimal footprint and secure implementation, following WordPress best practices.

---

## Installation

1. Upload the plugin files to the `/wp-content/plugins/blogLogistics-maintenance-page` directory, or install via the WordPress Plugins screen directly.  
2. Activate the plugin through the 'Plugins' screen in WordPress.  
3. Activate plugin.  
4. In WP settings, toggle maintenance mode on/of and upload custom image if needed.

---

## Usage

Once activated, the plugin will automatically display a maintenance page to all WordPress visitors except for logged-in administrator accounts.

---

## Frequently Asked Questions

### What does this plugin do?

This plugin displays a maintenance page to all non-administrator users when maintenance mode is enabled. It supports a custom image and integrates seamlessly with caching plugins like WP Rocket by preventing cached maintenance pages.

### Will administrators still see the site normally?

Yes. Administrators who are logged in will see the full website as usual, allowing for ongoing work or review during maintenance.

### Can I use my own maintenance image?

Absolutely. You can upload and select your own image from the plugin settings page in the WordPress admin. If no image is provided, a default maintenance image will be shown.

### Will this work with caching plugins like WP Rocket or W3 Total Cache?

Yes. The plugin sets the proper HTTP headers and a 503 Service Unavailable status code to ensure the maintenance page is not cached. It also defines the `DONOTCACHEPAGE` constant to work alongside most caching solutions.

### How do I enable or disable maintenance mode?

Go to **Settings → Maintenance Mode** in your WordPress admin panel. From there, simply check or uncheck the option to enable or disable maintenance mode.

### Will my site’s SEO be affected?

No. The plugin returns a 503 status code when the maintenance page is active, which informs search engines that the site is temporarily unavailable and to check back later.

### Can I customise the maintenance message?

The current version allows customisation of the image only. However, you can modify the plugin code to change the text if needed, or request this as a future feature.

---

## Changelog

### 1.5.1

- **Fixed:**
  Resolved an issue where the maintenance page content would not display, showing a blank 503 error instead. The core HTML output method has been reverted to a highly compatible and proven approach, ensuring the maintenance page displays correctly.

### 1.5.0 – Major Feature Update

#### 🚀 Key Improvements

- **Enable/Disable Maintenance Mode Toggle**
  - Added a settings page with a checkbox to easily toggle maintenance mode.
  - Utilizes the WordPress Settings API for secure, standards-based option handling.
  - Settings persist in the database across sessions and updates.

- **Custom Maintenance Image Upload**
  - Seamless Media Library integration using `wp_enqueue_media()` and a custom admin script.
  - Users can upload or select a custom image and see a live preview.
  - Fallback to a default bundled image if no custom image is set.

- **Improved Caching Compatibility**
  - Defines `DONOTCACHEPAGE` to prevent caching of the maintenance page (WP Rocket compatible).
  - Sends appropriate no-cache HTTP headers via `nocache_headers()`.
  - Returns a `503 Service Unavailable` status to ensure search engines don’t penalize your site and caches don’t store the page.

- **Administrator Notice**
  - Displays a visible dashboard warning when maintenance mode is active—only shown to administrators.

- **Object-Oriented Refactor**
  - Introduced `BlogLogistics_Maintenance_Mode` class to encapsulate all functionality.
  - Improves modularity, avoids naming conflicts, and makes the code easier to maintain.

- **Responsive & Modern Maintenance Page**
  - HTML5 structure with mobile-first CSS.
  - Uses `clamp()` and `vh` units for responsive typography and image scaling.
  - Enhanced visuals: clean layout, soft shadows, rounded corners, and semantic footer.

- **Output Buffering**
  - Uses `ob_start()` and `ob_end_clean()` to ensure that only the intended maintenance content and headers are sent—no theme or plugin interference.

- **Internationalisation (i18n)**
  - All user-facing strings are wrapped in translation functions with the proper text domain (`bloglogistics-maintenance-page`).

- **Security Enhancements**
  - `ABSPATH` check to prevent direct access.
  - Strict use of escaping functions (`esc_html()`, `esc_url()`, `esc_attr()`, etc.) throughout.
  - Role-based logic using `current_user_can( 'administrator' )`.

### 1.4.0-Beta

- Default "maintenance" image displayed.  
- No settings available.  
- Just activate and it works.  
- **Fixed:** Issue where caching solutions were still displaying content page.

---

## Upgrade Notice

### 1.4.0-Beta

First public release. Adds full Markdown generation and admin options.

---

## License

This plugin is licensed under the GPLv3 or later.  
See [https://www.gnu.org/licenses/gpl-3.0.html](https://www.gnu.org/licenses/gpl-3.0.html)
