<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Tests\Unit\Option;

use Codeception\Test\Unit;
use ItalyStrap\Settings\Checkbox;
use ItalyStrap\Settings\Html;
use ItalyStrap\Settings\Sanitizer;
use ItalyStrap\Settings\Schema;
use ItalyStrap\Settings\Tests\Unit\Option\Fake\MarkingSanitizing;
use ItalyStrap\Settings\Text;
use PHPUnit\Framework\Attributes\Test;

final class SanitizerTest extends Unit
{
    #[Test]
    public function itKeepsOnlyThePropertiesInTheirOrderSanitized(): void
    {
        $this->assertSame(
            ['active' => '1', 'color' => 'text(#000)', 'text' => 'html(<b>Hi</b>)'],
            $this->sanitize(['text' => '<b>Hi</b>', 'unknown' => 'x', 'color' => '#000', 'active' => '1'])
        );
    }

    #[Test]
    public function aMissingValueIsDroppedOrGetsItsDefault(): void
    {
        $this->assertSame(['color' => '#fff'], $this->sanitize([]));
    }

    #[Test]
    public function anInputThatIsNotAnArrayIsEmpty(): void
    {
        $this->assertSame(['color' => '#fff'], $this->sanitize('not an array'));
    }

    /**
     * @return array<string, mixed>
     */
    private function sanitize(mixed $input): array
    {
        $schema = new Schema('my_plugin', 1, new Checkbox('active'), new Text('color', '#fff'), new Html('text'));

        return (new Sanitizer($schema, new MarkingSanitizing()))($input);
    }
}
