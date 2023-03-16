<?php

namespace SECLGroup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

final class Plugin_Updater {

    use Plugin_Loader;

    public function __construct ( $file ) {

        self::load( $file );

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

        $wp_filesystem->move( $result['destination'], self::$plugin['dir'] );
        $result['destination'] = self::$plugin['dir'];

        if ( is_plugin_active(self::$plugin['basename']) )
            activate_plugin(self::$plugin['basename']);

        return $result;
    }

    public function install_package_result ( $result, $hook_extra ) {

        if (is_array($result) && isset($hook_extra['plugin']) && $hook_extra['plugin'] === self::$plugin['basename'])
            $result['destination_name'] = self::$plugin['slug'];

        return $result;
    }

    public function plugin_popup ( $result, $action, $args ) {

        if ( empty( $args->slug ) )
            return $result;

        if ( $args->slug == self::$plugin['slug'] ) {

            $github_info = $this->get_github_info();

            if ( is_array($github_info) )
                $result = (object) array(
                    'name'              => self::$plugin['Plugin Name'],
                    'slug'              => self::$plugin['basename'],
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

        if ( version_compare( $github_info['tag_name'], $checked[self::$plugin['basename']], 'gt' ) )
            $transient->response[self::$plugin['basename']] = (object) array(
                'url' => self::$plugin['Plugin URI'],
                'slug' => self::$plugin['slug'],
                'package' => $github_info['zipball_url'],
                'new_version' => $github_info['tag_name']
            );

        return $transient;
    }

    private function get_github_info () {

        $request = wp_remote_get( self::$plugin['Plugin URI'] );
        $response = json_decode( wp_remote_retrieve_body( $request ), true );

        if ( is_array( $response ) )
            return current( $response );

        return false;
    }

	public function plugin_row_meta ( $links, $file ) {

        if (self::$plugin['basename'] === $file)
            $links['details'] = sprintf('<a href="%s" class="thickbox">%s</a>',
				self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin=' . self::$plugin['slug'] . '&amp;TB_iframe=true&amp;width=600&amp;height=550' ),
				__( 'View details' )
			);

        return $links;
    }
}
