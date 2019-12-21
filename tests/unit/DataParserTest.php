<?php
class DataParserTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
		\tad\FunctionMockerLe\define( 'is_email', function ( $string ) {
			return \filter_var( $string, FILTER_VALIDATE_EMAIL );
		} );
    }

    protected function _after()
    {
    }

	public function getInstance(): \ItalyStrap\DataParser\DataParser{
		$sut = new \ItalyStrap\DataParser\DataParser();
		$this->assertInstanceOf( \ItalyStrap\DataParser\DataParserInterface::class, $sut, '' );
		$this->assertInstanceOf( \ItalyStrap\DataParser\DataParser::class, $sut, '' );
		return $sut;
    }

	/**
	 * @test
	 */
	public function ItShouldBeInstantiable() {
		$this->getInstance();

		$sut = \ItalyStrap\DataParser\DataParserFactory::make();
		$this->assertInstanceOf( \ItalyStrap\DataParser\DataParserInterface::class, $sut, '' );
		$this->assertInstanceOf( \ItalyStrap\DataParser\DataParser::class, $sut, '' );
    }

	/**
	 * @test
	 */
	public function ItShouldReturnNotFilteredData() {
		$sut = $this->getInstance();
		$data = $sut->parse( [ 'test' => '<h1>value</h1>' ] );
		$this->assertEquals( [ 'test' => '<h1>value</h1>' ], $data, '' );
	}

	/**
	 * @test
	 */
	private function ItShouldReturnFilteredDataWithDefaultStripTagsAndTrimIfNoFiltersAreProvided() {
		$sut = $this->getInstance();
		$sut->withSchema(
			[
				[
					'id'	=> 'test',
				],
			]
		);
		$data = $sut->parse( [ 'test' => '<h1> value </h1>' ] );
		$this->assertEquals( [ 'test' => 'value' ], $data, '' );
	}

	/**
	 * @test
	 */
	public function ItShouldReturnFilteredDataWithProvidedCustomFilters() {
		$sut = $this->getInstance();

		$filter = new class implements \ItalyStrap\DataParser\FilterableInterface {

			private function getSanitize() {
				return new \ItalyStrap\Cleaner\Sanitization();
			}

			public function filter( $data , array $schema ) {
				$san = $this->getSanitize();
				$san->addRules( $schema['sanitize'] );
				return $san->sanitize( $data );
			}
		};

		$sut->withFilters( $filter );

		$sut->withSchema(
			[
				[
					'id'	=> 'test',
				],
			]
		);
		$data = $sut->parse( [ 'test' => '<h1> value1 </h1>', 'test2' => '<h1> value2 </h1>' ] );
		$this->assertEquals( [ 'test' => 'value1', 'test2' => '<h1> value2 </h1>' ], $data, '' );
    }

	/**
	 * @test
	 */
	public function ItShouldReturnFilteredDataWithProvidedFilters() {
		$sut = $this->getInstance();

		$san = new \ItalyStrap\Cleaner\Sanitization();
		$filter = new \ItalyStrap\DataParser\Filters\SanitizeFilter( $san );
		$sut->withFilters( $filter );

		$sut->withSchema(
			[
				[
					'id'			=> 'test',
					'sanitize'		=> 'strip_tags|trim'
				],
			]
		);
		$data = $sut->parse( [ 'test' => '<h1> value1 </h1>', 'test2' => '<h1> value2 </h1>' ] );
		$this->assertEquals( [ 'test' => 'value1', 'test2' => '<h1> value2 </h1>' ], $data, '' );
    }

	/**
	 * @test
	 */
	public function ItShouldReturnValidatedDataWithProvidedFilters() {

		$sut = $this->getInstance();

		$val = new \ItalyStrap\Cleaner\Validation();
		$filter = new \ItalyStrap\DataParser\Filters\ValidateFilter( $val );
		$sut->withFilters( $filter );

		$sut->withSchema(
			[
				[
					'id'			=> 'email',
					'validate'		=> 'is_email'
				],
			]
		);

		$data = $sut->parse( [ 'email' => 'test@localhost.com' ] );
		$this->assertEquals( [ 'email' => 'test@localhost.com' ], $data, '' );

		$sut->withSchema(
			[
				[
					'id'			=> 'email2',
					'validate'		=> [
						function ( $string ) {
							return is_email( $string );
						},
					]
				],
			]
		);

		$data = $sut->parse( [ 'email2' => 'test@localhost.com' ] );
		$this->assertEquals( [ 'email2' => 'test@localhost.com' ], $data, '' );
    }

	/**
	 * @test
	 */
	public function ItShouldReturnEmptyStringIfValidationFail() {

		$sut = $this->getInstance();

		$val = new \ItalyStrap\Cleaner\Validation();
		$filter = new \ItalyStrap\DataParser\Filters\ValidateFilter( $val );
		$sut->withFilters( $filter );

		$sut->withSchema(
			[
				[
					'id'			=> 'email',
					'validate'		=> 'is_email'
				],
			]
		);

		$data = $sut->parse( [ 'email' => 'invalid_email' ] );
		$this->assertEquals( [ 'email' => '' ], $data, '' );
    }

	/**
	 * @test
	 */
	public function ItShouldReturnSanitizedAndValidatedEmail() {

		$sut = $this->getInstance();

		$san = new \ItalyStrap\Cleaner\Sanitization();
		$filter_san = new \ItalyStrap\DataParser\Filters\SanitizeFilter( $san );

		$val = new \ItalyStrap\Cleaner\Validation();
		$filter_val = new \ItalyStrap\DataParser\Filters\ValidateFilter( $val );

		$sut->withFilters( $filter_san, $filter_val );

		$sut->withSchema(
			[
				[
					'id'			=> 'email',
					'sanitize'		=> [
						function ( $string ) {
							return \filter_var( $string, FILTER_SANITIZE_STRING );
						},
					],
					'validate'		=> 'is_email',
				],
			]
		);

		$data = $sut->parse( [ 'email' => '<p>test@localhost.com</p>' ] );
		$this->assertEquals( [ 'email' => 'test@localhost.com' ], $data, '' );
    }

	/**
	 * @test
	 */
	public function ItShouldReturnSanitizedValidatedAnsTranslatedEmail() {

		$sut = $this->getInstance();

		$san = new \ItalyStrap\Cleaner\Sanitization();
		$filter_san = new \ItalyStrap\DataParser\Filters\SanitizeFilter( $san );

		$val = new \ItalyStrap\Cleaner\Validation();
		$filter_val = new \ItalyStrap\DataParser\Filters\ValidateFilter( $val );

		$tras = new \ItalyStrap\I18N\Translator( 'name' );
		$filter_tras = new \ItalyStrap\DataParser\Filters\TranslateFilter( $tras );

		$sut->withFilters( $filter_san, $filter_val, $filter_tras );

		$sut->withSchema(
			[
				[
					'id'			=> 'email',
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

		$data = $sut->parse( [ 'email' => '<p>test@localhost.com</p>' ] );
		$this->assertEquals( [ 'email' => 'test@localhost.com' ], $data, '' );
    }

	/**
	 * @test
	 */
	public function ItShouldParseArrayInDataValue() {

		$sut = $this->getInstance();

		$san = new \ItalyStrap\Cleaner\Sanitization();
		$filter_san = new \ItalyStrap\DataParser\Filters\SanitizeFilter( $san );

		$val = new \ItalyStrap\Cleaner\Validation();
		$filter_val = new \ItalyStrap\DataParser\Filters\ValidateFilter( $val );

		$tras = new \ItalyStrap\I18N\Translator( 'name' );
		$filter_tras = new \ItalyStrap\DataParser\Filters\TranslateFilter( $tras );

		$sut->withFilters(
			$filter_san,
			$filter_val,
			$filter_tras
		);

		$sut->withSchema(
			[
				[
					'id'			=> 'emails',
					'sanitize'		=> [
						function ( $string ) {
							return \filter_var( $string, FILTER_SANITIZE_STRING );
						},
					],
//					'validate'		=> 'is_email',
//					'translate'		=> true,
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

//		codecept_debug( $data_to_parse );

		$data = $sut->parse( $data_to_parse );

//		codecept_debug( $data );

		$this->assertEquals( $expected, $data, '' );
    }
}
