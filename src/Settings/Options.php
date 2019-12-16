<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Class Options
 * @package ItalyStrap\Settings
 */
class Options
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $group;
	/**
	 * @var array
	 */
	private $default;

	public function __construct( string $name, string $group = '', $default = [] ) {
		$this->name = $name;
		$this->group = $group;
		$this->default = $default;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return mixed
	 */
	public function getGroup() {
		return $this->group;
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
		return 	\add_option( $this->name, $values );
	}

	/**
	 * @return bool
	 */
	public function remove() {
		return \delete_option( $this->name );
	}
}
