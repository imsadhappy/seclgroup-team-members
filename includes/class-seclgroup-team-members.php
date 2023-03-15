<?php

namespace SECLGroup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists('SECLGroup\Team_Members') ) {
    return;
}

class Team_Members {

    private $dir;
    private $url;
	private $i18n;
	private static $file = null;
	private static $plugin = null;

	/**
	 * @return void
	 */
	public function __construct ( $file ) {

        if ( ! is_null( self::$plugin ) )
            return _doing_it_wrong( __FUNCTION__, __( 'Instance of '.__CLASS__.' already exists. Constructing new instances of this class is forbidden.' ), '1.0' );

		self::$file = $file;
        self::$plugin = get_file_data( $file, array(
			'Name' => 'Name',
			'Author' => 'Author',
			'Version' => 'Version',
			'Author URI' => 'Author URI',
			'Plugin URI' => 'Plugin URI',
			'Description' => 'Description',
			'Text Domain' => 'Text Domain',
			'Domain Path' => 'Domain Path'
		), 'plugin' );
        $this->url = plugin_dir_url( $file );
		$this->dir = plugin_dir_path( $file );
		$this->i18n = self::$plugin['Text Domain'];

		add_action( 'init', array( $this, 'hooks' ), 99 );
		add_action( 'team_member_template', array( $this, 'team_member_template' ) );
	}

	public function hooks () {

		unload_textdomain( $this->i18n );
		load_plugin_textdomain( $this->i18n, false, basename( dirname( self::$file ) ) . '/languages' );

		require_once( $this->dir . 'includes/post-types.php' );
        register_block_type( $this->dir . 'blocks/team-member-details' );
        register_block_type( $this->dir . 'blocks/team-members-list' );
    	require_once( $this->dir . 'includes/acf-fields.php' );
	}

	public function team_member_template ( $args = array() ) {

		wp_enqueue_style( 'dashicons' );

		extract($args);

		$template_path = apply_filters( 'team_member_template_path', $this->dir . 'includes/team-member-template.php', $args );

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
