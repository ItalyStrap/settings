<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\Config;

/**
 * Class Options
 * @package ItalyStrap\Settings
 */
class Options extends Config implements OptionsInterface {

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
		parent::__construct( (array) \get_option( $this->name, $default ), $default );
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @param array $values
	 * @return bool
	 */
	public function addAll( array $values = [] ) {
		return \add_option( $this->name, $values );
	}

	/**
	 * @inheritDoc
	 */
	public function removeAll() {
		return \delete_option( $this->name );
	}
}
