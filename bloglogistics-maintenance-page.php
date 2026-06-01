<?php
/**
 * Plugin Name:       BlogLogistics Maintenance Page
 * Plugin URI:        https://github.com/bloglogisticsdev/blogLogistics-maintenance-page
 * Description:       Displays a custom maintenance page for visitors while allowing administrators to access the site.
 * Version:           1.6.1
 * Requires at least: 7.0
 * Requires PHP:      8.3
 * Author:            BlogLogistics
 * Author URI:        https://www.bloglogistics.com/
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Update URI:        https://github.com/bloglogisticsdev/blogLogistics-maintenance-page
 * Text Domain:       bloglogistics-maintenance-page
 * Domain Path:       /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'BLOGLOGISTICS_MP_VERSION', '1.6.1' );
define( 'BLOGLOGISTICS_MP_SLUG', 'blogLogistics-maintenance-page' );
define( 'BLOGLOGISTICS_MP_FILE', __FILE__ );
define( 'BLOGLOGISTICS_MP_DIR', plugin_dir_path( __FILE__ ) );
define( 'BLOGLOGISTICS_MP_URL', plugin_dir_url( __FILE__ ) );
define( 'BLOGLOGISTICS_MP_REPO_URL', 'https://github.com/bloglogisticsdev/blogLogistics-maintenance-page/' );
define( 'BLOGLOGISTICS_MP_UPDATE_MANIFEST_URL', 'https://updates.bloglogistics.com/plugins/bloglogistics-maintenance-page.json' );

/**
 * Load plugin translations.
 */
function bloglogistics_maintenance_page_load_textdomain() {
    load_plugin_textdomain(
        'bloglogistics-maintenance-page',
        false,
        dirname( plugin_basename( BLOGLOGISTICS_MP_FILE ) ) . '/languages'
    );
}
add_action( 'plugins_loaded', 'bloglogistics_maintenance_page_load_textdomain' );

$bloglogistics_mp_puc = BLOGLOGISTICS_MP_DIR . 'vendor/plugin-update-checker/plugin-update-checker.php';

if ( file_exists( $bloglogistics_mp_puc ) ) {
    if ( ! class_exists( \YahnisElsts\PluginUpdateChecker\v5\PucFactory::class, false ) ) {
        require_once $bloglogistics_mp_puc;
    }

    require_once BLOGLOGISTICS_MP_DIR . 'includes/class-bloglogistics-maintenance-page-updater.php';

    BlogLogistics_Maintenance_Page_Updater::init( array(
        'repo_url'    => BLOGLOGISTICS_MP_UPDATE_MANIFEST_URL,
        'plugin_file' => BLOGLOGISTICS_MP_FILE,
        'slug'        => BLOGLOGISTICS_MP_SLUG,
    ) );
}

require_once BLOGLOGISTICS_MP_DIR . 'includes/class-bloglogistics-maintenance-page.php';

new BlogLogistics_Maintenance_Mode();
