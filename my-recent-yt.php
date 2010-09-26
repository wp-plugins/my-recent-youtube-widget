<?php

/*
Plugin Name: My Recent YouTube Widget
Description: Embed the most recent YouTube videos for a user in a sidebar
Author: Dave Ross
Version: 0.4
Author URI: http://davidmichaelross.com
*/

/**
 * Copyright (c) 2009 Dave Ross <dave@csixty4.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
 * persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 *   The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR 
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR 
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 **/

$wpVerParts = explode('.', $wp_version);
$wpVerMajorMinor = floatval($wpVerParts[0].'.'.$wpVerParts[1]);

if(5.0 > floatval(phpversion())) {
	// Call the special error handler that displays an error
	add_action('admin_notices', 'my_recent_youtube_phpver_admin_notice');
}
elseif($wpVerMajorMinor < 2.9) {
	add_action('admin_notices', 'my_recent_youtube_wpver_admin_notice');
}
else {
	// Pre-2.6 compatibility
	// See http://codex.wordpress.org/Determining_Plugin_and_Content_Directories
	if ( ! defined( 'WP_CONTENT_URL' ) )
	      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
	if ( ! defined( 'WP_CONTENT_DIR' ) )
	      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ( ! defined( 'WP_PLUGIN_URL' ) )
	      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
	if ( ! defined( 'WP_PLUGIN_DIR' ) )
	      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
	      
	include_once("MyRecentYT.php");
	add_action('plugins_loaded', array('MyRecentYT', 'init'));
}

function my_recent_youtube_phpver_admin_notice() {
	$alertMessage = __("My Recent YouTube Widget requires PHP 5.0 or higher");
	echo "<div class=\"updated\"><p><strong>$alertMessage</strong></p></div>";
}

function my_recent_youtube_wpver_admin_notice() {
	$alertMessage = __("My Recent YouTube Widget requires WordPress 2.9 or higher");
	echo "<div class=\"updated\"><p><strong>$alertMessage</strong></p></div>";
}
?>