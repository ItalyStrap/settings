<?php

namespace ItalyStrap\Settings;

/**
 * Class for Plugin_Links
 */
interface LinksInterface {

	/**
	 * @param string $key
	 * @param string $url
	 * @param string $content
	 * @return $this
	 */
	public function addLink( string $key, string $url, string $content );

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
