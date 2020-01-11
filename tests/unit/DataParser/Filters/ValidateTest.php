<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Cleaner\Validation;
use ItalyStrap\DataParser\Exception\InvalidValue;
use ItalyStrap\DataParser\Exception\ValueRejected;
use ItalyStrap\DataParser\Exception\ValueRequired;
use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\CapabilityFilter;
use ItalyStrap\DataParser\Filters\RequiredFilter;
use ItalyStrap\DataParser\Filters\ValidateFilter;
use Prophecy\Argument;
use Symfony\Component\VarDumper\VarDumper;

// phpcs:disable
require_once 'BaseFilter.php';
// phpcs:enable

/**
 * Class RequiredTest
 * @package ItalyStrap\Tests
 */
class ValidateTest extends BaseFilter {


	/**
	 * @var \UnitTester
	 */
	protected $tester;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $validation;

	/**
	 * @return Validation
	 */
	public function getValidation(): Validation {
		return $this->validation->reveal();
	}

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		$this->validation = $this->prophesize( Validation::class );

		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'is_email', function ( $string ) {
			return \filter_var( $string, FILTER_VALIDATE_EMAIL );
		} );
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	protected function getInstance(): FilterableInterface {
		$sut = new ValidateFilter( $this->getValidation() );
		$this->assertInstanceOf( ValidateFilter::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldReturnValidEmailIfValidationInSchemaIsFalse() {
		$sut = $this->getInstance();
		$value = $sut->filter( 'key', 'test@localhost.com', [ $sut::KEY => false ] );
		$this->assertStringContainsString( 'test@localhost.com', $value, '' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnValidEmailIfValidationReturnTrue() {

		$this->validation->addRules( Argument::type( 'string' ) )->willReturn( new Validation() );
		$this->validation->validate( Argument::any() )->willReturn( true );

		$sut = $this->getInstance();
		$value = $sut->filter( 'key', 'test@localhost.com', [ $sut::KEY => 'is_email' ] );
		$this->assertStringContainsString( 'test@localhost.com', $value, '' );
	}

	/**
	 * @test
	 */
	public function itShouldThrownExceptionIfValueIsNotValidAndIfValidationReturnFalse() {

		$this->validation->addRules( Argument::type( 'string' ) )->willReturn( new Validation() );
		$this->validation->validate( Argument::any() )->willReturn( false );

		$sut = $this->getInstance();
		$this->expectException( InvalidValue::class );
		$sut->filter( 'key', 'not-a-valid-email', [ $sut::KEY => 'is_email' ] );
	}
}
