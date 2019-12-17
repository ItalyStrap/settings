<?php
declare(strict_types=1);

use ItalyStrap\Settings\Page as P;

return [
	'page'	=> [
		P::PAGE_TITLE	=> \__( 'ItalyStrap Dashboard', 'italystrap' ),
		P::MENU_TITLE	=> \__( 'ItalyStrap', 'italystrap' ),
		P::CAPABILITY	=> 'manage_options', // Optional
		P::SLUG			=> 'italystrap-dashboard',
//		P::VIEW_CALLBACK	=> function () { echo 'Settings Page'; },
		P::ICON			=> 'dashicons-performance',
		P::POSITION		=> null,
		P::VIEW			=> '',
//		'pages'	=> [
//			[
//				P::PAGE_TITLE	=> \__( 'Settings', 'italystrap' ),
//				P::MENU_TITLE	=> \__( 'Settings', 'italystrap' ),
//				P::SLUG		=> 'italystrap-settings',
//				// P::VIEW_CALLBACK	=> function () { echo 'Settings Page'; },
//			],
//		],
	],
];
