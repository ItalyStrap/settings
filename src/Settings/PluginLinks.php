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

/**
 * Class for Plugin_Links
 */
class PluginLinks {
	private $plugin;

	/**
	 * Add link in plugin activation panel
	 *
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
	 * @param  array $links Array of link in wordpress dashboard.
	 * @return array        Array with my links
	 */
	public function pluginActionLinks( array $links ) {

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
	public function pluginRowMeta( array $plugin_meta, $plugin_file, array $plugin_data, $status ) {

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
