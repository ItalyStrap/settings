<?php

use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Settings\Page;
use ItalyStrap\Settings\SettingsBuilder;

$domain = 'ItalyStrap';
$option_name = 'italystrap';

$settings = new SettingsBuilder(
	ConfigFactory::make( require __DIR__ . '/tests/_data/fixtures/config/settings.php' ),
	$option_name,
	$domain,
	ITALYSTRAP_BASENAME
);

//$settings->addPage(
//	[
//		Page::PARENT		=> 'italystrap-dashboard',
//		Page::PAGE_TITLE	=> \__( 'Dashboard 2', 'italystrap' ),
//		Page::MENU_TITLE	=> \__( 'Child1', 'italystrap' ),
//		Page::SLUG			=> 'ciao1',
//		Page::VIEW			=> __DIR__ . '/tests/_data/fixtures/view/settings_form.php',
//	]
//);

$settings->addPage(
	[
		Page::PARENT		=> 'options-general.php',
//		Page::PAGE_TITLE	=> \__( 'ItalyStrap Dashboard 2', 'italystrap' ),
		Page::MENU_TITLE	=> \__( 'Child-general', 'italystrap' ),
		Page::SLUG			=> 'Child-general',
		Page::VIEW			=> __DIR__ . '/tests/_data/fixtures/view/settings_form.php',
	]
);

$settings->addCustomLink( 'key-for-css', 'http://localhost.com', 'Custom', [ 'target' => '_blank' ] );

$settings->build();
