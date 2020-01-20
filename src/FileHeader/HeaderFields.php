<?php
declare(strict_types=1);

namespace ItalyStrap\FileHeader;

use ItalyStrap\Config\Config;
use SplFileObject;

/**
 * Class PluginData
 * @package ItalyStrap\FileHeader
 */
class HeaderFields extends Config implements Plugin {

	/**
	 * @var SplFileObject
	 */
	private $file;

	/**
	 * PluginData constructor.
	 * @param SplFileObject $file
	 */
	public function __construct( SplFileObject $file ) {
		$this->file = $file;
		parent::__construct( $this->fields() );
	}

	public function textDomain(): string {
		return (string) $this->get( self::TEXT_DOMAIN );
	}

	/**
	 * @return array
	 */
	public function fields(): array {
		$content = $this->file->fread( 8 * 1024 );
		$content = \str_replace( "\r", "\n", $content );

		$all_headers = [];
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
