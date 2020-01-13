<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use Auryn\Injector;
use ItalyStrap\Cleaner\Sanitization;
use ItalyStrap\Cleaner\Validation;
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\DataParser\Filters\SanitizeFilter;
use ItalyStrap\DataParser\Filters\TranslateFilter;
use ItalyStrap\DataParser\Filters\ValidateFilter;
use ItalyStrap\DataParser\LazyParser;
use ItalyStrap\DataParser\ParserInterface;
use ItalyStrap\Fields\Fields;
use ItalyStrap\HTML\Attributes;
use ItalyStrap\HTML\Tag;
use ItalyStrap\I18N\Translator;

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
	 * @var array
	 */
	private $pages;

	/**
	 * @var ParserInterface
	 */
	private $parser;

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
	public function addCustomPluginLink( string $key, string $url, string $text, array $attr = [] ) {
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
	 * @return void
	 */
	public function build(): void {

		\array_map( function ( array $to_boot ) {
			foreach ( $to_boot as $bootable ) {
				$bootable->boot();
			}
		}, $this->pages );

		$this->getLinks()->boot( $this->base_name );

		/**
		 * Load script for Tabbed admin page
		 */
		$asset = new AssetLoader();
		\add_action( 'admin_enqueue_scripts', [ $asset, 'load' ] );
	}

	/**
	 * @param array $page
	 * @param array $sections
	 * @return SettingsBuilder
	 */
	public function addPage( array $page, array $sections = [] ): SettingsBuilder {

		$pages_obj = new Page(
			ConfigFactory::make( $page ),
			new ViewPage()
		);

		$this->pages[ $pages_obj->getSlug() ][] = $pages_obj;

		if ( ! empty( $sections ) ) {
			$sections = $this->addSections( $sections );
			$pages_obj->withSections( $sections );
			$this->pages[ $pages_obj->getSlug() ][] = $sections;
		}

		if ( ! empty( $this->base_name ) ) {
			$this->getLinks()->forPages( $pages_obj );
		}

		return $this;
	}

	/**
	 * @param array $item
	 * @return Sections
	 */
	private function addSections( array $item ): Sections {

		$sections_obj = new Sections(
			ConfigFactory::make( $item ),
			new Fields(),
			$this->makeParser(),
			$this->getOptions()
		);

		return $sections_obj;
	}

	protected function makeParser(): ParserInterface {

		$callable = function (): array {

			$filters = [
				new SanitizeFilter( new Sanitization() ),
				new ValidateFilter( new Validation() )
			];

			if ( !empty( $this->domain ) ) {
				$filters[] = new TranslateFilter( new Translator( $this->domain ) );
			}

			return $filters;
		};

		return new LazyParser( $callable );
	}
}
