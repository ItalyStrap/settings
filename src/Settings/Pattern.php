<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

use InvalidArgumentException;

/**
 * A text that matches a regular expression, the default or the empty text when it does not.
 *
 * The expression is searched in the text, not anchored, like the pattern of the JSON schema in the REST API.
 */
final readonly class Pattern implements Property
{
    private PropertyName $name;

    public function __construct(
        string $name,
        private string $pattern,
        private ?string $default = null,
    ) {
        $this->name = new PropertyName($name);
        if (@\preg_match($this->expression(), '') === false) {
            throw new InvalidArgumentException(\sprintf('The pattern of "%s" is not valid', $this->name()));
        }
    }

    public function name(): string
    {
        return $this->name->value();
    }

    public function jsonSchema(): array
    {
        return ['type' => 'string', 'pattern' => $this->pattern];
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

        $text = $sanitizing->text((new Scalar($value))->toString());

        return \preg_match($this->expression(), $text) === 1 ? $text : $this->default ?? '';
    }

    /**
     * The delimiter of the REST API, rest_validate_value_from_schema().
     */
    private function expression(): string
    {
        return '#' . \str_replace('#', '\\#', $this->pattern) . '#u';
    }
}
