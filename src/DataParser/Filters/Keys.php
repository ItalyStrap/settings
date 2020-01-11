<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

/**
 * Interface FiltersKey
 * @package ItalyStrap\DataParser
 */
interface Keys {
	const CAPABILITY = CapabilityFilter::KEY;
	const REQUIRED = RequiredFilter::KEY;
	const SANITIZE = SanitizeFilter::KEY;
	const THEME_MOD = ThemeModFilter::KEY;
	const TRANSLATE = TranslateFilter::KEY;
	const VALIDATE = ValidateFilter::KEY;
}
