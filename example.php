<?php
function d_footer ( ...$args ) {
	\add_action( 'shutdown', function () use ( $args ) {
		d( ...$args );
	} );
}


$plugin_name = 'ItalyStrap';
$option_name = 'italystrap';

$options_obj = new \ItalyStrap\Settings\Options( $option_name );

$sections_obj = new \ItalyStrap\Settings\Sections(
	\ItalyStrap\Config\ConfigFactory::make( require __DIR__ . '/tests/_data/fixtures/config/sections.php' ),
	new \ItalyStrap\Fields\Fields(),
	\ItalyStrap\DataParser\ParserFactory::make( $plugin_name ),
	$options_obj
);
add_action( 'admin_init', [ $sections_obj, 'register'] );

/**
 * ===================================
 *
 * ===================================
 */
$pages_obj = new \ItalyStrap\Settings\Page(
	\ItalyStrap\Config\ConfigFactory::make( require __DIR__ . '/tests/_data/fixtures/config/page.php' ),
	new \ItalyStrap\Settings\ViewPage(),
	$sections_obj
);
$pages_obj->boot();

$pages_obj2 = new \ItalyStrap\Settings\Page(
	\ItalyStrap\Config\ConfigFactory::make( 	[
		'parent'		=> 'italystrap-dashboard',
//		'page_title'	=> \__( 'ItalyStrap Dashboard 2', 'italystrap' ),
		'menu_title'	=> \__( 'Child', 'italystrap' ),
		'menu_slug'			=> 'ciao',
//		P::VIEW			=> 'child',
		'view'			=> __DIR__ . '/tests/_data/fixtures/view/settings_form.php',
	] ),
	new \ItalyStrap\Settings\ViewPage()
);
$pages_obj2->boot();

/**
 * Load script for Tabbed admin page
 */
$asset = new \ItalyStrap\Settings\AssetLoader();
add_action( 'admin_enqueue_scripts', [ $asset, 'load'] );

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
