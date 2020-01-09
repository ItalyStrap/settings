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
class SettingsBuilder
{
	/**
	 * @var OptionsInterface
	 */
	private $options;

	/**
	 * @var Links
	 */
	private $links;

	/**
	 * @var ConfigInterface
	 */
	private $config;

	/**
	 * @var string
	 */
	private $option_name;

	/**
	 * @var string
	 */

	private $domain;

	/**
	 * @var string
	 */
	private $base_name;

	/**
	 * @param ConfigInterface<array> $config
	 * @param string $option_name
	 * @param string $domain
	 * @param string $base_name
	 */
	public function __construct( ConfigInterface $config, $option_name, $domain = '', $base_name = '' ) {
		$this->config = $config;
		$this->option_name = $option_name;
		$this->domain = $domain;
		$this->base_name = $base_name;
	}

	/**
	 * @return Links
	 */
	public function getLinks(): Links {

		if ( empty( $this->links ) ) {
			$this->links = new Links( new Tag( new Attributes() ) );
		}

		return $this->links;
	}

	/**
	 * @param string $key
	 * @param string $url
	 * @param string $text
	 * @param array $attr
	 * @return $this
	 */
	public function addCustomLink( string $key, string $url, string $text, array $attr = [] ) {
		$this->getLinks()->addLink( ...\func_get_args() );
		return $this;
	}

	/**
	 * @return OptionsInterface
	 */
	public function getOptions(): OptionsInterface {

		if ( empty( $this->options ) ) {
			$this->options = new Options( $this->option_name );
		}

		return $this->options;
	}

	/**
	 * @param iterable $item
	 */
	public function withPage( iterable $item ) {

	}

	/**
	 * @return void
	 */
	public function build(): void {

		$injector = new Injector();

		foreach ($this->config as $item) {

			$sections_obj = new Sections(
				ConfigFactory::make( $item[ 'sections' ] ),
				new Fields(),
				ParserFactory::make( $this->domain ),
				$this->getOptions()
			);

			$this->page( $item[ 'page' ], $sections_obj );
		}

		/**
		 * Load script for Tabbed admin page
		 */
		$asset = new AssetLoader();
		\add_action( 'admin_enqueue_scripts', [$asset, 'load'] );
	}

	/**
	 * @param array $item
	 * @param Sections $sections_obj
	 * @return SettingsBuilder
	 */
	public function page( $item, Sections $sections_obj = null ): SettingsBuilder {

		$pages_obj = new Page(
			ConfigFactory::make( $item ),
			new ViewPage()
		);

		if ( $sections_obj ) {
			$pages_obj->withSections( $sections_obj );
			$sections_obj->boot();
		}

		$pages_obj->boot();

		if ( !empty( $this->base_name ) ) {
			$this->getLinks()->forPages( $pages_obj );
			$this->getLinks()->boot( $this->base_name );
		}

		return $this;
	}
}
