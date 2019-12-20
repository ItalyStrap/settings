<?php

class OptionsTest extends \Codeception\Test\Unit
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var array
	 */
	private $option_storage;

	protected function _before() {
		$this->option_storage = [];
		\tad\FunctionMockerLe\define( 'add_option', function ( $key, $value ) {
			$this->option_storage[ $key ] = $value;
		} );
		$this->option_storage = [];

		\tad\FunctionMockerLe\define( 'get_option', function ( $key, $default = [] ) {

			if ( !\array_key_exists( $key, $this->option_storage ) ) {
				return $default;
			}

			return $this->option_storage[ $key ];
		} );
	}

	protected function _after() {
	}

	public function getIntance( $name = '', $group = '', $values = [] ): \ItalyStrap\Settings\Options {
		$sut = new \ItalyStrap\Settings\Options( $name, $values );
		$this->assertInstanceOf( \ItalyStrap\Settings\Options::class, $sut );
		return $sut;
	}

	/**
	 * @test
	 */
	public function ItShouldBeinstantiable() {
		$this->getIntance( 'test' );
	}

	/**
	 * @test
	 */
	public function ItShouldReturnName() {
		$sut = $this->getIntance( 'test' );
		$this->assertStringContainsString( 'test', $sut->getName(), '' );
	}

	/**
	 * @test
	 */
	public function ItShouldReturnCorrectArrayValue() {

		$option_name = 'test';
		\add_option( $option_name, ['key' => 'value'] );

		$sut = $this->getIntance( $option_name );
		$this->assertArrayHasKey( 'key', $sut->get(), '' );
	}

	/**
	 * @test
	 */
	public function ItShouldReturnDefaultValueIfArrayKeyDoesNotExists() {

		$option_name = 'test';
		$sut = $this->getIntance( $option_name, '', ['key' => 'value'] );
		$this->assertArrayHasKey( 'key', $sut->get(), '' );
	}
}
