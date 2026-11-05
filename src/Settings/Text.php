<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

use InvalidArgumentException;

/**
 * A text on one line, the default when it is empty.
 */
final readonly class Text implements Property
{
    private PropertyName $name;

    /**
     * @param string|null $default The value of an empty text, null to store the empty text.
     * @param bool $required The text is never empty, it needs a default.
     */
    public function __construct(
        string $name,
        private ?string $default = null,
        private bool $required = false,
    ) {
        $this->name = new PropertyName($name);
        if ($required && ($default === null || $default === '')) {
            throw new InvalidArgumentException(\sprintf('The required text "%s" must have a default', $this->name()));
        }
    }

    public function name(): string
    {
        return $this->name->value();
    }

    public function jsonSchema(): array
    {
        return $this->required ? ['type' => 'string', 'minLength' => 1] : ['type' => 'string'];
    }

    public function default(): ?string
    {
        return $this->default;
    }

    public function sanitize(mixed $value, Sanitizing $sanitizing): ?string
    {
        if ($value === null) {
            return $this->default;
        }

        $text = (new Scalar($value))->toString();
        if ($this->default !== null && \trim($text) === '') {
            return $this->default;
        }

        return $sanitizing->text($text);
    }
}
