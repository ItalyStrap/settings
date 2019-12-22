<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigInterface as Config;

class Pages {

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
		$this->options_group = $sections->getPageName();
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

			$callable = $config[ self::CALLBACK ];

			if ( $config[ self::PARENT ] ) {

				if ( ! $this->showOn( $config[ 'show_on' ] ) ) {
					continue;
				}

				\add_submenu_page(
					$config[ self::PARENT ],
					$config[ self::PAGE_TITLE ],
					$config[ self::MENU_TITLE ],
					$config[ self::CAPABILITY ],
					$config[ self::SLUG ],
					$this->getCallable( $callable, $config )
				);

				continue;
			}

			\add_menu_page(
				$config[ self::PAGE_TITLE ],
				$config[ self::MENU_TITLE ],
				$config[ self::CAPABILITY ],
				$config[ self::SLUG ],
				$this->getCallable( $callable, $config ),
				$config[ self::ICON ],
				$config[ self::POSITION ]
			);
		}
	}

	/**
	 * @param array $config
	 */
	private function parseWithDefault( array &$config ) {

		$default = [
			'show_on'			=> true,
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

	/**
	 * The add_submenu_page callback
	 * @param array $config
	 */
	private function getView( array $config = [] ) {
		$this->view->render( $config[ self::VIEW ] );
	}

	/**
	 * @param mixed $callable
	 * @param array $config
	 * @return callable|\Closure
	 */
	private function getCallable( $callable, array $config ) {
		return \is_callable( $callable ) ? $callable : function () use ( $config ) {
			$this->getView( $config );
		};
	}
}
