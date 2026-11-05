<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use ItalyStrap\Cleaner\Validation;
use ItalyStrap\DataParser\Exception\InvalidValue;
use ItalyStrap\DataParser\FilterableInterface;
use ItalyStrap\DataParser\Filters\ValidateFilter;
use Prophecy\Argument;

use function tad\FunctionMockerLe\define;

// phpcs:disable
require_once __DIR__ . '/BaseFilter.php';
// phpcs:enable

/**
 * Class RequiredTest
 * @package ItalyStrap\Tests
 */
class ValidateTest extends BaseFilter
{
    use ProphecyTrait;



    /**
     * @var \UnitTester
     */
    protected $tester;

    private ?ObjectProphecy $validation = null;

    public function getValidation(): Validation
    {
        return $this->validation->reveal();
    }

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
        $this->validation = $this->prophesize(Validation::class);

		// phpcs:ignore -- This is not a constant definition
		define( 'is_email', fn($string): string|false => \filter_var($string, FILTER_VALIDATE_EMAIL));
    }

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
    }

    protected function getInstance(): FilterableInterface
    {
        $sut = new ValidateFilter($this->getValidation());
        $this->assertInstanceOf(ValidateFilter::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldReturnValidEmailIfValidationInSchemaIsFalse(): void
    {
        $sut = $this->getInstance();
        $value = $sut->filter('key', 'test@localhost.com', [ $sut::KEY => false ]);
        $this->assertStringContainsString('test@localhost.com', $value, '');
    }

    /**
     * @test
     */
    public function itShouldReturnValidEmailIfValidationReturnTrue(): void
    {

        $this->validation->addRules(Argument::type('string'))->willReturn(new Validation());
        $this->validation->validate(Argument::any())->willReturn(true);

        $sut = $this->getInstance();
        $value = $sut->filter('key', 'test@localhost.com', [ $sut::KEY => 'is_email' ]);
        $this->assertStringContainsString('test@localhost.com', $value, '');
    }

    /**
     * @test
     */
    public function itShouldThrownExceptionIfValueIsNotValidAndIfValidationReturnFalse(): void
    {

        $this->validation->addRules(Argument::type('string'))->willReturn(new Validation());
        $this->validation->validate(Argument::any())->willReturn(false);

        $sut = $this->getInstance();
        $this->expectException(InvalidValue::class);
        $sut->filter('key', 'not-a-valid-email', [ $sut::KEY => 'is_email' ]);
    }
}
