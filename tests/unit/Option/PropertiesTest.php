<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Tests\Unit\Option;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Settings\Checkbox;
use ItalyStrap\Settings\Choice;
use ItalyStrap\Settings\CustomProperty;
use ItalyStrap\Settings\Html;
use ItalyStrap\Settings\Pattern;
use ItalyStrap\Settings\Sanitizing;
use ItalyStrap\Settings\Tests\Unit\Option\Fake\MarkingSanitizing;
use ItalyStrap\Settings\Tests\Unit\Option\Fake\PassThroughSanitizing;
use ItalyStrap\Settings\Text;
use PHPUnit\Framework\Attributes\Test;

final class PropertiesTest extends Unit
{
    private MarkingSanitizing $sanitizing;

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore -- Codeception method
    protected function _before(): void
    {
        $this->sanitizing = new MarkingSanitizing();
    }

    #[Test]
    public function aCheckboxIsOnlyTheValueOne(): void
    {
        $checkbox = new Checkbox('active');

        $this->assertSame('active', $checkbox->name());
        $this->assertSame(['type' => 'string', 'enum' => ['1']], $checkbox->jsonSchema());
        $this->assertNull($checkbox->default());
        $this->assertSame('1', $checkbox->sanitize('1', $this->sanitizing));
        $this->assertNull($checkbox->sanitize(null, $this->sanitizing));
        $this->assertNull($checkbox->sanitize('yes', $this->sanitizing));
        $this->assertNull($checkbox->sanitize(1, $this->sanitizing));
    }

    #[Test]
    public function aCheckboxCanBeOnByDefault(): void
    {
        $this->assertSame('1', (new Checkbox('youtube_nocookie', true))->default());
    }

    #[Test]
    public function aChoiceIsOneOfItsValuesOrTheDefault(): void
    {
        $choice = new Choice('banner', ['1', '2', '3'], '1');

        $this->assertSame(['type' => 'string', 'enum' => ['1', '2', '3']], $choice->jsonSchema());
        $this->assertSame('1', $choice->default());
        $this->assertSame('2', $choice->sanitize('2', $this->sanitizing));
        $this->assertSame('1', $choice->sanitize('4', $this->sanitizing));
        $this->assertSame('1', $choice->sanitize(null, $this->sanitizing));
    }

    #[Test]
    public function aChoiceWithoutDefaultIsNotStored(): void
    {
        $choice = new Choice('banner', ['1', '2']);

        $this->assertNull($choice->default());
        $this->assertNull($choice->sanitize('3', $this->sanitizing));
    }

