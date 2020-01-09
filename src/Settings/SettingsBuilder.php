<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use Auryn\Injector;
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
	 * @var OptionsInterface
	 */
	private $options;

	/**
	 * @var PluginLinks
	 */
	private $links;

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
	 * @param string $option_name
	 * @param string $domain
	 * @param string $base_name
	 */
	public function __construct( $option_name, $domain = '', $base_name = '' ) {
		$this->option_name = $option_name;
		$this->domain = $domain;
		$this->base_name = $base_name;
	}

	/**
	 * @return PluginLinks
	 */
	public function getLinks(): PluginLinks {

		if ( empty( $this->links ) ) {
			$this->links = new PluginLinks( new Tag( new Attributes() ) );
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

		$this->getLinks()->boot( $this->base_name );

		/**
		 * Load script for Tabbed admin page
		 */
		$asset = new AssetLoader();
		\add_action( 'admin_enqueue_scripts', [$asset, 'load'] );
	}

	/**
	 * @param array $page
	 * @param Sections $sections
	 * @return SettingsBuilder
	 */
	public function addPage( array $page, array $sections = [] ): SettingsBuilder {

		$pages_obj = new Page(
			ConfigFactory::make( $page ),
			new ViewPage()
		);

		if ( ! empty( $sections ) ) {
			$sections = $this->addSections( $sections );
			$pages_obj->withSections( $sections );
			$sections->boot();
		}

		if ( ! empty( $this->base_name ) ) {
			$this->getLinks()->forPages( $pages_obj );
		}

		$pages_obj->boot();
		return $this;
	}

	/**
	 * @param array $item
	 * @return array
	 */
	private function addSections( array $item ): Sections {

		$sections_obj = new Sections(
			ConfigFactory::make( $item ),
			new Fields(),
			ParserFactory::make( $this->domain ),
			$this->getOptions()
		);

		return $sections_obj;
	}
}
