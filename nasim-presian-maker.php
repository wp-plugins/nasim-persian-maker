<?php
/**
* Plugin Name: Nasim Persian Maker
* Plugin URI: http://nasimnet.ir
* Description: You can make the [Zerif Lite] theme Persian and rtl easily, by installing an activating “Nasim Persian Maker” plugin.
* Version: 1.0
* Author: M.Motahari
* Author URI: http://nasimnet.ir
* License: GPL2
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class PersianThemePlugin {
	/**
	* The current langauge
	*/
	private $language;
	private $is_persian;
	public function __construct( $file ) {
		$this->file = $file;
		
		add_action( 'plugins_loaded', array( $this, 'load_mo_file' ) );
		add_action( 'activated_plugin', array( $this, 'activated_plugin' ) );
	}
	
	public function activated_plugin() {
		$path = str_replace( WP_PLUGIN_DIR . '/', '', $this->file );
		
		if ( $plugins = get_option( 'active_plugins' ) ) {
			if ( $key = array_search( $path, $plugins ) ) {
				array_splice( $plugins, $key, 1 );
				array_unshift( $plugins, $path );
				
				update_option( 'active_plugins', $plugins );
			}
		}
	}
	
	public function load_mo_file() {
		$rel_path = dirname( plugin_basename( $this->file ) ) . '/languages/';
		$dir    = plugin_dir_path( __FILE__ );
		
		if ( $this->language == null ) {
			$this->language = get_option( 'WPLANG', WPLANG );
			$this->is_persian = ( $this->language == 'fa' || $this->language == 'fa_IR' );
		}
		
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$this->is_persian = ( ICL_LANGUAGE_CODE == 'fa' );
		}
		
		$curLang = substr(get_bloginfo( 'language' ), 0, 2);
		
		if ( $this->is_persian || strtolower($curLang) == 'fa' ) {
			load_textdomain( 'zerif-lite', $dir . 'languages/fa_IR.mo' );
		}
	}
}

global $persian_theme;
$persian_theme = new PersianThemePlugin( __FILE__ );


add_action( 'wp_head','persian_theme_css');
function persian_theme_css() {
	wp_register_style('rtl', plugins_url('assets/css/rtl.css',__FILE__ ));
	wp_enqueue_style('rtl');
	
	wp_register_style ('bootstrap-rtl', plugins_url('assets/css/bootstrap-rtl.min.css', __FILE__));
	wp_enqueue_style('bootstrap-rtl');
}

add_action( 'admin_init','persian_theme_admin_css');
function persian_theme_admin_css() {
	wp_register_style('admin-rtl', plugins_url('assets/css/admin-rtl.css',__FILE__ ));
	wp_enqueue_style('admin-rtl');

}
