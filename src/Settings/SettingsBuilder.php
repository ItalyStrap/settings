<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use Auryn\Injector;
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\DataParser\Parser;
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
	 * @var OptionsInterface
	 */
	private $options;

	/**
	 * @var Links
	 */
	private $links;
	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @return Links
	 */
	public function getLinks(): Links {
		return $this->links;
	}

	/**
	 * @return OptionsInterface
	 */
	public function getOptions(): OptionsInterface {
		return $this->options;
	}

	/**
	 * @param ConfigInterface<array> $config
	 * @param string $option_name
	 * @param string $domain
	 * @param string $base_name
	 *
	 * @return void
	 */
	public function build( ConfigInterface $config, $option_name, $domain = '', $base_name = '' ): void {

		$injector = new Injector();

		$this->options = new Options( $option_name );
		$this->links = new Links( new Tag( new Attributes() ) );
		$this->parser = ParserFactory::make( $domain );

		foreach ( $config as $item ) {
			$sections_obj = new Sections(
				ConfigFactory::make( $item['sections'] ),
				new Fields(),
				$this->parser,
				$this->options
			);
			$sections_obj->boot();

			$pages_obj = new Page(
				ConfigFactory::make( $item['page'] ),
				new ViewPage()
			);
			$pages_obj->withSections( $sections_obj );
			$pages_obj->boot();

			if ( ! empty( $base_name ) ) {
				$this->links->forPages( $pages_obj );
				$this->links->boot( $base_name );
			}
		}

		/**
		 * Load script for Tabbed admin page
		 */
		$asset = new AssetLoader();
		\add_action( 'admin_enqueue_scripts', [ $asset, 'load'] );
	}
}
