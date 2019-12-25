<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigInterface as Config;

class Page implements PageInterface {

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
	const EVENT			= 'admin_menu';

	/**
	 * @var Config
	 */
	private $config;

	/**
	 * @var SectionsInterface
	 */
	private $sections;

	/**
	 * @var ViewPageInterface
	 */
	private $view;

	/**
	 * Pages constructor.
	 * @param Config $config
	 * @param SectionsInterface $sections
	 * @param ViewPageInterface $view
	 */
	public function __construct( Config $config, ViewPageInterface $view, SectionsInterface $sections = null ) {
		$this->config = $config;
		$this->view = $view;
		if ( $sections ) {
			$this->sections = $sections;
			$this->sections->forPage( $this );
			$this->view->forPage( $this );
			$this->view->withSections( $sections );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getPageName() {
		return \sanitize_key( $this->config->{self::SLUG} );
	}

	/**
	 * @inheritDoc
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
				\sanitize_key( $this->config->{self::SLUG} ),
				$this->getCallable( $callable, $this->config )
			);
		}

		return \add_menu_page(
			$this->config->get( self::PAGE_TITLE ),
			$this->config->get( self::MENU_TITLE ),
			$this->config->get( self::CAPABILITY, 'manage_options' ),
			\sanitize_key( $this->config->{self::SLUG} ),
			$this->getCallable( $callable, $this->config ),
			$this->config->get( self::ICON ),
			$this->config->get( self::POSITION )
		);
	}

	/**
	 * @param Config $config
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

	/**
	 * @return true|void
	 */
	public function boot() {
		return \add_action( self::EVENT, [ $this, 'register'] );
	}

	/**
	 * @return true|void
	 */
	public function unBoot() {
		return \remove_action( self::EVENT, [ $this, 'register'] );
	}
}
