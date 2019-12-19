<?php
class DataParserTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

	public function getInstance(): \ItalyStrap\Settings\DataParser{
		$sut = new \ItalyStrap\Settings\DataParser();
		$this->assertInstanceOf( \ItalyStrap\Settings\DataParser::class, $sut, '' );
		return $sut;
    }

	/**
	 * @test
	 */
	public function ItShouldBeInstantiable() {
		$sut = $this->getInstance();
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
	public function ItShouldReturnFilteredDataWithDefaultStripTagsAndTrimIfNoFiltersAreProvided() {
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
	public function ItShouldReturnFilteredDataWithProvidedFilters() {
		$sut = $this->getInstance();

		$filter = new class implements \ItalyStrap\Settings\FilterableInterface {

			private function getSanitize() {
				return new \ItalyStrap\Cleaner\Sanitization();
			}

			public function filter( $schema, $data ) {
				$san = $this->getSanitize();
				$san->addRules( $schema['sanitize'] );
				return $san->sanitize( $data[ $schema['id'] ] );
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
}
