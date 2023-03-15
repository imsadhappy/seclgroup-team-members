<?php

namespace SECLGroup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists('SECLGroup\Updater') ) {
    return;
}

final class Updater {

    private static $github_url = null;
    private static $github_token = null;
    private static $dir = null;
    private static $slug = null;
    private static $file = null;
    private static $plugin = null;
    private static $basename = null;

    function __construct ( $file, $github_url, $github_token ) {

        if ( ! is_null( self::$plugin ) )
            return _doing_it_wrong( __FUNCTION__, __( 'Instance of '.__CLASS__.' already exists. Constructing new instances of this class is forbidden.' ), '1.0' );

        self::$file = $file;
        self::$github_url = $github_url;
        self::$github_token = $github_token;
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
        self::$basename = plugin_basename( $file );
        self::$slug = current(explode('/', self::$basename));
        self::$dir = plugin_dir_path( $file );

        add_filter( 'upgrader_pre_download', array( $this, 'pre_download' ) );
        add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
        add_filter( 'plugins_api_result', array( $this, 'plugin_popup' ), 10, 3 );
        add_filter( 'upgrader_post_install', array( $this, 'post_install' ), 10, 3 );
        add_filter( 'upgrader_install_package_result', array( $this, 'install_package_result' ), 10, 2 );
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'modify_transient' ), 10, 1 );
    }

    public function pre_download () {

        add_filter( 'http_request_args', array( $this, 'download_package' ), 15, 2 );

        return false;
    }

    public function post_install ( $response, $hook_extra, $result ) {

        global $wp_filesystem;

        $wp_filesystem->move( $result['destination'], self::$dir );
        $result['destination'] = self::$dir;

        if ( is_plugin_active(self::$basename) )
            activate_plugin(self::$basename);

        return $result;
    }

    public function install_package_result ( $result, $hook_extra ) {

        if (is_array($result) && isset($hook_extra['plugin']) && $hook_extra['plugin'] === self::$basename)
            $result['destination_name'] = self::$slug;

        return $result;
    }

    public function plugin_popup ( $result, $action, $args ) {

        if ( empty( $args->slug ) )
            return $result;

        if ( $args->slug == self::$slug ) {

            $github_info = $this->get_github_info();

            if ( is_array($github_info) )
                $result = (object) array(
                    'name'              => self::$plugin['Name'],
                    'slug'              => self::$basename,
                    'version'           => $github_info['tag_name'],
                    'author'            => self::$plugin['Author'],
                    'author_profile'    => self::$plugin['Author URI'],
                    'last_updated'      => $github_info['published_at'],
                    'homepage'          => self::$plugin['Plugin URI'],
                    'short_description' => self::$plugin['Description'],
                    'sections'          => array(
                        'Description'   => self::$plugin['Description'],
                        'Updates'       => $github_info['body'],
                    ),
                    'download_link'     => $github_info['zipball_url']
                );
        }

        return $result;
    }

    public function download_package ( $args, $url ) {

        if ( ! empty($args['filename']) )
            $args = $this->add_authorization_header($args);

        remove_filter( 'http_request_args', array( $this, 'download_package' ) );

        return $args;
    }

    public function modify_transient ( $transient ) {

        if ( ! property_exists( $transient, 'checked') )
            return $transient;

        $checked = $transient->checked;

        if ( ! $checked )
            return $transient;

        $github_info = $this->get_github_info();

        if ( ! is_array($github_info) )
            return $transient;

        if ( version_compare( $github_info['tag_name'], $checked[self::$basename], 'gt' ) )
            $transient->response[self::$basename] = (object) array(
                'url' => self::$plugin['Plugin URI'],
                'slug' => self::$slug,
                'package' => $github_info['zipball_url'],
                'new_version' => $github_info['tag_name']
            );

        return $transient;
    }

    private function get_github_info () {

        $request = wp_remote_get( self::$github_url, $this->add_authorization_header() );
        $response = json_decode( wp_remote_retrieve_body( $request ), true );

        if ( is_array( $response ) )
            return current( $response );

        return false;
    }

    private function add_authorization_header ( $args = array() ) {

        $token = self::$github_token;
        return array_merge( $args, array( "headers" => array( "Authorization" => "token $token" ) ) );
    }

	public function plugin_row_meta ( $links, $file ) {

        if (strpos(self::$file, $file) !== false)
            $links['details'] = sprintf('<a href="%s" class="thickbox">%s</a>',
				self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . self::$slug . '&amp;TB_iframe=true&amp;width=600&amp;height=550' ),
				__( 'View details' )
			);

        return $links;
    }
}
