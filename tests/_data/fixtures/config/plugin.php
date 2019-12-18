<?php
/**
 * Plugin default value
 *
 * @package ItalyStrap
 */
declare(strict_types=1);

namespace ItalyStrap;

$plugin_name = 'ItalyStrap';

// italystrap
$page_slug = \strtolower( \str_replace( ' ', '-', $plugin_name ) );

// italystrap
$key = \strtolower( \str_replace( ' ', '_', $plugin_name ) );

return [
	// italystrap_options
	'options_name'			=> $page_slug,
	'options_group'			=> "{$key}_options_group",
];
