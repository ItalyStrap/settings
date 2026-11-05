<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * The stored option of a schema.
 */
final readonly class Option
{
    public function __construct(
        private Schema $schema,
        private OptionStorage $storage,
    ) {
    }

    /**
     * Read when they are needed, the option can change during the request.
     */
    public function values(): Values
    {
        $values = $this->storage->get($this->schema->name());

        return new Values(\is_array($values) ? $values : []);
    }
}
