<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\DataParser\Parser;
use ItalyStrap\DataParser\ParserInterface;
use ItalyStrap\Fields\Fields;
use ItalyStrap\Fields\FieldsInterface;
use ItalyStrap\Settings\Options;
use ItalyStrap\Settings\Page;
use ItalyStrap\Settings\PageInterface;
use ItalyStrap\Settings\Sections;
use ItalyStrap\Settings\SectionsInterface;
use function Codeception\Extension\codecept_log;

class SectionsTest extends \Codeception\TestCase\WPTestCase {

	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $fields;
	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $page;
	/**
	 * @var array
	 */
	private $sections_config;

	/**
	 * @return Page
	 */
	public function getPage() {
		return $this->page->reveal();
	}

	private function getFields() {
		return $this->fields->reveal();
	}
	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $parser;

	public function getParser() {
		return $this->parser->reveal();
	}

	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $options;

	public function getOptions() {
		return $this->options->reveal();
	}

	public function setUp(): void {
		// Before...
		parent::setUp();

		$this->fields =  $this->prophesize( FieldsInterface::class );
		$this->parser = $this->prophesize( ParserInterface::class );
		$this->options = $this->prophesize( Options::class );
		$this->page = $this->prophesize( PageInterface::class );

		$this->sections_config = require \codecept_data_dir( '/fixtures/config/sections.php' );

		global $wp_settings_sections, $wp_settings_fields;
		// Your set up methods here.
	}

	public function tearDown(): void {
		// Your tear down methods here.

		global $wp_settings_sections, $wp_settings_fields;
		$wp_settings_sections = [];
		$wp_settings_fields = [];
		// Then...
		parent::tearDown();
	}

	private function getInstance( array $config = [] ): Sections {
		$config = ConfigFactory::make( $config );

		$sut = new Sections(
			$config,
			$this->getFields(),
			$this->getParser(),
			$this->getOptions()
		);

		$this->assertInstanceOf( SectionsInterface::class, $sut, '' );
		$this->assertInstanceOf( Sections::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$sut = $this->getInstance();
	}

	/**
	 * @test
	 */
	public function itShouldRenderPageSlug() {
		$sut = $this->getInstance();
		$this->page->getSlug()->willReturn( 'slug' );
		$sut->forPage( $this->getPage() );

		$this->assertStringContainsString( 'slug', $sut->getPageSlug(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnArrayOfSectionsConfig() {
		$sut = $this->getInstance( $this->sections_config );

		$this->assertEquals( $this->sections_config, $sut->getSections(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldBeCountable() {
		$sut = $this->getInstance( $this->sections_config );

		$this->assertCount( \count( $this->sections_config ), $sut, '' );
	}

	/**
	 * @test
	 */
	public function itShouldRegister() {
		global $wp_settings_sections, $wp_settings_fields;
		$this->options->getName()->willReturn( 'option-name' );
		$this->fields->render()->willReturn( true );

		$sut = $this->getInstance( $this->sections_config );

		$page_slug = 'slug';
		$this->page->getSlug()->willReturn( $page_slug );
		$sut->forPage( $this->getPage() );

		$sut->register();

		foreach ( $wp_settings_sections as $page => $sections ) {
			$this->assertStringContainsString( $page_slug, $page, '' );

			foreach ( $this->sections_config as $section ) {
				$id = $section['id'];
				$this->assertArrayHasKey( $id, $sections, '' );
				$this->assertStringContainsString( $section['id'], $sections[ $id ]['id'], '' );
				$this->assertStringContainsString( $section['title'], $sections[ $id ]['title'], '' );
			}
		}

		foreach ( $wp_settings_fields as $page => $fields ) {
			$this->assertStringContainsString( $page_slug, $page, '' );

			foreach ( $this->sections_config as $section ) {
				$id = $section['id'];
				$this->assertArrayHasKey( $id, $fields, '' );

				foreach ( $section['fields'] as $field ) {
					codecept_debug( $field );
					$field_id = $field['id'];

					unset( $fields[ $id ][ $field_id ]['callback'] );

					codecept_debug( $fields[ $id ][ $field_id ] );

					$this->assertStringContainsString( $field['id'], $fields[ $id ][ $field_id ]['id'], '' );
					$this->assertStringContainsString( $field['label'], $fields[ $id ][ $field_id ]['title'], '' );
				}
			}
		}
	}
}
