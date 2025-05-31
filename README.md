# BlogLogistics Maintenance Page

**Contributors:** rogerwheatley  
**Tags:** wordpress, maintenance  
**Requires at least:** 6.8  
**Tested up to:** 6.8.1  
**Stable tag:** 1.4.0-Beta  
**License:** GPLv3 or later  
**License URI:** [https://www.gnu.org/licenses/gpl-3.0.html](https://www.gnu.org/licenses/gpl-3.0.html)  

Displays a static maintenance page to non-logged-in admins on a WordPress website.

---

## Description

This plugin displays a static maintenance page on the front-end when activated, redirecting all requests to the maintenance page. This plugin shows a maintenance page to everyone except administrators who are logged in (logged-in administrators will see the regular site). All other users will see the maintenance page. No configuration required.

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
4. No settings available.

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
