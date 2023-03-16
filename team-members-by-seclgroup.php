<?php
/**
 * Plugin Name: Team Members with ACF
 * Description: Gutenberg blocks for custom post type - Team Members
 * Plugin URI: https://api.github.com/repos/imsadhappy/seclgroup-team-members/releases
 * Author: SECL Group
 * Version: 1.0.6
 * Author URI: https://seclgroup.com
 * Text Domain: seclgroup-team-members
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'SECLGroup\Plugin_Loader' ) )
	require_once( plugin_dir_path( __FILE__ ) . 'includes/trait-seclgroup-plugin-loader.php' );

if ( ! class_exists( 'SECLGroup\Plugin_Updater' ) )
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-seclgroup-plugin-updater.php' );

new SECLGroup\Plugin_Updater( __FILE__ );

if ( ! class_exists('SECLGroup\Plugin_Factory') )
	require_once( plugin_dir_path( __FILE__ ) . 'includes/abstract-seclgroup-plugin-factory.php' );

if ( ! class_exists('SECLGroup\Team_Members') )
	require_once( plugin_dir_path( __FILE__ ) . 'includes/class-seclgroup-team-members.php' );

new SECLGroup\Team_Members( __FILE__ );
