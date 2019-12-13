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

// perf_metrics
$key = \strtolower( \str_replace( ' ', '_', $plugin_name ) );

$old = array(
	'plugin_action_links'	=> array(
		'<a href="admin.php?page=italystrap-settings">' . \__( 'Settings', 'italystrap' ) . '</a>',
		'<a href="http://docs.italystrap.com/" target="_blank">' . \__( 'Docs', 'italystrap' ) . '</a>',
		'<a href="https://italystrap.com/" target="_blank">ItalyStrap</a>',
	),
	'plugin_row_meta'		=> array(
		'<a href="admin.php?page=italystrap-settings">' . \__( 'Settings', 'italystrap' ) . '</a>',
		'<a href="http://docs.italystrap.com/" target="_blank">' . \__( 'Doc', 'italystrap' ) . '</a>',
		'<a href="https://italystrap.com/" target="_blank">ItalyStrap</a>',
	),
);

return [

	'basename'				=> ITALYSTRAP_BASENAME,

	'admin_view_path'		=> ITALYSTRAP_PLUGIN_PATH . 'admin/view/',
	'capability'			=> 'manage_options',

	'plugin_name'			=> $plugin_name,
	'page_slug'				=> $page_slug,

	// perf_metrics_options
	'options_name'			=> $page_slug,
	'options_group'			=> "{$key}_options_group",

	// 'meta_key'			=> '_perf_metrics_meta',
	'meta_key_prefix'		=> "_{$key}_meta_",

//	'plugin_dir_path'			=> ITALYSTRAP_PLUGIN_PATH,
//	'plugin_dir_template_name'	=> 'templates',

	'post_type_name'		=> $key,

//	'bloginfo_name'				=> \esc_attr( get_bloginfo( 'name' ) ),
//	'home_url'					=> \get_home_url( null, '/' ),
	'ajax_url'				=> \admin_url( 'admin-ajax.php' ),

	'menu_page'				=> [
		'page_title'		=> \__( 'ItalyStrap Dashboard', 'italystrap' ),
		'menu_title'		=> \__( 'ItalyStrap', 'italystrap' ),
		// 'capability'		=> $this->capability,
		'menu_slug'			=> 'italystrap-dashboard',
		// 'function'		=> array( $this, 'get_admin_view' ),
		'icon_url'			=> 'dashicons-performance',
		'position'			=> null,
	],
//	'submenu_pages'	=> [
//		[
//			'parent_slug'	=> 'italystrap-dashboard',
//			'page_title'	=> \__( 'Settings', 'italystrap' ),
//			'menu_title'	=> \__( 'Settings', 'italystrap' ),
//			// 'capability'	=> $this->capability,
//			'menu_slug'		=> 'italystrap-settings',
//			// 'function_cb'	=> array( $this, 'get_admin_view' ),
//		],
//	],
];
