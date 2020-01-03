<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Class Sectons
 * @package ItalyStrap\Settings
 */
interface SectionsInterface {

	/**
	 * @return array
	 */
	public function getSections(): array;

	/**
	 * Init settings for admin area
	 */
	public function register();

	/**
	 * Render section CB
	 *
	 * @param array $args The arguments for section CB.
	 */
	public function renderSection( array $args );

	/**
	 * Get the field type
	 *
	 * @param array $args Array with arguments.
	 * @return mixed|string
	 */
	public function renderField( array $args );

	/**
	 * @inheritDoc
	 */
	public function count(): int;

	/**
	 * @param Page $page
	 * @return Sections
	 */
	public function forPage( PageInterface $page );

	/**
	 * @return string
	 */
	public function getPageSlug(): string;
}
