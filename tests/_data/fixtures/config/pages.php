<?php
declare(strict_types=1);

use ItalyStrap\Settings\Page as P;

return [
	[
//		P::PAGE_TITLE	=> \__( 'ItalyStrap Dashboard', 'italystrap' ),
		P::MENU_TITLE	=> \__( 'ItalyStrap', 'italystrap' ), // Mandatory
		P::CAPABILITY	=> 'manage_options', // Optional
		P::SLUG			=> 'italystrap-dashboard', // Mandatory
//		P::CALLBACK	=> function () { echo 'Settings Page'; },
		P::ICON			=> 'dashicons-performance',
//		P::POSITION		=> null,
		P::VIEW			=> 'parent',
	],
	[
		P::PARENT		=> 'italystrap-dashboard',
		P::PAGE_TITLE	=> \__( 'ItalyStrap Dashboard 2', 'italystrap' ),
		P::MENU_TITLE	=> \__( 'Child', 'italystrap' ),
		P::SLUG			=> 'ciao',
//		P::VIEW			=> 'child',
		P::VIEW			=> __DIR__ . '/../view/settings_form.php',
	],
//	[
//		P::PAGE_TITLE	=> \__( 'ItalyStrap Dashboard 2', 'italystrap' ),
//		P::MENU_TITLE	=> \__( 'ItalyStrap 2', 'italystrap' ),
////		P::CAPABILITY	=> 'manage_options', // Optional
//		P::SLUG			=> 'italystrap-dashboard-2',
////		P::CALLBACK	=> function () { echo 'Settings Page'; },
//		P::ICON			=> 'dashicons-performance',
//		P::POSITION		=> null,
//		P::VIEW			=> '',
//	],
];
