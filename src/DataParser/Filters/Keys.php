<?php

declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

/**
 * Interface FiltersKey
 * @package ItalyStrap\DataParser
 */
interface Keys
{
    public const CAPABILITY = CapabilityFilter::KEY;
    public const REQUIRED = RequiredFilter::KEY;
    public const SANITIZE = SanitizeFilter::KEY;
    public const THEME_MOD = ThemeModFilter::KEY;
    public const TRANSLATE = TranslateFilter::KEY;
    public const VALIDATE = ValidateFilter::KEY;
}
