<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Class Options
 * @package ItalyStrap\Settings
 */
class Options {

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var array
	 */
	private $default;

	public function __construct( string $name, $default = [] ) {
		$this->name = $name;
		$this->default = $default;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function get(): array {
		return (array) \get_option( $this->name, $this->default );
	}

	/**
	 * @param array $values
	 * @return bool
	 */
	public function add( array $values = [] ) {
		return \add_option( $this->name, $values );
	}

	/**
	 * @return bool
	 */
	public function remove() {
		return \delete_option( $this->name );
	}
}
