<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Class ViewPage
 * @package ItalyStrap\Settings
 */
interface ViewPageInterface {

	/**
	 * @param mixed $capability
	 * @return ViewPageInterface
	 */
	public function withCapability( $capability ): ViewPageInterface;

	/**
	 * @param SectionsInterface $sections
	 */
	public function withSections( SectionsInterface $sections ): void;

	/**
	 * @param string $view
	 */
	public function render( $view = '' ): void;

	/**
	 * @param Page $page
	 * @return ViewPage
	 */
	public function forPage( PageInterface $page );
}
