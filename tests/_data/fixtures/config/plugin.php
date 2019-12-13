<?php
/**
 * Plugin default value
 *
 * @package Perf_Metrics
 */
declare(strict_types=1);

namespace Perf_Metrics;

$plugin_name = 'ItalyStrap';

// italystrap
$page_slug = \strtolower( \str_replace( ' ', '-', $plugin_name ) );

// italystrap
$key = \strtolower( \str_replace( ' ', '_', $plugin_name ) );

return [

//	'basename'				=> ITALYSTRAP_BASENAME,
//
	'capability'			=> 'manage_options',
//
//	'plugin_name'			=> $plugin_name,
//	'page_slug'				=> $page_slug,

	// italystrap_options
	'options_name'			=> $page_slug,
	'options_group'			=> "{$key}_options_group",

//	'bloginfo_name'				=> \esc_attr( get_bloginfo( 'name' ) ),
//	'home_url'					=> \get_home_url( null, '/' ),
//	'ajax_url'				=> \admin_url( 'admin-ajax.php' ),
];
