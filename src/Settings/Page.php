<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigInterface as Config;

class Page {

	use ShowableTrait;

	const DS 			= DIRECTORY_SEPARATOR;
	const PAGE_TITLE	= 'page_title';
	const MENU_TITLE	= 'menu_title';
	const CAPABILITY	= 'capability';
	const SLUG			= 'menu_slug';
	const CALLBACK		= 'callback';
	const ICON			= 'icon_url';
	const POSITION		= 'position';
	const VIEW			= 'view';
	const PARENT		= 'parent';

	/**
	 * @var Config
	 */
	private $config;
	private $capability;
	private $pagenow;
	private $sections;

	/**
	 * @var ViewPageInterface
	 */
	private $view;

	/**
	 * @var string
	 */
	private $options_group;

	/**
	 * @var string
	 */
	private $view_file = '';

	/**
	 * Pages constructor.
	 * @param Config $config
	 * @param SectionsInterface $sections
	 * @param ViewPageInterface $view
	 */
	public function __construct( Config $config, SectionsInterface $sections, ViewPageInterface $view ) {

		if ( isset( $_GET['page'] ) ) { // Input var okay.
			$this->pagenow = \stripslashes( $_GET['page'] ); // Input var okay.
		}

		$this->config = $config;
		$this->sections = $sections;
		$this->options_group = $sections->getGroup();
		$this->view = $view;
		$this->view->withSections( $this->sections );
	}

	/**
	 * Add plugin primary page in admin panel
	 */
	public function register() {

		foreach ( $this->config as $config ) {
			$this->assertHasMinimumValueSet( $config );
			$this->parseWithDefault( $config );

			$this->capability = $config[ self::CAPABILITY ];

			$callable = $config[ self::CALLBACK ];

			if ( $config[ self::PARENT ] ) {

				if ( isset( $config['show_on'] ) && ! $this->showOn( $config[ 'show_on' ] ) ) {
					continue;
				}

				\add_submenu_page(
					$config[ self::PARENT ],
					$config[ self::PAGE_TITLE ],
					$config[ self::MENU_TITLE ],
					$this->capability,
					$config[ self::SLUG ],
					\is_callable( $callable ) ? $callable : function () use ( $config ) {
						$this->getView( $config );
					}
				);
				continue;
			}

			\add_menu_page(
				$config[ self::PAGE_TITLE ],
				$config[ self::MENU_TITLE ],
				$this->capability,
				$config[ self::SLUG ],
				\is_callable( $callable ) ? $callable : function () use ( $config ) {
					$this->getView( $config );
				},
				$config[ self::ICON ],
				$config[ self::POSITION ]
			);
		}
	}

	/**
	 * The add_submenu_page callback
	 */
	public function getView( array $config = [] ) {
		$this->view->render( $config[ self::VIEW ] );
	}

	private function parseWithDefault( array &$config ) {

		$default = [
			self::PAGE_TITLE	=> '',
			self::MENU_TITLE	=> '',
			self::CAPABILITY	=> 'manage_options',
			self::SLUG			=> '',
			self::CALLBACK		=> null,
			self::ICON			=> '',
			self::POSITION		=> null,
			self::VIEW			=> '',

			// For child pages
			self::PARENT		=> false,
		];

		$config = \array_replace_recursive( $default, $config );
	}

	private function parentPage() {

		$this->capability = $this->config->get( 'page.capability', 'manage_options' );

		$callable = $this->config->get( 'page.' . self::CALLBACK );
		$this->view_file =  $this->config->get( 'page.' . self::VIEW );

		\add_menu_page(
			$this->config['page']['page_title'],
			$this->config['page']['menu_title'],
			$this->capability,
			$this->config['page']['menu_slug'],
			\is_callable( $callable ) ? $callable : [ $this, 'getView' ],
			$this->config['page']['icon_url'],
			$this->config['page']['position']
		);

		$this->addSubMenuPage( $this->config->get( 'page.pages', [] ), $this->config['page']['menu_slug'] );
	}

	/**
	 * Add sub menù pages for plugin's admin page
	 * @param array $submenu_pages
	 * @param string $parent_slug
	 */
	private function addSubMenuPage( array $submenu_pages, string $parent_slug ) {

		foreach ( $submenu_pages as $submenu ) {
			if ( isset( $submenu['show_on'] ) && ! $this->showOn( $submenu[ 'show_on' ] ) ) {
				continue;
			}

			$callable = $submenu[ self::CALLBACK ] ?? false;
			$this->view_file =  $submenu[ self::VIEW ];

			\add_submenu_page(
				$parent_slug,
				$submenu['page_title'],
				$submenu['menu_title'],
				$this->capability,
				$submenu['menu_slug'],
				\is_callable( $callable ) ? $callable : [ $this, 'getView']
			);
		}
	}

	/**
	 * @param $config
	 */
	private function assertHasMinimumValueSet( $config ) {
		if ( !isset( $config[ self::MENU_TITLE ] ) ) {
			throw new \RuntimeException( \sprintf( '%s must be not empty', self::MENU_TITLE ) );
		}

		if ( !isset( $config[ self::SLUG ] ) ) {
			throw new \RuntimeException( \sprintf( '%s must be not empty', self::SLUG ) );
		}
	}
}
