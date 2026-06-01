<?php
/**
 * Uninstall cleanup for BlogLogistics Maintenance Page.
 *
 * @package BlogLogistics_Maintenance_Page
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

$options = array(
    'bloglogistics_maintenance_mode_enabled',
    'bloglogistics_maintenance_custom_image_url',
    'bloglogistics_maintenance_cache_buster',
);

foreach ( $options as $option ) {
    delete_option( $option );
}
