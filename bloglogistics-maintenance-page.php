<?php
/**
 * Plugin Name:       BlogLogistics Maintenance Page
 * Plugin URI:        https://github.com/bloglogisticsdev/blogLogistics-maintenance-page
 * Description:       Displays a custom maintenance page for visitors while allowing administrators to access the site.
 * Version:           1.5.4
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

define( 'BLOGLOGISTICS_MP_VERSION', '1.5.4' );
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
        // Admin settings and UI.
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        add_action( 'admin_notices', array( $this, 'maintenance_mode_active_notice' ) );

        // Frontend functionality. Intercept public requests as early as practical, then keep a later fallback.
        add_action( 'init', array( $this, 'display_maintenance_page' ), 0 );
        add_action( 'template_redirect', array( $this, 'display_maintenance_page' ), 0 );

        // Purge common caches when the mode changes so logged-out visitors do not keep seeing cached public pages.
        add_action( 'update_option_' . self::OPTION_ENABLE_MAINTENANCE, array( $this, 'handle_maintenance_mode_update' ), 10, 3 );

        if ( $this->is_maintenance_mode_active() ) {
            $this->set_cache_bypass_constants();
        }
    }

    /**
     * Checks if maintenance mode is currently active.
     *
     * @return bool True if maintenance mode is active, false otherwise.
     */
    private function is_maintenance_mode_active() {
        // Default to false unless explicitly enabled.
        return rest_sanitize_boolean( get_option( self::OPTION_ENABLE_MAINTENANCE, false ) );
    }

    /**
     * Defines cache bypass constants for cache plugins that load after normal plugins.
     */
    private function set_cache_bypass_constants() {
        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( 'DONOTCACHEPAGE', true );
        }

        if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
            define( 'DONOTCACHEOBJECT', true );
        }

        if ( ! defined( 'DONOTCACHEDB' ) ) {
            define( 'DONOTCACHEDB', true );
        }
    }

    /**
     * Purges common cache layers when maintenance mode is toggled.
     *
     * @param mixed  $old_value The previous option value.
     * @param mixed  $value     The new option value.
     * @param string $option    The option name.
     */
    public function handle_maintenance_mode_update( $old_value, $value, $option ) {
        unset( $option );

        if ( rest_sanitize_boolean( $old_value ) === rest_sanitize_boolean( $value ) ) {
            return;
        }

        $this->purge_site_caches();
    }

    /**
     * Clears popular WordPress cache plugins and the object cache where available.
     */
    private function purge_site_caches() {
        if ( function_exists( 'rocket_clean_domain' ) ) {
            rocket_clean_domain();
        }

        if ( function_exists( 'w3tc_flush_all' ) ) {
            w3tc_flush_all();
        }

        if ( function_exists( 'wp_cache_clear_cache' ) ) {
            wp_cache_clear_cache();
        }

        if ( class_exists( 'LiteSpeed_Cache_API' ) && method_exists( 'LiteSpeed_Cache_API', 'purge_all' ) ) {
            LiteSpeed_Cache_API::purge_all();
        }

        if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
            sg_cachepress_purge_cache();
        }

        if ( function_exists( 'wp_cache_flush' ) ) {
            wp_cache_flush();
        }
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
            <input type="hidden" name="<?php echo esc_attr( self::OPTION_ENABLE_MAINTENANCE ); ?>" value="0" />
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
        if ( $this->is_maintenance_mode_active() && current_user_can( 'manage_options' ) ) {
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
     * Determines whether the current request should bypass maintenance mode.
     *
     * @return bool True when the current request should not show maintenance mode.
     */
    private function should_bypass_maintenance_page() {
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            return true;
        }

        if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
            return true;
        }

        if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
            return true;
        }

        if ( is_admin() ) {
            return true;
        }

        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }

        $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
        $script_name = isset( $_SERVER['SCRIPT_NAME'] ) ? wp_unslash( $_SERVER['SCRIPT_NAME'] ) : '';

        $allowed_paths = array(
            'wp-login.php',
            'wp-cron.php',
            'xmlrpc.php',
            'wp-json',
        );

        foreach ( $allowed_paths as $allowed_path ) {
            if ( false !== strpos( $request_uri, $allowed_path ) || false !== strpos( $script_name, $allowed_path ) ) {
                return true;
            }
        }

        if ( isset( $_GET['rest_route'] ) ) {
            return true;
        }

        return false;
    }

    /**
     * Sends headers that make cached public pages much less likely to leak through.
     */
    private function send_maintenance_headers() {
        $this->set_cache_bypass_constants();

        if ( ! headers_sent() ) {
            nocache_headers();
            status_header( 503 );
            header( 'Retry-After: 3600' );
            header( 'X-BlogLogistics-Maintenance-Mode: active' );
            header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0, private' );
            header( 'Pragma: no-cache' );
            header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
        }
    }

    /**
     * Displays the maintenance page to logged-out visitors and non-managers.
     */
    public function display_maintenance_page() {
        static $already_displayed = false;

        if ( $already_displayed ) {
            return;
        }

        if ( ! $this->is_maintenance_mode_active() ) {
            return;
        }

        if ( $this->should_bypass_maintenance_page() ) {
            return;
        }

        $already_displayed = true;

        $this->send_maintenance_headers();

        $custom_image_url = get_option( self::OPTION_CUSTOM_IMAGE_URL );
        $image_url        = ! empty( $custom_image_url ) ? $custom_image_url : plugins_url( self::DEFAULT_IMAGE_FILENAME, __FILE__ );

        echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow"><meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0"><meta http-equiv="Pragma" content="no-cache"><meta http-equiv="Expires" content="0"><title>' . esc_html__( 'Website Under Maintenance', 'bloglogistics-maintenance-page' ) . '</title><style>html,body{min-height:100%;margin:0;padding:0}body{display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;box-sizing:border-box;font-family:Arial,sans-serif;background:#f7f7f7;color:#111}h1{margin:0;padding:20px;font-size:clamp(1.75rem,4vw,3rem);letter-spacing:.04em}img{max-width:min(90%,900px);max-height:75vh;width:auto;height:auto;box-shadow:0 10px 10px -5px rgba(0,0,0,.5);border:10px solid #fff;outline:1px solid rgba(0,0,0,.1);background:#fff}</style></head><body><h1>' . esc_html__( 'WEBSITE UNDER MAINTENANCE', 'bloglogistics-maintenance-page' ) . '</h1><img class="skip-lazy" src="' . esc_url( $image_url ) . '" alt="' . esc_attr__( 'This website is under maintenance, we\'ll be back soon', 'bloglogistics-maintenance-page' ) . '" title="' . esc_attr__( 'This website is under maintenance, we\'ll be back soon', 'bloglogistics-maintenance-page' ) . '"></body></html>';
        exit;
    }
}

// Instantiate the plugin class.
new BlogLogistics_Maintenance_Mode();