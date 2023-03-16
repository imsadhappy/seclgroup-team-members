<?php

namespace SECLGroup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Plugin_Factory {

    use Plugin_Loader;

    protected static function hooks( $instance ) {}

	public function __construct ( $file ) {

		register_activation_hook( $file, array( get_called_class(), '__activate' ) );
		register_deactivation_hook( $file, array( get_called_class(), '__deactivate' ) );

		self::load( $file );
		self::hooks( $this );
	}

	public static function __activate () {

        if ( ! self::check_admin_referer('activate') )
            return;

		add_action( 'wp_loaded', 'flush_rewrite_rules', 99 );
	}

	public static function __deactivate () {

        if ( ! self::check_admin_referer('deactivate') )
            return;

		add_action( 'wp_loaded', 'flush_rewrite_rules', 99 );
	}

    /**
     * @return bool
     */
    protected static function check_admin_referer ( $action ) {

		if ( ! current_user_can( 'activate_plugins' ) )
			return false;

        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';

        return check_admin_referer( "{$action}-plugin_{$plugin}" );
    }
}
