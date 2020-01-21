<?php
declare(strict_types=1);

namespace ItalyStrap\FileHeader;

use SplFileObject;

/**
 * Class PluginData
 * @package ItalyStrap\FileHeader
 */
class HeaderFields implements HeaderFieldsInterface {

	/**
	 * @var SplFileObject
	 */
	private $file;

	/**
	 * @var array
	 */
	private $headers;

	/**
	 * PluginData constructor.
	 * @param SplFileObject $file
	 * @param array $headers
	 */
	public function __construct( SplFileObject $file, array $headers ) {
		$this->file = $file;
		$this->headers = $headers;
	}

	/**
	 * @inheritDoc
	 */
	public function fields(): array {
		$content = $this->fileContent();

		$headers = [];
		foreach ( $this->headers as $field => $regex ) {
			$headers[ $field ] = '';
			if (
				\preg_match(
					'/^[ \t\/*#@]*' . \preg_quote( $regex, '/' ) . ':(.*)$/mi',
					$content,
					$match
				) && $match[ 1 ]
			) {
				$headers[ $field ] = \trim(
					\preg_replace( '/\s*(?:\*\/|\?>).*/', '', $match[ 1 ] )
				);
			}
		}

		return $headers;
	}

	/**
	 * @return false|string|string[]
	 */
	private function fileContent(): string {
		return \str_replace( "\r", "\n", $this->file->fread( 8 * 1024 ) );
	}
}
