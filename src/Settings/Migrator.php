<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

use InvalidArgumentException;

/**
 * Brings the stored option to the version of its schema, the version is stored in its own option.
 *
 * A new installation gets the defaults. An option without a version, stored before the first migration,
 * is at version 1. The option is written only when a step changes it.
 */
final readonly class Migrator
{
    /** @var non-empty-string */
    private string $versionOption;

    /** @var array<int, MigrationStep> */
    private array $steps;

    public function __construct(
        private Schema $schema,
        private OptionStorage $storage,
        string $versionOption,
        MigrationStep ...$steps,
    ) {
        $this->versionOption = (new PropertyName($versionOption))->value();
        $this->steps = $this->ordered($steps);
    }

    public function migrate(): void
    {
        $storedVersion = $this->storedVersion();
        if ($storedVersion >= $this->schema->version()) {
            return;
        }

        $this->migrateTheOption($storedVersion);
        $this->storage->update($this->versionOption, $this->schema->version());
    }

    /**
     * The option brought from a version to the one of the schema, like the settings of an import.
     *
     * @param array<array-key, mixed> $option
     * @return array<array-key, mixed>
     */
    public function upgrade(array $option, int $fromVersion): array
    {
        foreach ($this->steps as $version => $step) {
            if ($version <= $fromVersion) {
                continue;
            }

            $option = $step($option);
        }

        return $option;
    }

    private function migrateTheOption(int $storedVersion): void
    {
        $option = $this->storage->get($this->schema->name());
        if ($option === null && $storedVersion < 1) {
            $this->storage->add($this->schema->name(), $this->schema->defaults());
            return;
        }

        if (!\is_array($option)) {
            return;
        }

        // The steps start at version 2, an option without a version runs all of them
        $migrated = $this->upgrade($option, $storedVersion);
        if ($migrated === $option) {
            return;
        }

        $this->storage->update($this->schema->name(), $migrated);
    }

    private function storedVersion(): int
    {
        $version = $this->storage->get($this->versionOption);

        return \is_numeric($version) ? (int) $version : 0;
    }

    /**
     * @param array<array-key, MigrationStep> $steps
     * @return array<int, MigrationStep>
     */
    private function ordered(array $steps): array
    {
        $ordered = [];
        foreach ($steps as $step) {
            $this->assertVersion($step->version(), $ordered);
            $ordered[$step->version()] = $step;
        }

        \ksort($ordered);

        return $ordered;
    }

    /**
     * @param array<int, MigrationStep> $ordered
     */
    private function assertVersion(int $version, array $ordered): void
    {
        if ($version < 2 || $version > $this->schema->version()) {
            throw new InvalidArgumentException(\sprintf(
                'The migration step %d of "%s" must be between 2 and %d',
                $version,
                $this->schema->name(),
                $this->schema->version()
            ));
        }

        if (\array_key_exists($version, $ordered)) {
            throw new InvalidArgumentException(\sprintf(
                'The migration step %d of "%s" is already registered',
                $version,
                $this->schema->name()
            ));
        }
    }
}
