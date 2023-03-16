<?php
/**
 * Plugin Name: Team Members with ACF
 * Description: Gutenberg blocks for custom post type - Team Members
 * Plugin URI: https://api.github.com/repos/imsadhappy/seclgroup-team-members/releases
 * Author: SECL Group
 * Version: 1.0.4
 * Author URI: https://seclgroup.com
 * Text Domain: seclgroup-team-members
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists('SECLGroup\Team_Members') )
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-seclgroup-team-members.php' );

new SECLGroup\Team_Members( __FILE__ );

register_activation_hook( __FILE__, array( 'SECLGroup\Team_Members', '__activate' ) );
register_deactivation_hook( __FILE__, array( 'SECLGroup\Team_Members', '__deactivate' ) );
