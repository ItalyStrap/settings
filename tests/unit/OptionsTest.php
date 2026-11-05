<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use ItalyStrap\Settings\Options;
use ItalyStrap\Settings\OptionsInterface;

use function tad\FunctionMockerLe\define;

class OptionsTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var array
     */
    private $option_storage;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
        $this->option_storage = [];
		// phpcs:ignore -- This is not a constant definition
		define( 'add_option', function ( $key, $value ): void {
            $this->option_storage[ $key ] = $value;
        });
        $this->option_storage = [];

		// phpcs:ignore -- This is not a constant definition
		define( 'get_option', function ( $key, $default = [] ) {

            if (! \array_key_exists($key, $this->option_storage)) {
                return $default;
            }

            return $this->option_storage[ $key ];
        });
    }

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
    }

    public function getIntance($name = '', $values = []): OptionsInterface
    {
        $sut = new Options($name, $values);
        $this->assertInstanceOf(Options::class, $sut);
        $this->assertInstanceOf(OptionsInterface::class, $sut);
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldBeinstantiable(): void
    {
        $this->getIntance('test');
    }

    /**
     * @test
     */
    public function itShouldReturnName(): void
    {
        $sut = $this->getIntance('test');
        $this->assertStringContainsString('test', $sut->getName(), '');
    }

    /**
     * @test
     */
    public function itShouldReturnCorrectArrayValue(): void
    {

        $option_name = 'test';
        \add_option($option_name, ['key' => 'value']);

        $sut = $this->getIntance($option_name);
        $this->assertArrayHasKey('key', $sut->toArray(), '');
    }

    /**
     * @test
     */
    public function itShouldReturnDefaultValueIfArrayKeyDoesNotExists(): void
    {

        $option_name = 'test';
        $sut = $this->getIntance($option_name, ['key' => 'value']);
        $this->assertArrayHasKey('key', $sut->toArray(), '');
    }

    /**
     * @test
     */
    public function itShouldGetKey(): void
    {

        $option_name = 'test';
        $sut = $this->getIntance($option_name, ['key' => 'value']);
        $this->assertStringContainsString('value', $sut->get('key'), '');
    }

    /**
     * @test
     */
    public function itShouldReturnDefaultValue(): void
    {

        $option_name = 'test';
        $sut = $this->getIntance($option_name);
        $this->assertStringContainsString('value', $sut->get('key', 'value'), '');
    }
}
