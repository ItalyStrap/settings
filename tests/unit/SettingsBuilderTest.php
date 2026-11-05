<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Settings\SettingsBuilder;

/**
 * Class SettingsBuilderTest
 * @package ItalyStrap\Tests
 */
class SettingsBuilderTest extends Unit
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

    private function getInstance(): SettingsBuilder
    {
        $sut = new SettingsBuilder(
            'option-name'
        );
        $this->assertInstanceOf(SettingsBuilder::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable(): void
    {
        $this->getInstance();
    }
}
