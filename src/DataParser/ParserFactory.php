<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser;

use ItalyStrap\Cleaner\Sanitization;
use ItalyStrap\Cleaner\Validation;
use ItalyStrap\I18N\Translator;
use ItalyStrap\DataParser\Filters\SanitizeFilter;
use ItalyStrap\DataParser\Filters\TranslateFilter;
use ItalyStrap\DataParser\Filters\ValidateFilter;

/**
 * Class DataParserFactory
 * @package ItalyStrap\Settings
 */
class ParserFactory {

	/**
	 * @param string $domain
	 * @return Parser
	 */
	public static function make( string $domain = '' ): Parser {

		$filters = [
			new SanitizeFilter( new Sanitization() ),
			new ValidateFilter( new Validation() )
		];

		if ( ! empty( $domain ) ) {
			$filters[] = new TranslateFilter( new Translator( $domain ) );
		}

		return ( new Parser() )->withFilters( ...$filters	);
	}
}
