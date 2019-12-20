<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Class DataParserFactory
 * @package ItalyStrap\Settings
 */
class DataParserFactory
{
	public static function make( string $plugin_name = '' ) {

		$filters = [
			new \ItalyStrap\Settings\Filters\SanitizeFilter( new \ItalyStrap\Cleaner\Sanitization() ),
			new \ItalyStrap\Settings\Filters\ValidateFilter( new \ItalyStrap\Cleaner\Validation() ),
			new \ItalyStrap\Settings\Filters\TranslateFilter( new \ItalyStrap\I18N\Translator( $plugin_name ) )
		];

		return ( new \ItalyStrap\Settings\DataParser() )->withFilters( ...$filters	);
	}
}
