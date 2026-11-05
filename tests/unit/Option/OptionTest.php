<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Tests\Unit\Option;

use Codeception\Test\Unit;
use ItalyStrap\Settings\Option;
use ItalyStrap\Settings\Schema;
use ItalyStrap\Settings\Tests\Unit\Option\Fake\InMemoryStorage;
use PHPUnit\Framework\Attributes\Test;

final class OptionTest extends Unit
{
    #[Test]
    public function itReadsTheStoredValues(): void
    {
        $storage = new InMemoryStorage(['my_plugin' => ['active' => '1']]);

        $this->assertSame(['active' => '1'], $this->option($storage)->values()->toArray());
    }

    #[Test]
    public function aMissingOrBrokenOptionHasNoValues(): void
    {
        $this->assertSame([], $this->option(new InMemoryStorage())->values()->toArray());
        $this->assertSame([], $this->option(new InMemoryStorage(['my_plugin' => 'broken']))->values()->toArray());
    }

    private function option(InMemoryStorage $storage): Option
    {
        return new Option(new Schema('my_plugin', 1), $storage);
    }
}
