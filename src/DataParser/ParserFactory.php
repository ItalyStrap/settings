<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser;

/**
 * Class DataParserFactory
 * @package ItalyStrap\Settings
 */
class ParserFactory {

	/**
	 * @return Parser
	 */
	public static function make(): Parser {
		return new Parser();
	}
}
