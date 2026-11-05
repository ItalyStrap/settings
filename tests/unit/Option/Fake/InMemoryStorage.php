<?php

declare(strict_types=1);

namespace ItalyStrap\Settings\Tests\Unit\Option\Fake;

use ItalyStrap\Settings\OptionStorage;

/**
 * The options in an array, the calls recorded in order.
 */
final class InMemoryStorage implements OptionStorage
{
    /** @var list<array{string, string, mixed}> */
    public array $writes = [];

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public array $options = [],
    ) {
    }

    public function get(string $name): mixed
    {
        return $this->options[$name] ?? null;
    }

    public function add(string $name, mixed $value): void
    {
        $this->writes[] = ['add', $name, $value];
        $this->options[$name] = $value;
    }

    public function update(string $name, mixed $value): void
    {
        $this->writes[] = ['update', $name, $value];
        $this->options[$name] = $value;
    }
}
