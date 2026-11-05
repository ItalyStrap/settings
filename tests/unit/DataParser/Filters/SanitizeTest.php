<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use ItalyStrap\Cleaner\Sanitization;
use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\SanitizeFilter;
use Prophecy\Argument;

// phpcs:disable
require_once __DIR__ . '/BaseFilter.php';
// phpcs:enable

/**
 * Class SanitizeTest
 * @package ItalyStrap\Tests
 */
class SanitizeTest extends BaseFilter
{
    use ProphecyTrait;


    /**
     * @var \UnitTester
     */
    protected $tester;

    private ?ObjectProphecy $sanitization = null;

    public function getSanitization(): Sanitization
    {
        return $this->sanitization->reveal();
    }

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
        $this->sanitization = $this->prophesize(Sanitization::class);
    }

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
    }

    protected function getInstance(): FilterableInterface
    {
        $sut = new SanitizeFilter($this->getSanitization());
        $this->assertInstanceOf(SanitizeFilter::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldFilter(): void
    {

        $this->sanitization->addRules(Argument::type('string'))->willReturn(new Sanitization());
        $this->sanitization->sanitize(Argument::any())->willReturn('value');

        $sut = $this->getInstance();
        $value = $sut->filter('', 'value', [ $sut::KEY => 'trim' ]);
        $this->assertStringContainsString('value', $value, '');
    }
}
