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
class Links implements LinksInterface
{
	/**
	 * @var array<string>
	 */
	private $base_parents = [
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
	 */
	public function __construct( Tag $tag ) {
		$this->tag = $tag;
	}

	private function createLink( string $slug, string $content ) {
		return $this->tag->open( $slug, 'a', [ 'href' => $slug, 'aria-label' => $content ] )
			. $content
			. $this->tag->close( $slug );
	}

	public function forPages( Page ...$pages ) {
		foreach ( $pages as $page ) {

			if ( ! $page->isSubmenu() ) {
				$prefix = 'admin.php?page=';
			} elseif ( \in_array( $page->getParentPageSlug(), $this->base_parents ) ) {
				$prefix = $page->getParentPageSlug() . '?page=';
			} else {
				$prefix = $page->getParentPageSlug() . '&page=';
			}

			$slug = \admin_url( $prefix . $page->getPageName() );
			$this->links[ $page->getPageName() ] = $this->createLink( $slug, $page->getMenuTitle() );
		}
	}

	/**
	 * Add link in plugin activation panel
	 *
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
	 * @param array $links Array of link in wordpress dashboard.
	 * @param $plugin_file
	 * @param $plugin_data
	 * @param $context
	 * @return array        Array with my links
	 */
	public function update( array $links, $plugin_file, $plugin_data, $context ) {
		return \array_merge( $this->links, $links );
	}

	public function boot( $base_name = '' ) {
		$prefix = is_network_admin() ? 'network_admin_' : '';
		\add_filter( $prefix . 'plugin_action_links_' . $base_name, [ $this, 'update' ], 10, 4 );
	}

	/**
	 * Add link in plugin activation panel
	 *
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
	 * @param array $links Array of link in wordpress dashboard.
	 * @param $plugin_file
	 * @param $plugin_data
	 * @param $context
	 * @return array        Array with my links
	 */
	private function pluginActionLinks( array $links, $plugin_file, $plugin_data, $context ) {

		if ( ! isset( $this->plugin['plugin_action_links'] ) ) {
			return $links;
		}

		if ( ! is_array( $this->plugin['plugin_action_links'] ) ) {
			return $links;
		}

		foreach ( $this->plugin['plugin_action_links'] as $link ) {
			array_unshift( $links, $link );
		}

		return $links;
	}

	/**
	 * Add information to the plugin description in plugin.php page
	 *
	 * @param array  $plugin_meta An array of the plugin's metadata,
	 *                            including the version, author,
	 *                            author URI, and plugin URI.
	 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
	 * @param array  $plugin_data An array of plugin data.
	 * @param string $status      Status of the plugin. Defaults are 'All', 'Active',
	 *                            'Inactive', 'Recently Activated', 'Upgrade',
	 *                            'Must-Use', 'Drop-ins', 'Search'.
	 * @return array              Return the new array
	 */
	private function pluginRowMeta( array $plugin_meta, $plugin_file, array $plugin_data, $status ) {

		if ( ! isset( $this->plugin['basename'] ) ) {
			return $plugin_meta;
		}

		if ( $this->plugin['basename'] !== $plugin_file ) {
			return $plugin_meta;
		}

		if ( ! isset( $this->plugin['plugin_row_meta'] ) ) {
			return $plugin_meta;
		}

		if ( ! is_array( $this->plugin['plugin_row_meta'] ) ) {
			return $plugin_meta;
		}

		$plugin_meta = array_merge( (array) $plugin_meta, (array) $this->plugin['plugin_row_meta'] );

		return $plugin_meta;
	}
}
