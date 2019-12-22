<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigInterface as Config;

/**
 * Class Page
 * @package ItalyStrap\Settings
 */
class Page
{
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
	const PARENT_SLUGS	= [

	];

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
		$this->options_group = $sections->getGroup();
		$this->view = $view;
		$this->view->withSections( $this->sections );
	}

	/**
	 * Add plugin primary page in admin panel
	 * @return bool|false|string
	 */
	public function register() {

		if ( ! $this->showOn( $this->config->get( 'show_on', true ) ) ) {
			return false;
		}

		$this->assertHasMinimumValueSet( $this->config );

		$callable = $this->config->get( self::CALLBACK );

		if ( $this->config->get( self::PARENT ) ) {
			return \add_submenu_page(
				$this->config->{self::PARENT},
				$this->config->get( self::PAGE_TITLE ),
				$this->config->get( self::MENU_TITLE ),
				$this->config->get( self::CAPABILITY, 'manage_options' ),
				$this->config->{self::SLUG},
				$this->getCallable( $callable, $this->config )
			);
		}

		return \add_menu_page(
			$this->config->get( self::PAGE_TITLE ),
			$this->config->get( self::MENU_TITLE ),
			$this->config->get( self::CAPABILITY, 'manage_options' ),
			$this->config->get( self::SLUG ),
			$this->getCallable( $callable, $this->config ),
			$this->config->get( self::ICON ),
			$this->config->get( self::POSITION )
		);
	}

	/**
	 * @param $config
	 */
	private function assertHasMinimumValueSet( Config $config ) {
		if ( ! $config->{self::MENU_TITLE} ) {
			throw new \RuntimeException( \sprintf( '%s must be not empty', self::MENU_TITLE ) );
		}

		if ( ! $config->{self::SLUG} ) {
			throw new \RuntimeException( \sprintf( '%s must be not empty', self::SLUG ) );
		}
	}

	/**
	 * @param mixed $callable
	 * @param Config $config
	 * @return callable|\Closure
	 */
	private function getCallable( $callable, Config $config ) {
		return \is_callable( $callable ) ? $callable : function () use ( $config ) {
			$this->view->render( $config->get( self::VIEW, '' ) );
		};
	}
}
