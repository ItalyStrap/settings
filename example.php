<?php

use ItalyStrap\Settings\Page;
use ItalyStrap\Settings\SettingsBuilder;

$text_domain = 'ItalyStrap';
$option_name = 'italystrap';
$settings_config = \ItalyStrap\Config\ConfigFactory::make(
	require __DIR__ . '/tests/_data/fixtures/config/settings.php'
);

$file = new \SplFileObject( ITALYSTRAP_FILE );

//d_footer(
//	ITALYSTRAP_FILE,
//	$file->getRealPath(),
//	dirname( plugin_basename( $file->getRealPath() ) ),
//	$file->getBasename(),
//	$file->getFilename()
//);




$default_headers = array(
	'Name'        => 'Plugin Name',
	'PluginURI'   => 'Plugin URI',
	'Version'     => 'Version',
	'Description' => 'Description',
	'Author'      => 'Author',
	'AuthorURI'   => 'Author URI',
	'TextDomain'  => 'Text Domain',
	'DomainPath'  => 'Domain Path',
	'Network'     => 'Network',
	'RequiresWP'  => 'Requires at least',
	'RequiresPHP' => 'Requires PHP',
);

//d_footer( get_file_data( ITALYSTRAP_FILE, $default_headers ) );
////
//add_action( 'admin_init', function () {
//	d(get_plugin_data( ITALYSTRAP_FILE ));
//} );

// Initialize the builder
$settings = new SettingsBuilder(
	$option_name,
	$text_domain,
	ITALYSTRAP_BASENAME
);

// You can add configuration via the \ItalyStrap\Config\ConfigFactory::class
$settings->addPage(
	$settings_config->get( 'page' ),
	$settings_config->get( 'sections' )
);

// Ora manually
// The section parameter is optional
// Not every page need a section with fields
// For example in a docs page
// Manu title and slug are mandatory
$settings->addPage(
	[
		Page::PARENT		=> 'italystrap-dashboard',
		Page::PAGE_TITLE	=> \__( 'Dashboard 2', 'italystrap' ),
		Page::MENU_TITLE	=> \__( 'Child1', 'italystrap' ),
		Page::SLUG			=> 'slug-for-child-page',
		Page::VIEW			=> __DIR__ . '/tests/_data/fixtures/view/empty_form.php',
	]
);

// You can also add a sub page either for you parent page or for the WP admin pages
$settings->addPage(
	[
		Page::PARENT		=> 'options-general.php',
//		Page::PAGE_TITLE	=> \__( 'ItalyStrap Dashboard 2', 'italystrap' ),
		Page::MENU_TITLE	=> \__( 'Child-general', 'italystrap' ),
		Page::SLUG			=> 'slug-for-child-general',
		Page::VIEW			=> __DIR__ . '/tests/_data/fixtures/view/empty_form.php',
	]
);

// You can also add a link to the plugins.php page in your plugin link for activation
// For example if you want to add an external link to your docs.
$settings->addCustomPluginLink(
	'key-for-css',
	'http://localhost.com',
	'Custom',
	[ 'target' => '_blank' ]
);

// After you added pages[?section] and/or link call the build() method.
$settings->build();
