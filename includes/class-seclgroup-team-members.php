<?php

namespace SECLGroup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Team_Members {

	private static $plugin = null;

	private static function load ( $file ) {

        self::$plugin = get_file_data( $file, array(
			'Plugin Name' => 'Plugin Name',
			'Author' => 'Author',
			'Version' => 'Version',
			'Author URI' => 'Author URI',
			'Plugin URI' => 'Plugin URI',
			'Description' => 'Description',
			'Text Domain' => 'Text Domain',
			'Domain Path' => 'Domain Path'
		), 'plugin' );

		self::$plugin['file'] = $file;
		self::$plugin['basename'] = plugin_basename( $file );
		self::$plugin['slug'] = current( explode('/', self::$plugin['basename']) );
        self::$plugin['url'] = plugin_dir_url( $file );
		self::$plugin['dir'] = plugin_dir_path( $file );
	}

	public function __construct ( $file ) {

        if ( ! is_null( self::$plugin ) )
            return _doing_it_wrong( __FUNCTION__, __( 'Instance of '.__CLASS__.' already exists. Constructing new instances of this class is forbidden.' ), '1.0' );

		self::load( $file );

		if ( ! class_exists( 'SECLGroup\Updater' ) )
			require_once( self::$plugin['dir'] . 'includes/class-seclgroup-updater.php' );

		new Updater( self::$plugin );

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

		register_block_type( self::$plugin['dir'] . 'blocks/team-member-details' );
        register_block_type( self::$plugin['dir'] . 'blocks/team-members-list' );

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

	/**
	 * @return void
	 */
	public static function __activate () {

        if ( ! self::check_admin_referer('activate') )
            return;

		add_action( 'wp_loaded', 'flush_rewrite_rules', 99 );
	}

	/**
	 * @return void
	 */
	public static function __deactivate () {

        if ( ! self::check_admin_referer('deactivate') )
            return;

		add_action( 'wp_loaded', 'flush_rewrite_rules', 99 );
	}

    /**
     * @return bool
     */
    private static function check_admin_referer ( $action ) {

		if ( ! current_user_can( 'activate_plugins' ) )
			return false;

        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';

        return check_admin_referer( "{$action}-plugin_{$plugin}" );
    }
}
