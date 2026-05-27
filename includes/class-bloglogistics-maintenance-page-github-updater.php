<?php
/**
 * GitHub updater for BlogLogistics Maintenance Page.
 *
 * @package BlogLogistics_Maintenance_Page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

if ( ! class_exists( 'BlogLogistics_Maintenance_Page_GitHub_Updater', false ) ) {

    final class BlogLogistics_Maintenance_Page_GitHub_Updater {

        /**
         * Initialise GitHub-based plugin updates.
         *
         * @param array<string, string> $args Updater arguments.
         */
        public static function init( array $args ): void {
            if (
                empty( $args['repo_url'] ) ||
                empty( $args['plugin_file'] ) ||
                empty( $args['slug'] )
            ) {
                return;
            }

            if ( ! class_exists( PucFactory::class ) ) {
                return;
            }

            $update_checker = PucFactory::buildUpdateChecker(
                $args['repo_url'],
                $args['plugin_file'],
                $args['slug']
            );

            $update_checker->getVcsApi()->enableReleaseAssets( '/\.zip($|[?&#])/i' );
        }
    }
}
