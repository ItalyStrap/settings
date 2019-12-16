<?php
declare(strict_types=1);

use ItalyStrap\Settings\Pages as P;

return [

	'admin_view_path'		=> ITALYSTRAP_PLUGIN_PATH . 'admin/view/',

	'page'	=> [
		P::PAGE_TITLE	=> \__( 'ItalyStrap Dashboard', 'italystrap' ),
		P::MENU_TITLE	=> \__( 'ItalyStrap', 'italystrap' ),
		P::CAPABILITY	=> 'manage_options',
		P::SLUG			=> 'italystrap-dashboard',
//		P::VIEW_CALLBACK	=> function () { echo 'Settings Page'; },
		P::ICON			=> 'dashicons-performance',
		P::POSITION		=> null,
		P::VIEW			=> '',
		'pages'	=> [
			[
				P::PAGE_TITLE	=> \__( 'Settings', 'italystrap' ),
				P::MENU_TITLE	=> \__( 'Settings', 'italystrap' ),
				P::SLUG		=> 'italystrap-settings',
				// P::VIEW_CALLBACK	=> function () { echo 'Settings Page' },
			],
		],
	],
];
