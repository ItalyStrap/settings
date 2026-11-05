<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Tests\Unit\Option;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Settings\Checkbox;
use ItalyStrap\Settings\Choice;
use ItalyStrap\Settings\Html;
use ItalyStrap\Settings\Schema;
use ItalyStrap\Settings\Text;
use PHPUnit\Framework\Attributes\Test;

final class SchemaTest extends Unit
{
    #[Test]
    public function itHasTheNameAndTheVersionOfTheOption(): void
    {
        $schema = new Schema('my_plugin', 3);

        $this->assertSame('my_plugin', $schema->name());
        $this->assertSame(3, $schema->version());
        $this->assertSame([], $schema->properties());
    }

    #[Test]
    public function theDefaultsAreThoseOfThePropertiesThatHaveOne(): void
    {
        $schema = $this->schema();

        $this->assertSame(['banner' => '1', 'cookie_name' => 'accepted', 'youtube' => '1'], $schema->defaults());
    }

    #[Test]
    public function theJsonSchemaDescribesAnObjectWithOnlyItsProperties(): void
    {
        $this->assertSame(
            [
                'type' => 'object',
                'properties' => [
                    'active' => ['type' => 'string', 'enum' => ['1']],
                    'banner' => ['type' => 'string', 'enum' => ['1', '2']],
                    'cookie_name' => ['type' => 'string', 'minLength' => 1],
                    'text' => ['type' => 'string'],
                    'youtube' => ['type' => 'string', 'enum' => ['1']],
                ],
                'additionalProperties' => false,
            ],
            $this->schema()->jsonSchema()
        );
    }

    #[Test]
    public function thePropertiesKeepTheirOrder(): void
    {
        $this->assertSame(
            ['active', 'banner', 'cookie_name', 'text', 'youtube'],
            \array_keys($this->schema()->properties())
        );
    }

    #[Test]
    public function aPropertyNameIsUnique(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"active"');
        new Schema('my_plugin', 1, new Checkbox('active'), new Checkbox('active'));
    }

    #[Test]
    public function theNameIsNotEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Schema('', 1);
    }

    #[Test]
    public function theVersionIsAtLeastOne(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Schema('my_plugin', 0);
    }

    private function schema(): Schema
    {
        return new Schema(
            'my_plugin',
            2,
            new Checkbox('active'),
            new Choice('banner', ['1', '2'], '1'),
            new Text('cookie_name', 'accepted', required: true),
            new Html('text'),
            new Checkbox('youtube', true),
        );
    }
}
