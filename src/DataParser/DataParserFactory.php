<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser;

use ItalyStrap\Cleaner\Sanitization;
use ItalyStrap\Cleaner\Validation;
use ItalyStrap\I18N\Translator;
use ItalyStrap\DataParser\{Filters\SanitizeFilter, Filters\TranslateFilter, Filters\ValidateFilter};

/**
 * Class DataParserFactory
 * @package ItalyStrap\Settings
 */
class DataParserFactory
{
	/**
	 * @param string $plugin_name
	 * @return DataParser
	 */
	public static function make( string $plugin_name = '' ): DataParser {

		$filters = [
			new SanitizeFilter( new Sanitization() ),
			new ValidateFilter( new Validation() )
		];

		if ( ! empty( $plugin_name ) ) {
			$filters[] = new TranslateFilter( new Translator( $plugin_name ) );
		}

		return ( new \ItalyStrap\DataParser\DataParser() )->withFilters( ...$filters	);
	}
}
