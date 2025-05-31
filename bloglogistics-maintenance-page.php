<?php
/**
* Plugin Name: BlogLogistics Maintenance Page
* Description: This plugin displays a static maintenance page on the front-end when activated, redirecting all requests to the maintenance page. This plugin shows a maintenance page to everyone except administrators who are logged in (logged in administrators will see the regular site). All other users will see the maintenance page. No configuration required.required.
* Version: 1.4.0-Beta
* Author: Roger Wheatley
* Plugin URI: https://www.bloglogistics.com
* License: GPLv3
*/

// Hook the 'template_redirect' action to display the maintenance page
add_action('template_redirect', 'bloglogistics_maintenance_mode');

function bloglogistics_maintenance_mode() {
    // Allow administrators to see the site
    if (current_user_can('administrator')) {
        return;
    }

     /* ---  Tell WP Rocket (and every proxy / browser) not to cache THIS request --- */
    define( 'DONOTCACHEPAGE', true );        // current request only, site-wide caching stays on
    nocache_headers();                       // Adds proper HTTP headers
    status_header( 503 );                    // 503 = service unavailable (WP Rocket never caches 503s)

    $image_url = plugins_url( 'website-maintenance-min.jpg', __FILE__ );

    // Output the maintenance page
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" text="text/html; charset=utf-8" /><meta http-equiv="CACHE-CONTROL" content="NO-CACHE" /><meta http-equiv="PRAGMA" content="NO-CACHE" /><meta http-equiv="EXPIRES" content="0" /><title>Website Under Maintenance</title><style> html,body{height:100%;margin:0;padding:0;display:flex;flex-direction:column;justify-content:center;align-items:center;text-align:center;box-sizing:border-box}h1{margin:0;padding:20px;font-size:2em}img{max-width:90%;max-height:90%;width:auto;height:auto;box-shadow:0 10px 10px -5px rgba(0,0,0,0.5);border:10px solid white;outline:1px solid rgba(0,0,0,0.1)}@media (max-width:600px){img{max-width:80%}}</style></head><body><h1>WEBSITE UNDER MAINTENANCE</h1><img class="skip-lazy" src="' . esc_url( $image_url ) . '" alt="This website is under maintenance, we\'ll be back soon" title="This website is under maintenance, we\'ll be back soon"/></body></html>';
    exit();
}
?>
