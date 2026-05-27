<?php
/**
 * Plugin Name:       BlogLogistics Maintenance Page
 * Plugin URI:        https://github.com/bloglogisticsdev/blogLogistics-maintenance-page
 * Description:       Displays a custom maintenance page for visitors while allowing administrators to access the site.
 * Version:           1.5.2
 * Requires at least: 7.0
 * Requires PHP:      8.3
 * Author:            BlogLogistics
 * Author URI:        https://www.bloglogistics.com/
 * License:           GPL-3.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Update URI:        https://github.com/bloglogisticsdev/blogLogistics-maintenance-page
 * Text Domain:       bloglogistics-maintenance-page
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'BLOGLOGISTICS_MP_VERSION', '1.5.2' );
define( 'BLOGLOGISTICS_MP_SLUG', 'blogLogistics-maintenance-page' );
define( 'BLOGLOGISTICS_MP_FILE', __FILE__ );
define( 'BLOGLOGISTICS_MP_DIR', plugin_dir_path( __FILE__ ) );
define( 'BLOGLOGISTICS_MP_REPO_URL', 'https://github.com/bloglogisticsdev/blogLogistics-maintenance-page/' );

$bloglogistics_mp_puc = BLOGLOGISTICS_MP_DIR . 'vendor/plugin-update-checker/plugin-update-checker.php';

if ( file_exists( $bloglogistics_mp_puc ) ) {
    require_once $bloglogistics_mp_puc;

    require_once BLOGLOGISTICS_MP_DIR . 'includes/class-bloglogistics-maintenance-page-github-updater.php';

    BlogLogistics_Maintenance_Page_GitHub_Updater::init( [
        'repo_url'    => BLOGLOGISTICS_MP_REPO_URL,
        'plugin_file' => BLOGLOGISTICS_MP_FILE,
        'slug'        => BLOGLOGISTICS_MP_SLUG,
    ] );
}

/**
 * Class BlogLogistics_Maintenance_Mode
 *
 * Manages the website's maintenance mode functionality.
 */
class BlogLogistics_Maintenance_Mode {

    /**
     * Option key for enabling/disabling maintenance mode.
     */
    const OPTION_ENABLE_MAINTENANCE = 'bloglogistics_maintenance_mode_enabled';

    /**
     * Option key for the custom maintenance image URL.
     */
    const OPTION_CUSTOM_IMAGE_URL = 'bloglogistics_maintenance_custom_image_url';

    /**
     * Default maintenance image filename.
     * This file should be in the plugin's root directory:
     * /wp-content/plugins/bloglogistics-maintenance-page/website-maintenance-min.jpg
     */
    const DEFAULT_IMAGE_FILENAME = 'website-maintenance-min.jpg';

    /**
     * Constructor.
     *
     * Initializes the plugin by setting up hooks.
     */
    public function __construct() {
        // Admin settings and UI
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

        // Frontend functionality
        if ( $this->is_maintenance_mode_active() ) {
            add_action( 'template_redirect', array( $this, 'display_maintenance_page' ) );
            add_action( 'admin_notices', array( $this, 'maintenance_mode_active_notice' ) );
        }
    }

    /**
     * Checks if maintenance mode is currently active.
     *
     * @return bool True if maintenance mode is active, false otherwise.
     */
    private function is_maintenance_mode_active() {
        // Default to false (off) unless explicitly enabled
        return (bool) get_option( self::OPTION_ENABLE_MAINTENANCE, false );
    }

