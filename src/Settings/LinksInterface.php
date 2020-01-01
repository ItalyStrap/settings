<?php

namespace ItalyStrap\Settings;

/**
 * Class for Plugin_Links
 */
interface LinksInterface {

	/**
	 * @return array
	 */
	public function getLinks(): array;

	public function forPages( Page ...$pages );
}
