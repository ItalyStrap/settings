<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\DataParser\FilterableInterface;

/**
 * Class BaseFilter
 * @package ItalyStrap\Tests
 */
class BaseFilter extends \Codeception\Test\Unit
{
	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		/** @var FilterableInterface $sut */
		$sut = $this->getInstance();
		$this->assertInstanceOf( FilterableInterface::class, $sut, '' );
	}

	/**
	 * @test
	 */
	public function itShouldHasDefaultSchemaArray() {
		/** @var FilterableInterface $sut */
		$sut = $this->getInstance();
		$this->assertArrayHasKey( $sut::KEY, $sut->getDefault(), '' );
	}
}
