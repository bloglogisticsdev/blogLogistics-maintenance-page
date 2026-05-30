=== BlogLogistics Maintenance Page ===
Contributors: bloglogistics
Tags: maintenance, maintenance mode, 503, coming soon, admin
Requires at least: 7.0
Tested up to: 7.0
Requires PHP: 8.3
Stable tag: 1.5.12
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Displays a custom maintenance page for visitors while allowing administrators to access the site.

== Description ==

BlogLogistics Maintenance Page displays a maintenance page on the front end of a WordPress site while allowing logged-in administrators to continue accessing and editing the regular site.

The plugin includes a simple maintenance mode setting, support for a custom maintenance image, administrator bypass, and cache-aware headers so visitors and search engines understand that the site is temporarily unavailable.

== Features ==

* Simple maintenance mode toggle from the WordPress admin.
* Custom maintenance image support using the WordPress Media Library.
* Logged-in administrators can continue using the normal site.
* Visitors and non-administrator users see the maintenance page when maintenance mode is enabled.
* Sends a 503 Service Unavailable status code while maintenance mode is active.
* Sends no-cache headers for better compatibility with caching plugins.
* Defines DONOTCACHEPAGE for compatibility with caching systems such as WP Rocket.
* Includes a clean, responsive maintenance page layout.
* Uses a bundled default maintenance image when no custom image is selected.
* Lightweight implementation with no front-end configuration required.

== Installation ==

1. Upload the plugin folder to /wp-content/plugins/.
2. Activate the plugin in WordPress.
3. Go to BlogLogistics > Maintenance Page.
4. Enable maintenance mode when needed.
5. Optionally select a custom maintenance image.

== Usage ==

When maintenance mode is enabled, visitors will see the maintenance page.

Logged-in administrators will continue to see the regular site. This allows site owners, developers, and administrators to keep working while the public-facing site is temporarily hidden.

== Frequently Asked Questions ==

= Will administrators still see the normal site? =

Yes. Logged-in administrators can continue viewing and editing the site normally.

= Will visitors see the maintenance page? =

Yes. When maintenance mode is enabled, visitors and non-administrator users will see the maintenance page.

= Can I use a custom maintenance image? =

Yes. The plugin supports selecting a custom image from the WordPress Media Library.

= What happens if I do not choose a custom image? =

The plugin will use the bundled default maintenance image.

= Does the plugin send a 503 status code? =

Yes. When maintenance mode is active, the plugin sends a 503 Service Unavailable response.

= Is this compatible with caching plugins? =

The plugin sends no-cache headers and defines DONOTCACHEPAGE to reduce the chance of caching systems storing the maintenance page.

= Does this plugin require coding? =

No. Maintenance mode can be enabled or disabled from the WordPress admin.

== Changelog ==

= 1.5.12 =
* Fix release workflow so plugin icons are included in the update manifest.

= 1.5.11 =
* Add BlogLogistics plugin icon assets.
* Add icon metadata to the generated update manifest.

= 1.5.10 =
* Move settings from Settings > Maintenance Mode to BlogLogistics > Maintenance Page.
* Add the shared BlogLogistics wp-admin parent menu with the RSS-style icon.

= 1.5.9 =
* Automate update manifest generation and upload from GitHub Actions.
* Generate the update manifest changelog from readme.txt so WordPress displays the full changelog.

= 1.5.8 =
* Switch update checks to the BlogLogistics update manifest endpoint.
* Avoid GitHub API update checks to reduce rate-limit errors.

= 1.5.7 =
* Prevent Plugin Update Checker from loading more than once when multiple BlogLogistics plugins are active.
* Keep the Maintenance Page updater wrapper class plugin-specific to avoid conflicts with other BlogLogistics plugins.

= 1.5.6 =
* Fixed admin warning notice so it only appears when maintenance mode is actually enabled.
* Added strict checkbox sanitization to prevent stale truthy values from keeping maintenance mode active.
* Purges common WordPress, host, and page-cache plugins immediately whenever maintenance mode is toggled on or off.
* Adds early maintenance checks on init and template_redirect so logged-out visitors are intercepted before the theme loads.
* Adds cache-bypass constants and diagnostic maintenance response headers.
* Adds hidden checkbox fallback so disabling maintenance mode saves reliably.

= 1.5.2 =
* Standardize plugin for GitHub release-based updates.
* Add GitHub updater integration.
* Add automated WordPress ZIP build workflow.
* Update requirements to WordPress 7.0 and PHP 8.3.
* Add standardized BlogLogistics plugin metadata.

= 1.5.1 =
* Maintenance release before GitHub update standardization.

= 1.5.0 =
* Added maintenance mode enable/disable setting.
* Added custom maintenance image upload support.
* Added Media Library integration.
* Added admin notice when maintenance mode is active.
* Added improved caching compatibility.
* Added 503 Service Unavailable response for maintenance mode.
* Added object-oriented plugin structure.
* Added responsive maintenance page layout.
* Added internationalisation support.
* Added security and escaping improvements.

= 1.4.0-beta =
* Added default maintenance image.
* Added basic maintenance page display.
* Fixed issue where caching solutions could still display the normal content page.

== Upgrade Notice ==

= 1.5.8 =
Maintenance Page now checks BlogLogistics update manifests instead of the GitHub API for update metadata.

= 1.5.6 =
Maintenance mode now purges caches when toggled, helping prevent cached normal pages from showing after maintenance is re-enabled.

= 1.5.2 =
Standardizes the plugin for BlogLogistics GitHub release-based updates and updates requirements to WordPress 7.0 and PHP 8.3.

== License ==

This plugin is licensed under GPL-3.0-or-later.
See https://www.gnu.org/licenses/gpl-3.0.html.