<?php

namespace SECLGroup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Team_Members extends Plugin_Factory {

	protected function hooks () {

		add_action( 'init', array( $this, 'init' ), 99 );
		add_action( 'team_member_template', array( $this, 'team_member_template' ) );
	}

	public function init () {

		$i18n = self::$plugin['Text Domain'];

		unload_textdomain( $i18n );
		load_plugin_textdomain( $i18n, false, basename( self::$plugin['dir'] ) . self::$plugin['Domain Path'] );

		if ( ! class_exists('ACF') ) {

			add_action( 'admin_notices', function () use ($i18n) {
				printf( '<div class="notice notice-error"><p>"%s" %s</p></div>',
						esc_html__(self::$plugin['Plugin Name'], $i18n),
						esc_html__('plugin requires ACF plugin to work properly', $i18n) );
			} );

			return;
		}

		wp_register_style( $i18n, self::$plugin['url'] . 'assets/team-members.css' );

		if ( ! post_type_exists('team_members') )
			require_once( self::$plugin['dir'] . 'includes/team-members-post-type.php' );

		if ( function_exists('register_block_type') ) {
			register_block_type( self::$plugin['dir'] . 'blocks/team-member-details' );
        	register_block_type( self::$plugin['dir'] . 'blocks/team-members-list' );
		}

		if ( function_exists('acf_add_local_field_group') )
    		require_once( self::$plugin['dir'] . 'includes/team-members-acf-fields.php' );
	}

	public function team_member_template ( $args = array() ) {

		if ( ! wp_style_is( 'dashicons' ) )
			wp_enqueue_style( 'dashicons' );

		if ( ! wp_style_is( 'seclgroup-team-members' ) )
			wp_enqueue_style( 'seclgroup-team-members' );

		extract($args);

		$template_path = apply_filters( 'team_member_template_path', self::$plugin['dir'] . 'includes/team-member-template.php', $args );

		include( $template_path );
	}
}
