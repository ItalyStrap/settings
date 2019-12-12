<?php
/**
 * Settings file
 *
 * @package Perf_Metrics
 */
declare(strict_types=1);
/**
 * Settings for the content area
 */
return [
	'general'	=> [
		'tab_title'			=> __( 'General', 'perf-metrics' ),
		'id'				=> 'general',
		'title'				=> __( 'General options page', 'perf-metrics' ),
		'desc'				=> __( 'General setting for Perf_Metrics', 'perf-metrics' ),
		'settings_fields'	=> require 'fields.php',
	],
];
