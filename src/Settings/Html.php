<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * A text with the tags allowed in a post.
 */
final readonly class Html implements Property
{
    private PropertyName $name;

    public function __construct(string $name)
    {
        $this->name = new PropertyName($name);
    }

    public function name(): string
    {
        return $this->name->value();
    }

    public function jsonSchema(): array
    {
        return ['type' => 'string'];
    }

    public function default(): null
    {
        return null;
    }

    public function sanitize(mixed $value, Sanitizing $sanitizing): ?string
    {
        return $value === null ? null : $sanitizing->html((new Scalar($value))->toString());
    }
}
