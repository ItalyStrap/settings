<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\DataParser\ParserInterface;
use ItalyStrap\Fields\FieldsInterface;
use ItalyStrap\Settings\Options;
use ItalyStrap\Settings\PageInterface;
use ItalyStrap\Settings\Sections;
use ItalyStrap\Settings\SectionsInterface;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;

class SectionsTest extends WPTestCase {

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
		$this->assertInstanceOf( \Countable::class, $sut, '' );
		return $sut;
	}

	/**
	 * @test
	 */
	public function itShouldBeInstantiable() {
		$this->options->get()->willReturn( [] );
		$sut = $this->getInstance();
	}

	/**
	 * @test
	 */
	public function itShouldRenderPageSlug() {
		$this->options->get()->willReturn( [] );
		$sut = $this->getInstance();

		$this->page->getSlug()->willReturn( 'slug' );
		$sut->forPage( $this->getPage() );

		$this->assertStringContainsString( 'slug', $sut->getPageSlug(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnArrayOfSectionsConfig() {
		$this->options->get()->willReturn( [] );
		$sut = $this->getInstance( $this->sections_config );

		$this->assertEquals( $this->sections_config, $sut->getSections(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldBeCountable() {
		$this->options->get()->willReturn( [] );
		$sut = $this->getInstance( $this->sections_config );

		$this->assertCount( \count( $this->sections_config ), $sut, '' );
	}

	/**
	 * @test
	 */
	public function itShouldRenderSectionCallback() {
		$this->options->get()->willReturn( [] );
		$sut = $this->getInstance( $this->sections_config );

		$section = [
			'id'		=> '',
			'title'		=> '',
			'callback'	=> null,
		];


		$sut->renderSection( $section );
//		codecept_debug( $this->getActualOutputForAssertion() );
	}

	/**
	 * @test
	 */
	public function itShouldSetValuesInIdNameAndValueAndRenderField() {

		$option_name = 'option-name';
		$this->options->getName()->willReturn( $option_name );
		$this->options->get()->willReturn( [] );

		$field = [
			'callback'	=> null,
			'value'		=> 'the value is always set',
			'id'		=> 'some-unique-id',
		];

		$html_returned_from_fake_fields_render = '<fake_html>';

		$this->fields->render(Argument::type('array'))->will(
			function ( array $args ) use ( $field, $option_name, $html_returned_from_fake_fields_render ) {

				Assert::assertEquals( $field['value'], $args[0]['value'], '' );

				$name_string = \sprintf(
					'%s[%s]',
					$option_name,
					$field['id']
				);

				Assert::assertEquals( $name_string, $args[0]['id'], '' );
				Assert::assertEquals( $name_string, $args[0]['name'], '' );
				return $html_returned_from_fake_fields_render;
			}
		);

		$sut = $this->getInstance( $this->sections_config );

		\ob_start();
		$sut->renderField( $field );
		$content = \ob_get_clean();

		$this->assertStringContainsString( $html_returned_from_fake_fields_render, $content, '' );
	}

	/**
	 * @test
	 */
	public function itShouldBeClassSetted() {

		$option_name = 'option-name';
		$this->options->getName()->willReturn( $option_name );
		$this->options->get()->willReturn( [] );

		$field = [
			'callback'	=> null,
			'value'		=> 'the value is always set',
			'id'		=> 'some-unique-id',
		];

		$this->fields->render(Argument::type('array'))->will(
			function ( array $args ) {
				Assert::assertTrue( isset( $args[0]['class'] ), '' );
				return '';
			}
		);

		$sut = $this->getInstance();
		$sut->renderField( $field );
	}

	/**
	 * @test
	 */
	public function itShouldExecutedCallable() {

		$option_name = 'option-name';
		$this->options->getName()->willReturn( $option_name );
		$this->options->get()->willReturn( [] );

		$returned_value_for_callback = 'Some random value';

		$field = [
			'callback'	=> function ( array $args ) use ( $returned_value_for_callback ) {
				Assert::assertStringContainsString( 'the value is always set', $args['value'], '' );
				Assert::assertStringContainsString( 'some-unique-id', $args['id'], '' );
				return $returned_value_for_callback;
			},
			'value'		=> 'the value is always set',
			'id'		=> 'some-unique-id',
		];

		$sut = $this->getInstance();
		$sut->renderField( $field );
		$this->assertStringContainsString( $this->getActualOutputForAssertion(), $returned_value_for_callback, '' );
	}

	/**
	 * @test
	 */
	public function itShouldRegister() {
		global $wp_settings_sections, $wp_settings_fields;
		$this->options->getName()->willReturn( 'option-name' );
		$this->options->get()->willReturn( [] );

		$sut = $this->getInstance( $this->sections_config );

		$page_slug = 'slug';
		$this->page->getSlug()->willReturn( $page_slug );
		$sut->forPage( $this->getPage() );

		$sut->register();

		foreach ( $wp_settings_sections as $page => $settings_sections ) {
			$this->assertStringContainsString( $page_slug, $page, '' );

			foreach ( $this->sections_config as $section ) {
				$section_id = $section['id'];
				$this->assertArrayHasKey( $section_id, $settings_sections, '' );
				$this->assertStringContainsString(
					$section['id'],
					$settings_sections[ $section_id ]['id'],
					''
				);
				$this->assertStringContainsString(
					$section['title'],
					$settings_sections[ $section_id ]['title'],
					''
				);
			}
		}

		foreach ( $wp_settings_fields as $page => $settings_fields ) {
			$this->assertStringContainsString( $page_slug, $page, '' );

			foreach ( $this->sections_config as $section ) {
				$section_id = $section['id'];
				$this->assertArrayHasKey( $section_id, $settings_fields, '' );

				foreach ( $section['fields'] as $config_field ) {
					$field_id = $config_field['id'];

					$this->assertStringContainsString(
						$config_field['id'],
						$settings_fields[ $section_id ][ $field_id ]['id'],
						''
					);
					$this->assertStringContainsString(
						$config_field['label'],
						$settings_fields[ $section_id ][ $field_id ]['title'],
						''
					);
				}
			}
		}
	}
}
