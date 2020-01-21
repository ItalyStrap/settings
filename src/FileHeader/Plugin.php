<?php
declare(strict_types=1);

namespace ItalyStrap\FileHeader;

/**
 * Interface Plugin
 * @link https://developer.wordpress.org/plugins/plugin-basics/header-requirements/
 * @package ItalyStrap\FileHeader
 */
interface Plugin extends SharedKeys {

	const PLUGIN_URI = 'PluginURI';
	const NETWORK = 'Network';
	const REQUIRES_WP = 'RequiresWP';
	const REQUIRES_PHP = 'RequiresPHP';

	const HEADERS = [
		self::NAME			=> 'Plugin Name',
		self::PLUGIN_URI	=> 'Plugin URI',
		self::VERSION		=> 'Version',
		self::DESCRIPTION	=> 'Description',
		self::AUTHOR		=> 'Author',
		self::AUTHOR_URI	=> 'Author URI',
		self::TEXT_DOMAIN	=> 'Text Domain',
		self::DOMAIN_PATH	=> 'Domain Path',
		self::NETWORK		=> 'Network',
		self::REQUIRES_WP	=> 'Requires at least',
		self::REQUIRES_PHP	=> 'Requires PHP',
		self::LICENSE		=> 'License',
		self::LICENSE_URI	=> 'License URI',
	];
}
