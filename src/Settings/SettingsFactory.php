<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigInterface;

/**
 * Class SettingsFactory
 * @package ItalyStrap\Settings
 */
class SettingsFactory
{
	public static function make( ConfigInterface $config, $option_name, $plugin_name = '' ) {

		$options_obj = new \ItalyStrap\Settings\Options( $option_name );

		foreach ( $config as $item ) {
			$sections_obj = new \ItalyStrap\Settings\Sections(
				\ItalyStrap\Config\ConfigFactory::make( $item['sections'] ),
				new \ItalyStrap\Fields\Fields(),
				\ItalyStrap\DataParser\ParserFactory::make( $plugin_name ),
				$options_obj
			);
			$sections_obj->boot();

			$pages_obj = new \ItalyStrap\Settings\Page(
				\ItalyStrap\Config\ConfigFactory::make( $item['page'] ),
				new \ItalyStrap\Settings\ViewPage(),
				$sections_obj
			);
			$pages_obj->boot();
		}
	}
}
