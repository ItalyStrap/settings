<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Tests\Unit\Option;

use Codeception\Test\Unit;
use InvalidArgumentException;
use ItalyStrap\Settings\Checkbox;
use ItalyStrap\Settings\MigrationStep;
use ItalyStrap\Settings\Migrator;
use ItalyStrap\Settings\Schema;
use ItalyStrap\Settings\Tests\Unit\Option\Fake\InMemoryStorage;
use ItalyStrap\Settings\Text;
use PHPUnit\Framework\Attributes\Test;

final class MigratorTest extends Unit
{
    private const OPTION = 'my_plugin';
    private const VERSION = 'my_plugin_version';

    #[Test]
    public function aNewInstallationGetsTheDefaultsAndTheVersion(): void
    {
        $storage = new InMemoryStorage();

        $this->migrator($storage)->migrate();

        $this->assertSame([
            ['add', self::OPTION, ['color' => '#fff', 'consent_log' => '1']],
            ['update', self::VERSION, 3],
        ], $storage->writes);
    }

    #[Test]
    public function anOptionWithoutVersionRunsTheStepsAfterTheFirst(): void
    {
        $storage = new InMemoryStorage([self::OPTION => ['old_close' => 'Close', 'color' => '#000']]);

        $this->migrator($storage)->migrate();

        $this->assertSame(['color' => '#000', 'consent_log' => '1'], $storage->options[self::OPTION]);
        $this->assertSame(3, $storage->options[self::VERSION]);
    }

    #[Test]
    public function anOptionRunsOnlyTheStepsAfterItsVersion(): void
    {
        $storage = new InMemoryStorage([self::OPTION => ['old_close' => 'Close'], self::VERSION => '2']);

        $this->migrator($storage)->migrate();

        $this->assertSame(['old_close' => 'Close', 'consent_log' => '1'], $storage->options[self::OPTION]);
    }

    #[Test]
    public function nothingRunsWhenTheVersionIsCurrent(): void
    {
        $storage = new InMemoryStorage([self::OPTION => ['old_close' => 'Close'], self::VERSION => 3]);

        $this->migrator($storage)->migrate();

        $this->assertSame([], $storage->writes);
    }

    #[Test]
    public function anOptionThatDoesNotChangeIsNotWritten(): void
    {
        $storage = new InMemoryStorage([self::OPTION => ['consent_log' => '1'], self::VERSION => 2]);

        $this->migrator($storage)->migrate();

        $this->assertSame([['update', self::VERSION, 3]], $storage->writes);
    }

    /**
     * Two first requests: another one stored the defaults after this one read the version 0.
     *
     * @see https://github.com/ItalyCookieChoices/italy-cookie-choices/issues/300
     */
    #[Test]
    public function theDefaultsStoredByAnotherRequestAreNotChanged(): void
    {
        $storage = new InMemoryStorage([self::OPTION => ['color' => '#fff', 'consent_log' => '1']]);

        $this->migrator($storage)->migrate();

        $this->assertSame([['update', self::VERSION, 3]], $storage->writes);
    }

    #[Test]
    public function aBrokenOptionIsLeftAsItIs(): void
    {
        $storage = new InMemoryStorage([self::OPTION => 'broken', self::VERSION => 1]);

        $this->migrator($storage)->migrate();

        $this->assertSame([['update', self::VERSION, 3]], $storage->writes);
    }

    #[Test]
    public function aMissingOptionWithAVersionIsNotCreated(): void
    {
        $storage = new InMemoryStorage([self::VERSION => 1]);

        $this->migrator($storage)->migrate();

        $this->assertSame([['update', self::VERSION, 3]], $storage->writes);
    }

    #[Test]
    public function anInvalidStoredVersionRunsAllTheSteps(): void
    {
        $storage = new InMemoryStorage([self::OPTION => ['old_close' => 'Close'], self::VERSION => 'x']);

        $this->migrator($storage)->migrate();

        $this->assertSame(['consent_log' => '1'], $storage->options[self::OPTION]);
    }

    #[Test]
    public function theUpgradeRunsTheStepsAfterTheGivenVersion(): void
    {
        $migrator = $this->migrator(new InMemoryStorage());

        $this->assertSame(
            ['old_close' => 'Close', 'consent_log' => '1'],
            $migrator->upgrade(['old_close' => 'Close'], 2)
        );
        $this->assertSame(['consent_log' => '1'], $migrator->upgrade(['old_close' => 'Close'], 1));
        $this->assertSame(['old_close' => 'Close'], $migrator->upgrade(['old_close' => 'Close'], 3));
    }

    #[Test]
    public function theStepsRunInTheOrderOfTheirVersion(): void
    {
        $storage = new InMemoryStorage([self::OPTION => ['history' => '']]);
        $append = fn (int $version): MigrationStep => $this->step(
            $version,
            static fn (array $option): array => ['history' => $option['history'] . $version]
        );

        (new Migrator(new Schema(self::OPTION, 4), $storage, self::VERSION, $append(4), $append(2), $append(3)))
            ->migrate();

        $this->assertSame(['history' => '234'], $storage->options[self::OPTION]);
    }

    #[Test]
    public function aStepIsNotAfterTheVersionOfTheSchema(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Migrator(new Schema(self::OPTION, 2), new InMemoryStorage(), self::VERSION, $this->turnOnTheLog());
    }

    #[Test]
    public function aStepIsAfterTheFirstVersion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $first = $this->step(1, static fn (array $option): array => $option);

        new Migrator(new Schema(self::OPTION, 2), new InMemoryStorage(), self::VERSION, $first);
    }

    #[Test]
    public function twoStepsHaveNotTheSameVersion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $step = $this->removeTheClose();

        new Migrator(new Schema(self::OPTION, 3), new InMemoryStorage(), self::VERSION, $step, $step);
    }

    #[Test]
    public function theVersionOptionHasAName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Migrator(new Schema(self::OPTION, 1), new InMemoryStorage(), '');
    }

    private function migrator(InMemoryStorage $storage): Migrator
    {
        // The defaults are at the current version, the last step adds nothing to them
        $schema = new Schema(
            self::OPTION,
            3,
            new Checkbox('active'),
            new Text('color', '#fff'),
            new Checkbox('consent_log', true),
        );

        return new Migrator($schema, $storage, self::VERSION, $this->removeTheClose(), $this->turnOnTheLog());
    }

    private function removeTheClose(): MigrationStep
    {
        return $this->step(2, static function (array $option): array {
            unset($option['old_close']);

            return $option;
        });
    }

    private function turnOnTheLog(): MigrationStep
    {
        return $this->step(3, static fn (array $option): array => $option + ['consent_log' => '1']);
    }

    /**
     * @param callable(array<array-key, mixed>): array<array-key, mixed> $step
     */
    private function step(int $version, callable $step): MigrationStep
    {
        return new class ($version, $step) implements MigrationStep {
            /** @var callable(array<array-key, mixed>): array<array-key, mixed> */
            private $step;

            /**
             * @param callable(array<array-key, mixed>): array<array-key, mixed> $step
             */
            public function __construct(private readonly int $version, callable $step)
            {
                $this->step = $step;
            }

            public function version(): int
            {
                return $this->version;
            }

            public function __invoke(array $option): array
            {
                return ($this->step)($option);
            }
        };
    }
}
