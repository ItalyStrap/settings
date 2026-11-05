<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Settings\AssetLoader;
use ItalyStrap\Config\Config;

class AssetTest extends Unit
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

    private function getInstance(): AssetLoader
    {
        $config = $this->make(Config::class);
        $sut = new AssetLoader($config);
        $this->assertInstanceOf(AssetLoader::class, $sut, '');

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
