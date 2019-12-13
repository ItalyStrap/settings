<?php
/**
 * Settings file
 *
 * @package ItalyStrap
 */
declare(strict_types=1);

/**
 * Settings for the content area
 */
return [
	'general'	=> [
		'tab_title'			=> __( 'General', 'italystrap' ),
		'id'				=> 'general',
		'title'				=> __( 'General options page', 'italystrap' ),
		'desc'				=> __( 'General setting for ItalyStrap', 'italystrap' ),
		'settings_fields'	=> require 'fields.php',
	],
];
