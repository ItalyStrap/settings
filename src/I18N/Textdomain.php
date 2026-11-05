<?php

declare(strict_types=1);

namespace ItalyStrap\I18N;

/**
 * Class Textdomain
 * @package ItalyStrap\I18N
 */
class Textdomain
{
    /**
     * Constructor
     *
     * @param $path
     */
    public function __construct(private readonly Translator $translator)
    {
    }

    public function isLoaded(): bool
    {
        return \is_textdomain_loaded($this->translator->getDomain());
    }
}
