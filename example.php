<?php

use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Settings\Page;
use ItalyStrap\Settings\SettingsBuilder;
use ItalyStrap\Settings\ViewPage;

$domain = 'ItalyStrap';
$option_name = 'italystrap';
$settings_config = require __DIR__ . '/tests/_data/fixtures/config/settings.php';

$settings = new SettingsBuilder(
	$option_name,
	$domain,
	ITALYSTRAP_BASENAME
);

$settings->addPage(
	$settings_config[0]['page'],
	$settings_config[0]['sections']
);

$settings->addPage(
	[
		Page::PARENT		=> 'italystrap-dashboard',
		Page::PAGE_TITLE	=> \__( 'Dashboard 2', 'italystrap' ),
		Page::MENU_TITLE	=> \__( 'Child1', 'italystrap' ),
		Page::SLUG			=> 'slug-for-child-page',
		Page::VIEW			=> __DIR__ . '/tests/_data/fixtures/view/empty_form.php',
	]
);

$settings->addPage(
	[
		Page::PARENT		=> 'options-general.php',
//		Page::PAGE_TITLE	=> \__( 'ItalyStrap Dashboard 2', 'italystrap' ),
		Page::MENU_TITLE	=> \__( 'Child-general', 'italystrap' ),
		Page::SLUG			=> 'slug-for-child-general',
		Page::VIEW			=> __DIR__ . '/tests/_data/fixtures/view/empty_form.php',
	]
);

$settings->addCustomPluginLink(
	'key-for-css',
	'http://localhost.com',
	'Custom',
	[ 'target' => '_blank' ]
);

$settings->build();
