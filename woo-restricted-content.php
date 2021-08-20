<?php
/**
 */
/*
Plugin Name: Restricted content based on purchase
Plugin URI: https://wordpress.org/plugins/woo-restricted-content
Description: Restricted content for users who have not purchased the indicated product
Version: 1.0.0
Author: Maciej Molenda
Author URI: https://profiles.wordpress.org/maciex777/
License: GPLv2 or later
*/

defined( 'ABSPATH' ) || exit;

define( 'WOORESCON_URL', plugin_dir_url( __FILE__ ) );
define( 'WOORESCON_DIR', dirname( plugin_basename( __FILE__ ) ) );

include_once('inc/functions.php');
include_once('inc/menu.php');
include_once('inc/post_options.php');
include_once('inc/shortcode.php');
