<?php
/**
 * Plugin Name: Team Members with ACF
 * Description: Gutenberg blocks for custom post type - Team Members
 * Plugin URI: https://seclgroup.com
 * Author: SECL Group
 * Version: 1.0.1
 * Author URI: https://seclgroup.com
 * Text Domain: seclgroup-team-members
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-seclgroup-team-members.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-seclgroup-updater.php' );

new SECLGroup\Updater(
	__FILE__,
	'https://api.github.com/repos/imsadhappy/seclgroup-team-members/releases',
	'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'
);
new SECLGroup\Team_Members( __FILE__ );

register_activation_hook( __FILE__, array( 'SECLGroup\Team_Members', '__activate' ) );
register_deactivation_hook( __FILE__, array( 'SECLGroup\Team_Members', '__deactivate' ) );