    /**
     * Registers plugin settings with WordPress.
     */
    public function register_settings() {
        // Register the maintenance mode toggle setting
        register_setting(
            'bloglogistics_maintenance_options', // Option group
            self::OPTION_ENABLE_MAINTENANCE,     // Option name
            array(
                'type'              => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default'           => false,
                'show_in_rest'      => false,
            )
        );

        // Register the custom image URL setting
        register_setting(
            'bloglogistics_maintenance_options', // Option group
            self::OPTION_CUSTOM_IMAGE_URL,       // Option name
            array(
                'type'              => 'string',
                'sanitize_callback' => array( $this, 'sanitize_image_url' ),
                'default'           => '',
                'show_in_rest'      => false,
            )
        );

        // Add a settings section
        add_settings_section(
            'bloglogistics_maintenance_section', // ID
            esc_html__( 'Maintenance Mode Settings', 'bloglogistics-maintenance-page' ), // Title
            null, // Callback (no intro text needed)
            'bloglogistics_maintenance_page_slug' // Page
        );

        // Add the enable/disable field
        add_settings_field(
            'bloglogistics_maintenance_enable_field', // ID
            esc_html__( 'Enable Maintenance Mode', 'bloglogistics-maintenance-page' ), // Title
            array( $this, 'render_enable_maintenance_field' ), // Callback
            'bloglogistics_maintenance_page_slug', // Page
            'bloglogistics_maintenance_section' // Section
        );

        // Add the custom image upload field
        add_settings_field(
            'bloglogistics_maintenance_image_field', // ID
            esc_html__( 'Custom Maintenance Image', 'bloglogistics-maintenance-page' ), // Title
            array( $this, 'render_custom_image_field' ), // Callback
            'bloglogistics_maintenance_page_slug', // Page
            'bloglogistics_maintenance_section' // Section
        );
    }

    /**
     * Sanitizes the custom image URL.
     *
     * @param string $url The URL to sanitize.
     * @return string The sanitized URL.
     */
    public function sanitize_image_url( $url ) {
        return esc_url_raw( $url );
    }

    /**
     * Renders the checkbox field for enabling/disabling maintenance mode.
     */
    public function render_enable_maintenance_field() {
        $enabled = $this->is_maintenance_mode_active();
        ?>
        <label for="<?php echo esc_attr( self::OPTION_ENABLE_MAINTENANCE ); ?>">
            <input type="checkbox" id="<?php echo esc_attr( self::OPTION_ENABLE_MAINTENANCE ); ?>" name="<?php echo esc_attr( self::OPTION_ENABLE_MAINTENANCE ); ?>" value="1" <?php checked( $enabled, true ); ?> />
            <?php esc_html_e( 'Check this box to put your website into maintenance mode.', 'bloglogistics-maintenance-page' ); ?>
        </label>
        <?php
    }

