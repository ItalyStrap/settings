<?php
/**
 * Class for Plugin_Links
 *
 * This class add some functions for use in admin panel
 *
 * @since 4.0.0
 *
 * @package ItalyStrap\Settings
 */
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\HTML\Tag;

//'plugin_action_links'	=> array(
//	'<a href="admin.php?page=italystrap-settings">' . __( 'Settings','italystrap' ) . '</a>',
//	'<a href="http://docs.italystrap.com/" target="_blank">' . __( 'Docs','italystrap' ) . '</a>',
//	'<a href="https://italystrap.com/" target="_blank">ItalyStrap</a>',
//),
//	'plugin_row_meta'		=> array(
//	'<a href="admin.php?page=italystrap-settings">' . __( 'Settings','italystrap' ) . '</a>',
//	'<a href="http://docs.italystrap.com/" target="_blank">' . __( 'Doc','italystrap' ) . '</a>',
//	'<a href="https://italystrap.com/" target="_blank">ItalyStrap</a>',
//),

/**
 * Add link in plugin activation panel
 */
//add_filter( 'plugin_action_links_' . ITALYSTRAP_BASENAME, array( $this, 'plugin_action_links' ) );

//add_filter( 'plugin_row_meta' , array( $this, 'plugin_row_meta' ), 10, 4 );
//add_filter( 'plugin_row_meta_' . ITALYSTRAP_BASENAME , array( $this, 'plugin_row_meta' ), 10, 4 );

/**
 * Class for Plugin_Links
 */
class PluginLinks implements PluginLinksInterface {

	/**
	 * @var string
	 */
	private $default_page = 'admin.php';

	/**
	 * @var array<string>
	 */
	private $default_pages = [
		'options-general.php',
		'edit-comments.php',
		'plugins.php',
		'edit.php',
		'upload.php',
		'themes.php',
		'users.php',
		'tools.php'
	];
	/**
	 * @var string
	 */
	private $base_name;

	/**
	 * @return array
	 */
	public function getDefaultPages(): array {
		return $this->default_pages;
	}

	/**
	 * @var array<string>
	 */
	private $links = [];

	/**
	 * @return array<string>
	 */
	public function getLinks(): array {
		return $this->links;
	}

	/**
	 * @var Tag
	 */
	private $tag;

	/**
	 * Links constructor.
	 * @param Tag $tag
	 * @param string $base_name
	 */
	public function __construct( Tag $tag, string $base_name ) {
		$this->tag = $tag;
		$this->base_name = $base_name;
	}

	/**
	 * @inheritDoc
	 */
	public function forPages( PageInterface ...$pages ): PluginLinks {
		foreach ( $pages as $page ) {
			$url = $this->generateAdminUrl( $page );
			$this->addLink( $page->getSlug(), $url, $page->getPageTitle() );
		}
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function addLink( string $key, string $url, string $text, array $attr = [] ): PluginLinks {
		$this->links[ $key ] = $this->createLink( $url, $text, $attr );
		return $this;
	}

	/**
	 * Add link in plugin activation panel
	 *
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
	 * @param array $links Array of link in wordpress dashboard.
	 * @param string $plugin_file
	 * @param string $plugin_data
	 * @param string $context
	 * @return array        Array with my links
	 */
	public function update( array $links, $plugin_file, $plugin_data, $context ): array {
		return \array_merge( $this->links, $links );
	}

	/**
	 * @return void
	 */
	public function boot(): void {
		$prefix = is_network_admin() ? 'network_admin_' : '';
		\add_filter( $prefix . 'plugin_action_links_' . $this->base_name, [ $this, 'update' ], 10, 4 );
	}

	/**
	 * @param PageInterface $page
	 * @return string
	 */
	private function generateAdminUrl( PageInterface $page ): string {
		$prefix = $this->default_page;

		if ( $page->isSubmenu() && \in_array( $page->getParentPageSlug(), $this->default_pages ) ) {
			$prefix = $page->getParentPageSlug();
		}

		return \admin_url( $prefix . '?page=' . $page->getSlug() );
	}

	/**
	 * @param string $url
	 * @param string $content
	 * @param array $attr
	 * @return string
	 */
	private function createLink( string $url, string $content, array $attr ): string {

		$attr = \array_merge( [ 'href' => $url, 'aria-label' => $content ], $attr );

		return $this->tag->open( $url, 'a', $attr )
			. $content
			. $this->tag->close( $url );
	}
}
