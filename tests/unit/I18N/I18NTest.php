<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\I18N\Translator;

use function tad\FunctionMockerLe\define;

class I18NTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
    }

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
    }

    private function getInstance(): Translator
    {
        $sut = new Translator('some-domain');
        $this->assertInstanceOf(Translator::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable(): void
    {
        $this->getInstance();
    }

    /**
     * @test
     */
    public function itShouldBeHasDeomain(): void
    {
        $sut = $this->getInstance();
        $this->assertStringContainsString('some-domain', $sut->getDomain(), '');
    }

    /**
     * @test
     */
    public function itShouldBeHasDeomainsdgs(): void
    {

		// phpcs:ignore -- This is not a constant definition
		define( 'get_locale', fn(): string => 'it_IT');

        $sut = $this->getInstance();
        $this->assertStringContainsString('it_IT', $sut->getLanguage(), '');
    }
}
