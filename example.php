<?php
function d_footer( ...$args ) {
	\add_action( 'shutdown', function () use ( $args ) {
		d( ...$args );
	} );
}

$plugin_name = 'ItalyStrap';
$option_name = 'italystrap';

$settings = new \ItalyStrap\Settings\SettingsBuilder();
$settings->build(
	\ItalyStrap\Config\ConfigFactory::make( require __DIR__ . '/tests/_data/fixtures/config/settings.php' ),
	$option_name,
	$plugin_name,
	ITALYSTRAP_BASENAME
);

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

//$options_parser = new \ItalyStrap\Settings\OptionsParser( $options_obj );
//add_action( 'update_option', [ $options_parser, 'save' ], 10, 3 );

/**
 * Adjust priority to make sure this runs
 */
//\add_action( 'init', function () use ( $plugin ) {
	/**
	 * Load po file
	 */
//	\load_plugin_textdomain( $plugin['options_name'], null, \dirname( ITALYSTRAP_BASENAME ) . '/languages' );
//}, 100 );
