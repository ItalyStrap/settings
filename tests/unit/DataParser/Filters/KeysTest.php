<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\Filters\Keys;

/**
 * Class KeysTest
 * @package ItalyStrap\Tests
 */
class KeysTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

	// phpcs:ignore -- Method from Codeception
    protected function _before()
    {
    }

	// phpcs:ignore -- Method from Codeception
    protected function _after()
    {
    }

	private function getInstance(): Keys {
		return new class implements Keys {};
    }

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$sut = $this->getInstance();
    }

	/**
	 * @test
	 */
	public function itShouldBeKeySetCorrectly() {
		$sut = $this->getInstance();

		$this->assertStringContainsString( 'capability', $sut::CAPABILITY, '' );
		$this->assertStringContainsString( 'required', $sut::REQUIRED, '' );
		$this->assertStringContainsString( 'sanitize', $sut::SANITIZE, '' );
		$this->assertStringContainsString( 'option-type', $sut::THEME_MOD, '' );
		$this->assertStringContainsString( 'translate', $sut::TRANSLATE, '' );
		$this->assertStringContainsString( 'data-type', $sut::TYPE, '' );
		$this->assertStringContainsString( 'validate', $sut::VALIDATE, '' );
    }
}
