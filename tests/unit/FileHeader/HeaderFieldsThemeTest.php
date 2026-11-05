<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\FileHeader\Theme;

// phpcs:disable
require_once __DIR__ . '/HeaderFieldsBase.php';
// phpcs:enable

class HeaderFieldsThemeTest extends HeaderFieldsBase
{
    protected static function values(): array
    {
        return [
            'Name'          => 'MyTheme',
            'Description'   => 'Theme description',
            'ThemeURI'      => 'https://italystrap.com',
            'Author'        => 'Enea Overclokk',
            'AuthorURI'     => 'https://italystrap.com',
            'Version'       => '1.0',
            'License'       => 'GNU General Public License v2 or later',
            'TextDomain'    => 'ItalyStrap',
            'DomainPath'    => 'languages',
            'LicenseURI'    => 'http://www.gnu.org/licenses/gpl-2.0.html',
            'Tags'          => 'black, brown',
            'Template'      => 'ItalyStrap',
        ];
    }

    protected static function headers(): array
    {
        return Theme::HEADERS;
    }

    protected static function file(): string
    {
        return 'fixtures/file-header/styles.css';
    }
}
