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
use ItalyStrap\FileHeader\HeaderFields;
use ItalyStrap\FileHeader\Plugin;
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
	private $pages_obj;

	/**
	 * @var ParserInterface
	 */
	private $parser;
	/**
	 * @var string
	 */
	private $plugin_file;

	/**
	 * @var array
	 */
	private $pages;

	/**
	 * @param string $option_name
	 * @param string $base_name
	 * @param string $plugin_file
	 */
	public function __construct(
		string $option_name,
		string $base_name = '',
		string $plugin_file = ''
	) {
		$this->option_name = $option_name;
		$this->base_name = $base_name;
		$this->plugin_file = $plugin_file;
	}

	/**
	 * @return PluginLinks
	 */
	public function links(): PluginLinks {

		if ( empty( $this->links ) ) {
			$this->links = new PluginLinks( new Tag( new Attributes() ), $this->base_name );
		}

		return $this->links;
	}

	/**
	 * @return OptionsInterface
	 */
	public function options(): OptionsInterface {

		if ( empty( $this->options ) ) {
			$this->options = new Options( $this->option_name );
		}

		return $this->options;
	}

	protected function parser(): ParserInterface {

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

	/**
	 * @param string $key
	 * @param string $url
	 * @param string $text
	 * @param array $attr
	 * @return $this
	 */
	public function addCustomPluginLink( string $key, string $url, string $text, array $attr = [] ) {
		$this->links()->addLink( ...\func_get_args() );
		return $this;
	}

	/**
	 * @param array $page
	 * @param array $sections
	 * @return SettingsBuilder
	 */
	public function addPage( array $page, array $sections = [] ): SettingsBuilder {

		$this->pages[ $page[ Page::SLUG ] ][] = $page;
		$this->pages[ $page[ Page::SLUG ] ][] = $sections;

		$pages_obj = new Page(
			ConfigFactory::make( $page ),
			new ViewPage()
		);

		$this->pages_obj[ $pages_obj->getSlug() ][] = $pages_obj;

		if ( ! empty( $sections ) ) {
			$sections_obj = new Sections(
				ConfigFactory::make( $sections ),
				new Fields(),
				$this->parser(),
				$this->options()
			);

			$pages_obj->withSections( $sections_obj );
			$this->pages_obj[ $pages_obj->getSlug() ][] = $sections_obj;
		}

		if ( ! empty( $this->base_name ) ) {
			$this->links()->forPages( $pages_obj );
		}

		return $this;
	}

	/**
	 * @return void
	 */
	public function build(): void {

		$file = new \SplFileObject( $this->plugin_file );
		$headers_info = new HeaderFields( $file, Plugin::HEADERS );

		$this->domain = $headers_info->fields()[ Plugin::TEXT_DOMAIN ];

		\array_map( function ( array $to_boot ) {
			foreach ( $to_boot as $bootable ) {
				$bootable->boot();
			}
		}, $this->pages_obj );

		$this->links()->boot();

		/**
		 * Load script for Tabbed admin page
		 */
		$asset = new AssetLoader();
		\add_action( 'admin_enqueue_scripts', [ $asset, 'load' ] );
	}
}
