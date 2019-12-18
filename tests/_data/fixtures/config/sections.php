<?php
/**
 * Settings file
 *
 * @package ItalyStrap
 */
declare(strict_types=1);

use ItalyStrap\Settings\Sections as S;

/**
 * Settings for the content area
 */
return [
	[
		S::TAB_TITLE		=> __( 'General', 'italystrap' ),
		S::ID				=> 'general',
		S::TITLE			=> __( 'General options page', 'italystrap' ),
		S::DESC				=> __( 'General setting for ItalyStrap', 'italystrap' ),
		S::FIELDS			=> require 'fields.php',
	],
	[
		S::TAB_TITLE		=> __( 'Advanced', 'italystrap' ),
		S::ID				=> 'advanced',
		S::TITLE			=> __( 'Advanced options page', 'italystrap' ),
		S::DESC				=> __( 'Advanced setting for ItalyStrap', 'italystrap' ),
		S::FIELDS			=> require 'fields-advanced.php',
	],
];
