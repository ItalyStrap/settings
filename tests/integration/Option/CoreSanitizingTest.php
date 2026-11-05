<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Tests\Integration\Option;

use ItalyStrap\Settings\Core\CoreSanitizing;
use lucatume\WPBrowser\TestCase\WPTestCase;
use PHPUnit\Framework\Attributes\Test;

final class CoreSanitizingTest extends WPTestCase
{
    #[Test]
    public function aTextHasNoTagsAndNoNewLines(): void
    {
        $this->assertSame('Hi there', (new CoreSanitizing())->text("<b>Hi</b>\n there "));
    }

    #[Test]
    public function anHtmlKeepsTheTagsOfAPost(): void
    {
        $this->assertSame('<p>Hi</p>alert(1)', (new CoreSanitizing())->html('<p>Hi</p><script>alert(1)</script>'));
    }

    #[Test]
    public function aKeyIsLowercaseWithoutSpaces(): void
    {
        $this->assertSame('my-key_1', (new CoreSanitizing())->key('My-Key_1 !'));
    }
}
