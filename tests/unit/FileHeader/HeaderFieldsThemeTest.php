<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;


use ItalyStrap\FileHeader\Theme;

require_once 'HeaderFieldsBase.php';

class HeaderFieldsThemeTest extends HeaderFieldsBase {

	protected function values(): array {
		return [
			'Name'			=> 'MyTheme',
			'Description'	=> 'Theme description',
			'ThemeURI'		=> 'https://italystrap.com',
			'Author'		=> 'Enea Overclokk',
			'AuthorURI'		=> 'https://italystrap.com',
			'Version'		=> '1.0',
			'License'		=> 'GNU General Public License v2 or later',
			'TextDomain'	=> 'ItalyStrap',
			'DomainPath'	=> 'languages',
			'LicenseURI'	=> 'http://www.gnu.org/licenses/gpl-2.0.html',
			'Tags'			=> 'black, brown',
			'Template'		=> 'ItalyStrap',
		];
	}

	protected function headers(): array {
		return Theme::HEADERS;
	}

	protected function file(): string {
		return 'fixtures/file-header/styles.css';
	}
}
