<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class BaseFilter
 * @package ItalyStrap\Tests
 */
class BaseFilter extends Unit
{
    /**
     * @test
     */
    public function itShouldBeInstantiable(): void
    {
        /** @var FilterableInterface $sut */
        $sut = $this->getInstance();
        $this->assertInstanceOf(FilterableInterface::class, $sut, '');
    }

    /**
     * @test
     */
    public function itShouldHasDefaultSchemaArray(): void
    {
        /** @var FilterableInterface $sut */
        $sut = $this->getInstance();
        $this->assertArrayHasKey($sut::KEY, $sut->getDefault(), '');
        $this->assertFalse($sut->getDefault()[ $sut::KEY ], '');
    }
}
