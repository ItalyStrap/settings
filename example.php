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

$plugin_name = 'ItalyStrap';
$option_name = 'italystrap';
$option_group = 'italystrap_options_group';

$options_obj = new \ItalyStrap\Settings\Options( $option_name );

\add_action( 'admin_footer', function () use ( $options_obj ) {
//	d( $options_obj->get() );
} );

$sections = require __DIR__ . '/tests/_data/fixtures/config/sections.php';

$sections_obj = new \ItalyStrap\Settings\Sections(
	\ItalyStrap\Config\ConfigFactory::make( $sections ),
	new \ItalyStrap\Fields\Fields(),
	\ItalyStrap\DataParser\DataParserFactory::make( $plugin_name ),
	$options_obj
);
add_action( 'admin_init', [ $sections_obj, 'register'] );

/**
 * ===================================
 *
 * ===================================
 */
$pages_config = require __DIR__ . '/tests/_data/fixtures/config/pages.php';

$pages_obj = new \ItalyStrap\Settings\Pages(
	\ItalyStrap\Config\ConfigFactory::make( $pages_config ),
	$sections_obj,
	new \ItalyStrap\Settings\ViewPage()
);
add_action( 'admin_menu', [ $pages_obj, 'register'] );

/**
 * Load script for Tabbed admin page
 */
$asset = new \ItalyStrap\Settings\Asset();
add_action( 'admin_enqueue_scripts', [ $asset, 'enqueue'] );

$options_parser = new \ItalyStrap\Settings\OptionsParser( $options_obj );
add_action( 'update_option', [ $options_parser, 'save' ], 10, 3 );

/**
 * Add link in plugin activation panel
 * Vedi Plugin_Link
 */
//add_filter( 'plugin_action_links_' . ITALYSTRAP_BASENAME, array( $settings_obj, 'pluginActionLinks' ) );
//add_filter( 'plugin_row_meta' , array( $settings_obj, 'pluginRowMeta' ), 10, 4 );

/**
 * Adjust priority to make sure this runs
 */
//\add_action( 'init', function () use ( $plugin ) {
	/**
	 * Load po file
	 */
//	\load_plugin_textdomain( $plugin['options_name'], null, \dirname( ITALYSTRAP_BASENAME ) . '/languages' );
//}, 100 );
