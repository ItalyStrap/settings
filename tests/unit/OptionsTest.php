<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Settings\OptionsInterface;

class OptionsTest extends \Codeception\Test\Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var array
	 */
	private $option_storage;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->option_storage = [];
		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'add_option', function ( $key, $value ) {
			$this->option_storage[ $key ] = $value;
		} );
		$this->option_storage = [];

		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'get_option', function ( $key, $default = [] ) {

			if ( ! \array_key_exists( $key, $this->option_storage ) ) {
				return $default;
			}

			return $this->option_storage[ $key ];
		} );
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	public function getIntance( $name = '', $values = [] ): OptionsInterface {
		$sut = new \ItalyStrap\Settings\Options( $name, $values );
		$this->assertInstanceOf( \ItalyStrap\Settings\Options::class, $sut );
		$this->assertInstanceOf( \ItalyStrap\Settings\OptionsInterface::class, $sut );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeinstantiable() {
		$sut = $this->getIntance( 'test' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnName() {
		$sut = $this->getIntance( 'test' );
		$this->assertStringContainsString( 'test', $sut->getName(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnCorrectArrayValue() {

		$option_name = 'test';
		\add_option( $option_name, ['key' => 'value'] );

		$sut = $this->getIntance( $option_name );
		$this->assertArrayHasKey( 'key', $sut->toArray(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnDefaultValueIfArrayKeyDoesNotExists() {

		$option_name = 'test';
		$sut = $this->getIntance( $option_name, ['key' => 'value'] );
		$this->assertArrayHasKey( 'key', $sut->toArray(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldGetKey() {

		$option_name = 'test';
		$sut = $this->getIntance( $option_name, ['key' => 'value'] );
		$this->assertStringContainsString( 'value', $sut->get( 'key' ), '' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnDefaultValue() {

		$option_name = 'test';
		$sut = $this->getIntance( $option_name );
		$this->assertStringContainsString( 'value', $sut->get( 'key', 'value' ), '' );
	}
}
