<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Cleaner\Sanitization;
use ItalyStrap\Cleaner\Validation;
use ItalyStrap\DataParser\Exception\InvalidValue;
use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\SanitizeFilter;
use ItalyStrap\DataParser\Filters\TranslateFilter;
use ItalyStrap\DataParser\Filters\ValidateFilter;
use ItalyStrap\DataParser\Parser;
use ItalyStrap\DataParser\ParserFactory;
use ItalyStrap\DataParser\ParserInterface;
use ItalyStrap\I18N\Translator;

class ParserTest extends Unit {

	/**
	 * @var \UnitTester
	 */
	protected $tester;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {

		// phpcs:ignore -- This is not a constant definition
		\tad\FunctionMockerLe\define( 'is_email', function ( $string ) {
			return \filter_var( $string, FILTER_VALIDATE_EMAIL );
		} );
	}

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
	}

	public function getInstance(): Parser {
		$sut = new Parser();
		$this->assertInstanceOf( ParserInterface::class, $sut, '' );
		$this->assertInstanceOf( Parser::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$this->getInstance();

		$sut = ParserFactory::make();
		$this->assertInstanceOf( ParserInterface::class, $sut, '' );
		$this->assertInstanceOf( Parser::class, $sut, '' );
	}

	/**
	 * @test
	 */
	public function itShouldGetSchema() {
		$sut = $this->getInstance();
		$this->assertIsArray( $sut->getSchema(), '' );
		$this->assertEmpty( $sut->getSchema(), '' );

		$sut->withSchema( [ 'some-key' => 'some-value' ] );
		$this->assertIsArray( $sut->getSchema(), '' );
		$this->assertNotEmpty( $sut->getSchema(), '' );
		$this->assertArrayHasKey( 'some-key', $sut->getSchema(), '' );
		$this->assertEquals( [ 'some-key' => 'some-value' ], $sut->getSchema(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldThrownExceptionIfNoFiltersAreProvided() {
		$sut = $this->getInstance();
		$this->expectException( \RuntimeException::class );
		$data = $sut->parseValues( [ 'test' => '<h1>value</h1>' ] );
	}

	/**
	 * @test
	 */
	public function itShouldReturnFilteredDataWithProvidedCustomFilters() {
		$sut = $this->getInstance();

		$filter = new class implements FilterableInterface {

			const KEY = 'sanitize';

			private function getSanitize() {
				return new Sanitization();
			}

			public function filter( string $key, $value, array $schema ) {
				$san = $this->getSanitize();
				$san->addRules( $schema[ self::KEY ] );
				return $san->sanitize( $value );
			}

			/**
			 * @inheritDoc
			 */
			public function getDefault() {
				return [ self::KEY => 'strip_tags|trim' ];
			}
		};

		$sut->withFilters( $filter );

		$sut->withSchema(
			[
				'test'	=> [],
			]
		);
		$data = $sut->parseValues(
			[
				'test'	=> '<h1> value1 </h1>',
				'test2'	=> '<h1> value2 </h1>'
			]
		);
		$this->assertEquals(
			[
				'test'	=> 'value1',
				'test2'	=> '<h1> value2 </h1>'
			],
			$data,
			''
		);
	}

	/**
	 * @test
	 */
	public function itShouldReturnFilteredDataWithProvidedFilters() {
		$sut = $this->getInstance();

		$san = new Sanitization();
		$filter = new SanitizeFilter( $san );
		$sut->withFilters( $filter );

		$sut->withSchema(
			[
				'test'	=> [
					'sanitize'		=> 'strip_tags|trim'
				],
			]
		);
		$data = $sut->parseValues( [ 'test' => '<h1> value1 </h1>', 'test2' => '<h1> value2 </h1>' ] );
		$this->assertEquals( [ 'test' => 'value1', 'test2' => '<h1> value2 </h1>' ], $data, '' );
	}

	/**
	 * @test
	 */
	public function itShouldCheckeAndReturnEmptyStringIfDataHasNotKeyFromSchema() {
		$sut = $this->getInstance();

		$san = new Sanitization();
		$filter = new SanitizeFilter( $san );
		$sut->withFilters( $filter );

		$sut->withSchema(
			[
				'test'	=> [
					'sanitize'		=> 'strip_tags|trim'
				],
			]
		);
		$data = $sut->parseValues( [ 'other-key' => 'with-value' ] );
		$this->assertIsString( $data['test'], '' );
		$this->assertTrue( isset( $data['test'] ), '' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnValidatedDataWithProvidedFilters() {

		$sut = $this->getInstance();

		$val = new Validation();
		$filter = new ValidateFilter( $val );
		$sut->withFilters( $filter );

		$sut->withSchema(
			[
				'email'	=> [
					'validate'		=> 'is_email'
				],
			]
		);

		$data = $sut->parseValues( [ 'email' => 'test@localhost.com' ] );
		$this->assertEquals( [ 'email' => 'test@localhost.com' ], $data, '' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnEmptyStringIfValidationFail() {

		$sut = $this->getInstance();

		$val = new Validation();
		$filter = new ValidateFilter( $val );
		$sut->withFilters( $filter );

		$sut->withSchema(
			[
				'email'	=> [
					'validate'		=> 'is_email'
				],
			]
		);

		$this->expectException( InvalidValue::class );
		$data = $sut->parseValues( [ 'email' => 'invalid_email' ] );
	}

	/**
	 * @test
	 */
	public function itShouldReturnSanitizedAndValidatedEmail() {

		$sut = $this->getInstance();

		$san = new Sanitization();
		$filter_san = new SanitizeFilter( $san );

		$val = new Validation();
		$filter_val = new ValidateFilter( $val );

		$sut->withFilters( $filter_san, $filter_val );

		$sut->withSchema(
			[
				'email'	=> [
					'sanitize'		=> [
						function ( $string ) {
							return \filter_var( $string, FILTER_SANITIZE_STRING );
						},
					],
					'validate'		=> 'is_email',
				],
			]
		);

		$data = $sut->parseValues( [ 'email' => '<p>test@localhost.com</p>' ] );
		$this->assertEquals( [ 'email' => 'test@localhost.com' ], $data, '' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnSanitizedValidatedAnsTranslatedEmail() {

		$sut = $this->getInstance();

		$san = new Sanitization();
		$filter_san = new SanitizeFilter( $san );

		$val = new Validation();
		$filter_val = new ValidateFilter( $val );

		$tras = new Translator( 'name' );
		$filter_tras = new TranslateFilter( $tras );

		$sut->withFilters( $filter_san, $filter_val, $filter_tras );

		$sut->withSchema(
			[
				'email'	=> [
					'sanitize'		=> [
						function ( $string ) {
							return \filter_var( $string, FILTER_SANITIZE_STRING );
						},
					],
					'validate'		=> 'is_email',
					'translate'		=> true,
				],
			]
		);

		$data = $sut->parseValues( [ 'email' => '<p>test@localhost.com</p>' ] );
		$this->assertEquals( [ 'email' => 'test@localhost.com' ], $data, '' );
	}

	/**
	 * @test
	 */
	public function itShouldParseArrayInDataValue() {

		$sut = $this->getInstance();

		$san = new Sanitization();
		$filter_san = new SanitizeFilter( $san );

		$sut->withFilters(
			$filter_san
		);

		$sut->withSchema(
			[
				'emails'	=> [
					'sanitize'		=> [
						function ( $string ) {
							if ( ! \is_array( $string ) ) {
								return \filter_var( $string, FILTER_SANITIZE_STRING );
							}

							foreach ( (array) $string as $key => $value ) {
								$string[ $key ] = \filter_var( $value, FILTER_SANITIZE_STRING );
							}

							return $string;
						},
					],
				],
			]
		);

		$data_to_parse = [
			'emails' => [
				'<p>test@localhost.com</p>',
				'<p>test@localhost.com</p>',
			],
		];

		$expected = [
			'emails' => [
				'test@localhost.com',
				'test@localhost.com',
			],
		];

		$data = $sut->parseValues( $data_to_parse );
		$this->assertEquals( $expected, $data, '' );
	}
}