    /**
     * Renders the custom image upload field.
     */
    public function render_custom_image_field() {
        $image_url = get_option( self::OPTION_CUSTOM_IMAGE_URL, '' );
        ?>
        <div class="bloglogistics-image-uploader">
            <input type="text" id="<?php echo esc_attr( self::OPTION_CUSTOM_IMAGE_URL ); ?>" name="<?php echo esc_attr( self::OPTION_CUSTOM_IMAGE_URL ); ?>" value="<?php echo esc_attr( $image_url ); ?>" class="regular-text" readonly />
            <button type="button" class="button button-secondary bloglogistics-upload-button">
                <?php esc_html_e( 'Upload/Select Image', 'bloglogistics-maintenance-page' ); ?>
            </button>
            <button type="button" class="button button-secondary bloglogistics-remove-button" style="<?php echo empty( $image_url ) ? 'display:none;' : ''; ?>">
                <?php esc_html_e( 'Remove Image', 'bloglogistics-maintenance-page' ); ?>
            </button>
            <p class="description">
                <?php esc_html_e( 'Upload a custom image for your maintenance page. If no image is set, the default image will be used.', 'bloglogistics-maintenance-page' ); ?>
            </p>
            <div class="bloglogistics-image-preview" style="margin-top: 10px;">
                <?php if ( ! empty( $image_url ) ) : ?>
                    <img src="<?php echo esc_url( $image_url ); ?>" style="max-width: 200px; height: auto;" />
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Adds the plugin's settings page to the WordPress admin menu.
     */
    public function add_admin_menu() {
        add_options_page(
            esc_html__( 'Maintenance Mode Settings', 'bloglogistics-maintenance-page' ), // Page title
            esc_html__( 'Maintenance Mode', 'bloglogistics-maintenance-page' ),         // Menu title
            'manage_options',                                                         // Capability required
            'bloglogistics_maintenance_page_slug',                                    // Menu slug
            array( $this, 'render_settings_page' )                                    // Callback to render content
        );
    }

    /**
     * Enqueues necessary admin scripts (Media Uploader).
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_admin_scripts( $hook ) {
        if ( 'settings_page_bloglogistics_maintenance_page_slug' !== $hook ) {
            return;
        }

        wp_enqueue_media(); // Enqueue WordPress media uploader scripts and styles

        wp_enqueue_script(
            'bloglogistics-maintenance-admin-script',
            plugins_url( 'admin-script.js', __FILE__ ), // Path to our custom admin JS (now in root)
            array( 'jquery' ),
            '1.0.0', // Version
            true
        );
    }

    /**
     * Renders the content of the plugin's settings page.
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'BlogLogistics Maintenance Mode', 'bloglogistics-maintenance-page' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'bloglogistics_maintenance_options' ); // Output security fields
                do_settings_sections( 'bloglogistics_maintenance_page_slug' ); // Output setting sections
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Displays an admin notice if maintenance mode is active.
     */
    public function maintenance_mode_active_notice() {
        if ( current_user_can( 'administrator' ) ) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong><?php esc_html_e( 'Maintenance Mode is ACTIVE!', 'bloglogistics-maintenance-page' ); ?></strong><br />
                    <?php esc_html_e( 'Your website is currently displaying the maintenance page to all non-administrators. ', 'bloglogistics-maintenance-page' ); ?>
                    <a href="<?php echo esc_url( admin_url( 'options-general.php?page=bloglogistics_maintenance_page_slug' ) ); ?>">
                        <?php esc_html_e( 'Go to settings to disable it.', 'bloglogistics-maintenance-page' ); ?>
                    </a>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Displays the maintenance page to non-administrators using the old working method.
     */
    public function display_maintenance_page() {
        // Allow administrators to see the site normally.
        if ( current_user_can( 'administrator' ) ) {
            return;
        }

        // --- Tell caching solutions (like WP Rocket) and browsers not to cache THIS request ---
        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( 'DONOTCACHEPAGE', true ); // Current request only, site-wide caching stays on
        }
        nocache_headers(); // Adds proper HTTP headers (Cache-Control, Pragma, Expires)
        status_header( 503 ); // 503 = service unavailable (WP Rocket and most proxies never cache 503s)

        // Determine which image to use (uses custom if set, otherwise default in plugin root)
        $custom_image_url = get_option( self::OPTION_CUSTOM_IMAGE_URL );
        // Use custom image if available, otherwise use the default image from the plugin's root folder.
        $image_url        = ! empty( $custom_image_url ) ? $custom_image_url : plugins_url( self::DEFAULT_IMAGE_FILENAME, __FILE__ );

        // Output the maintenance page using the old working method (single echo, no output buffering)
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" text="text/html; charset=utf-8" /><meta http-equiv="CACHE-CONTROL" content="NO-CACHE" /><meta http-equiv="PRAGMA" content="NO-CACHE" /><meta http-equiv="EXPIRES" content="0" /><title>' . esc_html__( 'Website Under Maintenance', 'bloglogistics-maintenance-page' ) . '</title><style> html,body{height:100%;margin:0;padding:0;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;box-sizing:border-box}h1{margin:0;padding:20px;font-size:2em}img{max-width:90%;max-height:90%;width:auto;height:auto;box-shadow:0 10px 10px -5px rgba(0,0,0,0.5);border:10px solid white;outline:1px solid rgba(0,0,0,0.1)}@media (max-width:600px){img{max-width:80%}}</style></head><body><h1>' . esc_html__( 'WEBSITE UNDER MAINTENANCE', 'bloglogistics-maintenance-page' ) . '</h1><img class="skip-lazy" src="' . esc_url( $image_url ) . '" alt="' . esc_attr__( 'This website is under maintenance, we\'ll be back soon', 'bloglogistics-maintenance-page' ) . '" title="' . esc_attr__( 'This website is under maintenance, we\'ll be back soon', 'bloglogistics-maintenance-page' ) . '"/></body></html>';
        exit(); // Crucial to stop WordPress execution and display only this page.
    }
}

// Instantiate the plugin class.
new BlogLogistics_Maintenance_Mode();