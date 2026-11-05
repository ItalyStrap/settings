<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Tests\Unit\Option;

use Codeception\Test\Unit;
use ItalyStrap\Settings\Values;
use PHPUnit\Framework\Attributes\Test;

final class ValuesTest extends Unit
{
    #[Test]
    public function aCheckboxIsCheckedWhenItsValueIsOne(): void
    {
        $values = new Values(['active' => '1', 'reload' => '', 'target' => 'yes']);

        $this->assertTrue($values->isChecked('active'));
        $this->assertFalse($values->isChecked('reload'));
        $this->assertFalse($values->isChecked('target'));
        $this->assertFalse($values->isChecked('missing'));
    }

    #[Test]
    public function aTextIsTheDefaultWhenItIsMissingEmptyOrNotAString(): void
    {
        $values = new Values(['url' => 'https://example.com', 'empty' => '', 'number' => 3]);

        $this->assertSame('https://example.com', $values->text('url'));
        $this->assertSame('none', $values->text('empty', 'none'));
        $this->assertSame('none', $values->text('number', 'none'));
        $this->assertSame('', $values->text('missing'));
    }

    #[Test]
    public function aListHasOnlyItsNonEmptyTexts(): void
    {
        $values = new Values(['patterns' => ['a', '', ' ', 3, 'b'], 'text' => 'not a list']);

        $this->assertSame(['a', 'b'], $values->texts('patterns'));
        $this->assertSame([], $values->texts('text'));
        $this->assertSame([], $values->texts('missing'));
    }

    #[Test]
    public function theRecordsAreTheArraysOfAList(): void
    {
        $values = new Values(['vendors' => [['id' => 'a'], 'x', ['id' => 'b']], 'text' => 'not a list']);

        $this->assertSame([['id' => 'a'], ['id' => 'b']], $values->records('vendors'));
        $this->assertSame([], $values->records('text'));
    }

    #[Test]
    public function aMapIsKeyedByText(): void
    {
        $values = new Values(['categories' => ['marketing' => ['label' => 'Ads'], 7 => ['label' => 'Seven']]]);

        $this->assertSame(
            ['marketing' => ['label' => 'Ads'], 7 => ['label' => 'Seven']],
            $values->map('categories')
        );
        $this->assertSame([], (new Values(['categories' => 'x']))->map('categories'));
    }

    #[Test]
    public function itHasAllTheValues(): void
    {
        $this->assertSame(['a' => '1'], (new Values(['a' => '1']))->toArray());
    }
}
