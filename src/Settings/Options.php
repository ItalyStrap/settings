<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\Config;

/**
 * Class Options
 * @package ItalyStrap\Settings
 */
class Options extends Config implements OptionsInterface
{
    public function __construct(private readonly string $name, $default = [])
    {
        parent::__construct((array) \get_option($this->name, $default), $default);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function addAll(array $values = [])
    {
        return \add_option($this->name, $values);
    }

    /**
     * @inheritDoc
     */
    public function removeAll()
    {
        return \delete_option($this->name);
    }
}
