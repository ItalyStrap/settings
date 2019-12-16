<?php

class ViewPageTest extends \Codeception\Test\Unit
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected function _before() {
		\tad\FunctionMockerLe\define( 'current_user_can', function () { return true; });
		\tad\FunctionMockerLe\define( 'includes_url', function () { return ''; });
		\tad\FunctionMockerLe\define( 'apply_filters', function ( $filter, $value ) { return $value; });
		\tad\FunctionMockerLe\define( 'esc_attr', function ( $value ) { return $value; });
		\tad\FunctionMockerLe\define( 'esc_html', function ( $value ) { return $value; });
		\tad\FunctionMockerLe\define( 'do_action', function ( $hook, $value ) {});

	}

	protected function _after() {
	}

	public function getInstance(): \ItalyStrap\Settings\ViewPage {
		$finder = new \ItalyStrap\View\ViewFinder();
		$finder->in( codecept_data_dir() );
		$sut = new \ItalyStrap\Settings\ViewPage( $finder );
		$this->assertInstanceOf( \ItalyStrap\Settings\ViewPage::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function ItShouldBeInstantiable() {
		$this->getInstance();
	}

	/**
	 * @test
	 */
//	public function ItShouldBeInstantiabljhgkh() {
//		$sut = $this->getInstance();
//		$sut->render( '' );
//		$this->expectOutputString( '' );
//	}
}
