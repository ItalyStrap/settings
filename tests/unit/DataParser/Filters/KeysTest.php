<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\DataParser\Filters\Keys;

/**
 * Class KeysTest
 * @package ItalyStrap\Tests
 */
class KeysTest extends Unit
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

    private function getInstance(): Keys
    {
        return new class implements Keys {
        };
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
    public function itShouldBeKeySetCorrectly(): void
    {
        $sut = $this->getInstance();

        $this->assertStringContainsString('capability', $sut::CAPABILITY, '');
        $this->assertStringContainsString('required', $sut::REQUIRED, '');
        $this->assertStringContainsString('sanitize', $sut::SANITIZE, '');
        $this->assertStringContainsString('option-type', $sut::THEME_MOD, '');
        $this->assertStringContainsString('translate', $sut::TRANSLATE, '');
        $this->assertStringContainsString('validate', $sut::VALIDATE, '');
    }
}
