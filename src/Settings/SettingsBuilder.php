<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\DataParser\ParserFactory;
use ItalyStrap\Fields\Fields;
use ItalyStrap\HTML\Attributes;
use ItalyStrap\HTML\Tag;

/**
 * Class SettingsFactory
 * @package ItalyStrap\Settings
 */
class SettingsBuilder {

	/**
	 * @var Options
	 */
	private $options;

	/**
	 * @var Links
	 */
	private $links;

	/**
	 * @return Options
	 */
	public function getOptions(): Options {
		return $this->options;
	}

	/**
	 * @param ConfigInterface $config
	 * @param string $option_name
	 * @param string $plugin_name
	 * @param string $base_name
	 */
	public function build( ConfigInterface $config, $option_name, $plugin_name = '', $base_name = '' ) {

		$this->options = new Options( $option_name );
		$this->links = new Links( new Tag( new Attributes() ) );

		foreach ( $config as $item ) {
			$sections_obj = new Sections(
				ConfigFactory::make( $item['sections'] ),
				new Fields(),
				ParserFactory::make( $plugin_name ),
				$this->options
			);
			$sections_obj->boot();

			$pages_obj = new Page(
				ConfigFactory::make( $item['page'] ),
				new ViewPage(),
				$sections_obj
			);
			$pages_obj->boot();

			if ( ! empty( $base_name ) ) {
				$this->links->forPages( $pages_obj );
				$this->links->boot( $base_name );
			}
		}
	}
}
