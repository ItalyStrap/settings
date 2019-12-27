<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\DataParser\ParserFactory;
use ItalyStrap\Fields\Fields;

/**
 * Class SettingsFactory
 * @package ItalyStrap\Settings
 */
class SettingsBuilder
{
	public function build( ConfigInterface $config, $option_name, $plugin_name = '' ) {

		$options_obj = new Options( $option_name );

		foreach ( $config as $item ) {
			$sections_obj = new Sections(
				ConfigFactory::make( $item['sections'] ),
				new Fields(),
				ParserFactory::make( $plugin_name ),
				$options_obj
			);
			$sections_obj->boot();

			$pages_obj = new Page(
				ConfigFactory::make( $item['page'] ),
				new ViewPage(),
				$sections_obj
			);
			$pages_obj->boot();
		}
	}
}
