<?php
declare(strict_types=1);

use ItalyStrap\Settings\Pages as P;

return [
	P::PAGE_TITLE	=> \__( 'ItalyStrap Dashboard', 'italystrap' ),
	P::MENU_TITLE	=> \__( 'ItalyStrap', 'italystrap' ), // Mandatory
//	P::CAPABILITY	=> 'manage_options', // Optional
	P::SLUG			=> 'italystrap-dashboard', // Mandatory
//	P::CALLBACK	=> function () { echo 'Settings Page'; },
//	P::ICON			=> 'dashicons-performance',
//	P::POSITION		=> null,
//	P::VIEW			=> 'parent',
];
