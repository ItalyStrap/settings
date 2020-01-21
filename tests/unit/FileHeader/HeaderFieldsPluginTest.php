<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\FileHeader\Plugin;

require_once 'HeaderFieldsBase.php';
/**
 * @link https://codex.wordpress.org/File_Header
 * Class PluginDataTest
 * @package ItalyStrap\Tests
 */
class HeaderFieldsPluginTest extends HeaderFieldsBase {

	protected function values(): array {
		return [
			'Name'			=> 'Settings',
			'Description'	=> 'Settings API for WordPress',
			'PluginURI'		=> 'https://italystrap.com',
			'Author'		=> 'Enea Overclokk',
			'AuthorURI'		=> 'https://italystrap.com',
			'Version'		=> '1.0.0',
			'License'		=> 'GPL2',
			'TextDomain'	=> 'ItalyStrap',
			'DomainPath'	=> 'languages',
			'Network'		=> 'True',
			'RequiresWP'	=> '5.3',
			'RequiresPHP'	=> '7.2',
			'LicenseURI'	=> 'https://opensource.org/licenses/MIT',
		];
	}

	protected function headers(): array {
		return Plugin::HEADERS;
	}

	protected function file(): string {
		return 'fixtures/file-header/plugin.php';
	}

	/**
	 * @test
	 */
//	public function itShouldRenderTextDomainFromDocBlock() {
//
//		$this->file->fread( Argument::type('integer') )->willReturn(
//			\file_get_contents( codecept_data_dir( 'fixtures/file-header/plugin-with-docblock.php' ) )
//		);
//
//		$sut = $this->getIntance();
//		$this->assertStringContainsString( 'ItalyStrap', $sut->textDomain(), '' );
//	}
}
