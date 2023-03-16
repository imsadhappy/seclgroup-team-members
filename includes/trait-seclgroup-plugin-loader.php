<?php

namespace SECLGroup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

trait Plugin_Loader {

    protected static $plugin = null;

	protected static function load ( $file ) {

        if ( ! is_null( self::$plugin ) )
            return _doing_it_wrong( __FUNCTION__, sprintf( __( 'Instance of %s already exists. Constructing new instances of this class is forbidden.' ), get_called_class() ), '1.0' );

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
}
