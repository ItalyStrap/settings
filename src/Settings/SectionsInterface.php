<?php

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
	 */
	public function renderField( array $args );

	/**
	 * @return string
	 */
	public function getGroup(): string;

	/**
	 * @inheritDoc
	 */
	public function count(): int;
}
