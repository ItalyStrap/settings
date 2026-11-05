<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Tests\Integration\Option;

use ItalyStrap\Settings\Core\CoreOptionStorage;
use lucatume\WPBrowser\TestCase\WPTestCase;
use PHPUnit\Framework\Attributes\Test;

final class CoreOptionStorageTest extends WPTestCase
{
    private const OPTION = 'italystrap_settings_test';

    #[Test]
    public function aMissingOptionIsNull(): void
    {
        $this->assertNull((new CoreOptionStorage())->get(self::OPTION));
    }

    #[Test]
    public function itAddsReadsAndUpdatesTheOption(): void
    {
        $storage = new CoreOptionStorage();

        $storage->add(self::OPTION, ['active' => '1']);
        $this->assertSame(['active' => '1'], $storage->get(self::OPTION));
        $this->assertSame(['active' => '1'], \get_option(self::OPTION));

        $storage->update(self::OPTION, ['active' => '1', 'color' => '#000']);
        $this->assertSame(['active' => '1', 'color' => '#000'], \get_option(self::OPTION));
    }

    #[Test]
    public function aStoredFalseIsNotMissing(): void
    {
        \add_option(self::OPTION, '');

        $this->assertSame('', (new CoreOptionStorage())->get(self::OPTION));
    }
}
