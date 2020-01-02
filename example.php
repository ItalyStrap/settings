<?php

use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Settings\Page;
use ItalyStrap\Settings\SettingsBuilder;
use ItalyStrap\Settings\ViewPage;

function d_footer( ...$args ) {
	\add_action( 'shutdown', function () use ( $args ) {
		d( ...$args );
	} );
}

$plugin_name = 'ItalyStrap';
$option_name = 'italystrap';

$settings = new SettingsBuilder();
$settings->build(
	ConfigFactory::make( require __DIR__ . '/tests/_data/fixtures/config/settings.php' ),
	$option_name,
	$plugin_name,
	ITALYSTRAP_BASENAME
);

$pages_obj2 = new Page(
	ConfigFactory::make( 	[
		'parent'		=> 'italystrap-dashboard',
//		'page_title'	=> \__( 'ItalyStrap Dashboard 2', 'italystrap' ),
		'menu_title'	=> \__( 'Child', 'italystrap' ),
		'menu_slug'			=> 'ciao',
//		P::VIEW			=> 'child',
		'view'			=> __DIR__ . '/tests/_data/fixtures/view/settings_form.php',
	] ),
	new ViewPage()
);
$pages_obj2->boot();

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
