<?php
declare(strict_types=1);

namespace ItalyStrap\FileHeader;

/**
 * Interface Theme
 * @link https://developer.wordpress.org/themes/basics/main-stylesheet-style-css/
 * @package ItalyStrap\FileHeader
 */
interface Theme extends SharedKeys {

	const THEME_URI = 'ThemeURI';
	const TAGS = 'Tags';
	const TEMPLATE = 'Template';

	const HEADERS = [
		self::NAME			=> 'Theme Name',
		self::THEME_URI		=> 'Theme URI',
		self::VERSION		=> 'Version',
		self::DESCRIPTION	=> 'Description',
		self::AUTHOR		=> 'Author',
		self::AUTHOR_URI	=> 'Author URI',
		self::TEXT_DOMAIN	=> 'Text Domain',
		self::DOMAIN_PATH	=> 'Domain Path',
		self::LICENSE		=> 'License',
		self::LICENSE_URI	=> 'License URI',
		self::TAGS			=> 'Tags',

		/**
		 * Used by child theme
		 */
		self::TEMPLATE		=> 'Template',
	];
}
