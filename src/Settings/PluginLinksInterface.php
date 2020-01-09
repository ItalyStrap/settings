<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Class for Plugin_Links
 */
interface PluginLinksInterface {

	/**
	 * @param string $key
	 * @param string $url
	 * @param string $text
	 * @param array<string> $attr
	 * @return $this
	 */
	public function addLink( string $key, string $url, string $text, array $attr = [] );

	/**
	 * @return array
	 */
	public function getLinks(): array;

	/**
	 * @param PageInterface ...$pages
	 * @return $this
	 */
	public function forPages( PageInterface ...$pages );
}