    #[Test]
    public function aChoiceHasValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Choice('banner', []);
    }

    #[Test]
    public function theDefaultOfAChoiceIsOneOfItsValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Choice('banner', ['1', '2'], '3');
    }

    #[Test]
    public function aTextIsSanitizedAsText(): void
    {
        $text = new Text('url');

        $this->assertSame(['type' => 'string'], $text->jsonSchema());
        $this->assertNull($text->default());
        $this->assertSame('text(https://example.com)', $text->sanitize('https://example.com', $this->sanitizing));
        $this->assertSame('text()', $text->sanitize('', $this->sanitizing));
        $this->assertSame('text(42)', $text->sanitize(42, $this->sanitizing));
        $this->assertNull($text->sanitize(null, $this->sanitizing));
        $this->assertSame('text()', $text->sanitize(['an', 'array'], $this->sanitizing));
    }

    #[Test]
    public function anEmptyTextGetsItsDefault(): void
    {
        $text = new Text('banner_bg', '#fff');

        $this->assertSame('#fff', $text->default());
        $this->assertSame('#fff', $text->sanitize('', $this->sanitizing));
        $this->assertSame('#fff', $text->sanitize(null, $this->sanitizing));
        $this->assertSame('#fff', $text->sanitize('   ', $this->sanitizing));
        $this->assertSame('text(#000)', $text->sanitize('#000', $this->sanitizing));
    }

    #[Test]
    public function aRequiredTextIsNotEmpty(): void
    {
        $text = new Text('cookie_name', 'displayCookieConsent', required: true);

        $this->assertSame(['type' => 'string', 'minLength' => 1], $text->jsonSchema());
        $this->assertSame('displayCookieConsent', $text->sanitize('', $this->sanitizing));
    }

    #[Test]
    public function aRequiredTextHasADefault(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Text('cookie_name', required: true);
    }

    #[Test]
    public function anHtmlIsSanitizedAsHtml(): void
    {
        $html = new Html('text');

        $this->assertSame(['type' => 'string'], $html->jsonSchema());
        $this->assertNull($html->default());
        $this->assertSame('html(<p>Hi</p>)', $html->sanitize('<p>Hi</p>', $this->sanitizing));
        $this->assertNull($html->sanitize(null, $this->sanitizing));
        $this->assertSame('html(7)', $html->sanitize(7, $this->sanitizing));
    }

    #[Test]
    public function aPatternKeepsOnlyTheMatchingText(): void
    {
        $pattern = new Pattern('ga_measurement_id', '^(G-[A-Z0-9]+)?$');

        $this->assertSame(['type' => 'string', 'pattern' => '^(G-[A-Z0-9]+)?$'], $pattern->jsonSchema());
        $this->assertNull($pattern->default());
        $this->assertSame('G-ABC123', $pattern->sanitize(' G-ABC123 ', new PassThroughSanitizing()));
        $this->assertSame('', $pattern->sanitize('UA-1', new PassThroughSanitizing()));
        $this->assertNull($pattern->sanitize(null, new PassThroughSanitizing()));
    }

    #[Test]
    public function aPatternIsSearchedLikeTheJsonSchemaOfTheRestApi(): void
    {
        $pattern = new Pattern('slug', '[a-z]+');

        $this->assertSame('1abc', $pattern->sanitize('1abc', new PassThroughSanitizing()));
        $this->assertSame('', $pattern->sanitize('123', new PassThroughSanitizing()));
    }

    #[Test]
    public function aPatternWithTheHashIsMatched(): void
    {
        $pattern = new Pattern('color', '^#[0-9a-f]{3}$');

        $this->assertSame('#fff', $pattern->sanitize('#fff', new PassThroughSanitizing()));
    }

    #[Test]
    public function aPatternThatDoesNotMatchGetsItsDefault(): void
    {
        $pattern = new Pattern('ga_measurement_id', '^G-[A-Z0-9]+$', 'G-DEFAULT');

        $this->assertSame('G-DEFAULT', $pattern->default());
        $this->assertSame('G-DEFAULT', $pattern->sanitize('UA-1', new PassThroughSanitizing()));
        $this->assertSame('G-DEFAULT', $pattern->sanitize(null, new PassThroughSanitizing()));
    }

    #[Test]
    public function aPatternWithSlashesIsMatched(): void
    {
        $pattern = new Pattern('path', '^(/[A-Za-z0-9._~-]+)*$');

        $this->assertSame('/metrics/v2', $pattern->sanitize('/metrics/v2', new PassThroughSanitizing()));
        $this->assertSame('', $pattern->sanitize('/bad path', new PassThroughSanitizing()));
    }

    #[Test]
    public function aPatternIsAValidExpression(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Pattern('path', '(unclosed');
    }

    #[Test]
    public function aCustomPropertyHasItsSchemaAndItsSanitizing(): void
    {
        $property = new CustomProperty(
            'ids',
            ['type' => 'array', 'items' => ['type' => 'string']],
            static fn (mixed $value, Sanitizing $sanitizing): array => \array_map(
                $sanitizing->key(...),
                \is_array($value) ? $value : []
            ),
            [],
        );

        $this->assertSame('ids', $property->name());
        $this->assertSame(['type' => 'array', 'items' => ['type' => 'string']], $property->jsonSchema());
        $this->assertSame([], $property->default());
        $this->assertSame(['key(A)', 'key(b)'], $property->sanitize(['A', 'b'], $this->sanitizing));
    }

    #[Test]
    public function aPropertyHasAName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Text(' ');
    }
}
