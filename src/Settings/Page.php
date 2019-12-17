<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigInterface as Config;

class Page {

	use ShowableTrait;

	const DS = DIRECTORY_SEPARATOR;
	const PAGE_TITLE	= 'page_title';
	const MENU_TITLE	= 'menu_title';
	const CAPABILITY	= 'capability';
	const SLUG			= 'menu_slug';
	const VIEW_CALLBACK	= 'callback';
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
	public function load() {

		$this->capability = $this->config->get( 'page.capability', 'manage_options' );

		$callable = $this->config->get( 'page.' . self::VIEW_CALLBACK );
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

			$callable = $submenu[ self::VIEW_CALLBACK ] ?? false;
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
	 * The add_submenu_page callback
	 */
	public function getView() {
		$this->view->render( $this->view_file );
	}
}
