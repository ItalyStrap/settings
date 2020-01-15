<?php
declare(strict_types=1);

namespace ItalyStrap\FileHeader;

use SplFileInfo;

class PluginData {

	/**
	 * @var SplFileInfo
	 */
	private $file;

	const NAME = 'Name';
	const TEXT_DOMAIN = 'TextDomain';

	const HEADERS = [
		self::NAME        => 'Plugin Name',
		'PluginURI'   => 'Plugin URI',
		'Version'     => 'Version',
		'Description' => 'Description',
		'Author'      => 'Author',
		'AuthorURI'   => 'Author URI',
		self::TEXT_DOMAIN  => 'Text Domain',
		'DomainPath'  => 'Domain Path',
		'Network'     => 'Network',
		'RequiresWP'  => 'Requires at least',
		'RequiresPHP' => 'Requires PHP',
	];

	/**
	 * @var array
	 */
	private $headers;

	/**
	 * PluginData constructor.
	 * @param SplFileInfo $file
	 */
	public function __construct( SplFileInfo $file ) {
		$this->file = $file;
		$this->headers = $this->headers();
	}

	public function textDomain(): string {
		return $this->headers[ self::TEXT_DOMAIN ];
	}

	/**
	 * @param $all_headers
	 * @param $match
	 * @return array
	 */
	private function headers(): array {
		$content = $this->file->fread( 8 * 1024 );
		$content = \str_replace( "\r", "\n", $content );

		foreach (self::HEADERS as $field => $regex) {
			$all_headers[ $field ] = '';
			if (
				\preg_match(
				'/^[ \t\/*#@]*' . \preg_quote( $regex, '/' ) . ':(.*)$/mi',
				$content,
				$match
				) && $match[ 1 ]
			) {
				$all_headers[ $field ] = \trim(
					\preg_replace( '/\s*(?:\*\/|\?>).*/', '', $match[ 1 ] )
				);
			}
		}

		return $all_headers;
	}
}
