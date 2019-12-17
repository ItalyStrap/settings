<?php

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

$plugin = require __DIR__ . '/tests/_data/fixtures/config/plugin.php';
$sections = require __DIR__ . '/tests/_data/fixtures/config/sections.php';
$pages_config = require __DIR__ . '/tests/_data/fixtures/config/pages.php';

$options_obj = new \ItalyStrap\Settings\Options( $plugin['options_name'], $plugin['options_group'] );

$sections_obj = new \ItalyStrap\Settings\Sections(
	\ItalyStrap\Config\ConfigFactory::make( $sections ),
	new \ItalyStrap\Fields\Fields(),
	new \ItalyStrap\Settings\DataParser(),
	$options_obj
);

$settings_obj = new \ItalyStrap\Settings\Settings(
	$sections_obj,
	$options_obj,
	$plugin['capability']
);
add_action( 'admin_init', [ $settings_obj, 'load' ] );
add_action( 'update_option', [ $settings_obj, 'save' ], 10, 3 );

/**
 * ===================================
 *
 * ===================================
 */
$view_page = new \ItalyStrap\Settings\ViewPage();

$pages_obj = new \ItalyStrap\Settings\Page(
	\ItalyStrap\Config\ConfigFactory::make( $pages_config ),
	$sections_obj,
	$view_page
);

add_action( 'admin_menu', [ $pages_obj, 'load'] );

/**
 * Load script for ItalyStrap\Admin
 */
$asset = new \ItalyStrap\Settings\Asset();
add_action( 'admin_enqueue_scripts', [ $asset, 'enqueue'] );

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
