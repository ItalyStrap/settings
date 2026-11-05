<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\TranslateFilter;
use ItalyStrap\I18N\Translator;
use Prophecy\Argument;

// phpcs:disable
require_once __DIR__ . '/BaseFilter.php';
// phpcs:enable

/**
 * Class SanitizeTest
 * @package ItalyStrap\Tests
 */
class TranslateTest extends BaseFilter
{
    use ProphecyTrait;




    /**
     * @var \UnitTester
     */
    protected $tester;

    private ?ObjectProphecy $translator = null;

    private ?bool $translator_called = null;

    public function isTranslatorCalled(): bool
    {
        return $this->translator_called;
    }

    public function getTranslator(): Translator
    {
        return $this->translator->reveal();
    }

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
        $this->translator_called = false;
        $this->translator = $this->prophesize(Translator::class);
    }

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
    }

    protected function getInstance(): FilterableInterface
    {
        $sut = new TranslateFilter($this->getTranslator());
        $this->assertInstanceOf(TranslateFilter::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldRegisterTranslatedString(): void
    {

        $test = $this;

        $this->translator->registerString(
            Argument::type('string'),
            Argument::type('string')
        )->will(function ($args) use ($test): void {
            $test->translator_called = true;
        });

        $sut = $this->getInstance();
        $value = $sut->filter('some-key', 'value', [ $sut::KEY => true ]);
        $this->assertStringContainsString('value', $value, '');
        $this->assertTrue($this->isTranslatorCalled(), '');
    }

    /**
     * @test
     */
    public function itShouldNotRegisterTranslatedString(): void
    {

        $test = $this;

        $this->translator->registerString(
            Argument::type('string'),
            Argument::type('string')
        )->will(function ($args) use ($test): void {
            $test->translator_called = true;
        });

        $sut = $this->getInstance();
        $value = $sut->filter('', 'value', [ $sut::KEY => false ]);
        $this->assertStringContainsString('value', $value, '');
        $this->assertFalse($this->isTranslatorCalled(), '');
    }
}
