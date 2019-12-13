<?php
/*
Plugin Name: Settings
Description: Settings API for WordPress
Plugin URI: https://italystrap.com
Author: Enea Overclokk
Author URI: https://italystrap.com
Version: 1.0.0
License: GPL2
Text Domain: Text Domain
Domain Path: Domain Path
*/

/*

    Copyright (C) Year  Enea Overclokk  Email

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Example = F:\xampp\htdocs\italystrap\wp-content\plugins\italystrap-extended\italystrap.php
 */
if ( ! defined( 'ITALYSTRAP_FILE' ) ) {
	define( 'ITALYSTRAP_FILE', __FILE__ );
}

/**
 * Example = F:\xampp\htdocs\italystrap\wp-content\plugins\italystrap-extended/
 */
if ( ! defined( 'ITALYSTRAP_PLUGIN_PATH' ) ) {
	define( 'ITALYSTRAP_PLUGIN_PATH', plugin_dir_path( ITALYSTRAP_FILE ) );
}

/**
 * Example: 'http://192.168.1.10/italystrap/wp-content/plugins/italystrap-extended/'
 */
if ( ! defined( 'ITALYSTRAP_PLUGIN_URL' ) ) {
	define( 'ITALYSTRAP_PLUGIN_URL', plugin_dir_url( ITALYSTRAP_FILE ) );
}

/**
 * Example = italystrap-extended/italystrap.php
 */
if ( ! defined( 'ITALYSTRAP_BASENAME' ) ) {
	define( 'ITALYSTRAP_BASENAME', plugin_basename( ITALYSTRAP_FILE ) );
}

/**
 * This could be loaded on MU plugins
 */
//function italystrap_load () {
//	$files = [
//		'vendor/autoload.php',
//		'bootstrap.php',
//	];
//
//	foreach ( $files as $file ) {
//		require_once $file;
//	}
//}
//
//\add_action( 'plugins_loaded', 'italystrap_load' );

require( __DIR__ . '/vendor/autoload.php' );

$plugin = require __DIR__ . '/config/plugin.php';
$settings = require __DIR__ . '/config/settings.php';

$injector = new  \Auryn\Injector();

/**
 * @var \ItalyStrap\Settings\Settings
 */
//$settings_obj = $injector->make( \ItalyStrap\Settings\Settings::class, [
//	':options'		=> (array) \get_option( $plugin['options_name'] ),
//	':settings'		=> $settings,
//	':plugin'		=> $plugin,
//	':theme_mods'	=> (array) \get_theme_mods()
//] );

$settings_obj = new \ItalyStrap\Settings\Settings(
	(array) \get_option( $plugin['options_name'] ),
	$settings,
	$plugin,
	(array) \get_theme_mods(),
	new \ItalyStrap\Fields\Fields(),
	new \ItalyStrap\View\View( new \ItalyStrap\View\ViewFinder() )
);

add_action( 'admin_init', array( $settings_obj, 'load') );


$config_pages = \ItalyStrap\Config\ConfigFactory::make( require __DIR__ . '/config/pages.php' );

$finder = new \ItalyStrap\View\ViewFinder();
$finder->in( ITALYSTRAP_PLUGIN_PATH . 'src/Settings/view/' );
$view = new \ItalyStrap\View\View( $finder );

$pages = new \ItalyStrap\Settings\Pages( $config_pages, $view, $settings, $plugin['options_group'] );
add_action( 'admin_menu', [ $pages, 'load'] );
add_action( 'italystrap_after_settings_form', [ $pages, 'getAside' ] );

/**
 * Load script for ItalyStrap\Admin
 */
//$asset = new \ItalyStrap\Settings\Asset( \ItalyStrap\Config\ConfigFactory::make( $plugin ) );
//add_action( 'admin_enqueue_scripts', [ $asset, 'enqueue'] );

/**
 * Add link in plugin activation panel
 * Vedi Plugin_Link
 */
//add_filter( 'plugin_action_links_' . ITALYSTRAP_BASENAME, array( $settings_obj, 'pluginActionLinks' ) );
//add_filter( 'plugin_row_meta' , array( $settings_obj, 'pluginRowMeta' ), 10, 4 );

/**
 * Adjust priority to make sure this runs
 */
\add_action( 'init', function () use ( $plugin ) {
	/**
	 * Load po file
	 */
//	\load_plugin_textdomain( $plugin['options_name'], null, \dirname( ITALYSTRAP_BASENAME ) . '/languages' );
}, 100 );

/**
 * debug_example
 */
function settings_example() {
}

add_action( 'wp_footer', 'settings_example' );
