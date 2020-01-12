<?php
declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\TestCase\WPTestCase;
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Config\ConfigInterface;
use ItalyStrap\DataParser\Parser;
use ItalyStrap\DataParser\ParserInterface;
use ItalyStrap\Fields\FieldsInterface;
use ItalyStrap\Settings\Options;
use ItalyStrap\Settings\PageInterface;
use ItalyStrap\Settings\Sections;
use ItalyStrap\Settings\SectionsInterface;
use PHPUnit\Framework\Assert;
use Prophecy\Argument;
use Prophecy\Promise\PromiseInterface;
use Prophecy\Prophecy\MethodProphecy;
use Prophecy\Prophecy\ObjectProphecy;

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
	/**
	 * @var \ItalyStrap\Config\Config
	 */
	private $config;
	/**
	 * @var \Prophecy\Prophecy\ObjectProphecy
	 */
	private $config_fake;

	public function getConfigFake() {
		return $this->config_fake->reveal();
	}

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


		$this->config = ConfigFactory::make();
		$this->config_fake =  $this->prophesize( ConfigInterface::class );
		$this->fields =  $this->prophesize( FieldsInterface::class );
		$this->parser = $this->prophesize( ParserInterface::class );
		$this->options = $this->prophesize( Options::class );
		$this->page = $this->prophesize( PageInterface::class );

		$this->sections_config = require \codecept_data_dir( '/fixtures/config/sections.php' );

		global $wp_settings_sections, $wp_settings_fields, $wp_registered_settings;
		// Your set up methods here.
	}

	public function tearDown(): void {
		// Your tear down methods here.

		global $wp_settings_sections, $wp_settings_fields, $wp_registered_settings;
		$wp_settings_sections = [];
		$wp_settings_fields = [];
		$wp_registered_settings = [];
		// Then...
		parent::tearDown();
	}

	private function getInstance( array $config = [], $fake_config = null ): Sections {

		$config_obj = $this->config;
		$config_obj->merge( $config );

		if ( $fake_config ) {
			$config_obj = $fake_config;
		}

		$sut = new Sections(
			$config_obj,
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
		$this->options->toArray()->willReturn( [] );
		$sut = $this->getInstance();
	}

	/**
	 * @test
	 */
	public function itShouldRenderPageSlug() {
		$this->options->toArray()->willReturn( [] );
		$sut = $this->getInstance();

		$this->page->getSlug()->willReturn( 'slug' );
		$sut->forPage( $this->getPage() );

		$this->assertStringContainsString( 'slug', $sut->getPageSlug(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldReturnArrayOfSectionsConfig() {
		$this->options->toArray()->willReturn( [] );
		$sut = $this->getInstance( $this->sections_config );

		$this->assertEquals( $this->sections_config, $sut->getSections(), '' );
	}

	/**
	 * @test
	 */
	public function itShouldBeCountable() {
		$this->options->toArray()->willReturn( [] );
		$sut = $this->getInstance( $this->sections_config );

		$this->assertCount( \count( $this->sections_config ), $sut, '' );
	}

	/**
	 * @test
	 */
	public function itShouldRenderDescriptionFromCallableInSectionCallback() {
		$this->options->toArray()->willReturn( [] );

		$promise = new class implements PromiseInterface {

			/**
			 * @inheritDoc
			 */
			public function execute( array $args, ObjectProphecy $object, MethodProphecy $method ) {
				return function ( array $section ) {
					return 'This is a callable description.';
				};
			}
		};

		$this->config_fake
			->get( Argument::type('string'), Argument::any() )
			->will( $promise );

		$sut = $this->getInstance( [], $this->getConfigFake() );

		$section = [
			'id'		=> 'unique-id',
			'title'		=> 'Title of the section',
			'callback'	=> null,
		];

		$sut->renderSection( $section );
		$this->assertStringContainsString(
			$this->getActualOutputForAssertion(),
			'This is a callable description.',
			''
		);
	}

	/**
	 * @test
	 */
	public function itShouldRenderDescriptionInSectionCallback() {
		$this->options->toArray()->willReturn( [] );



		$this->config_fake
			->get( Argument::type('string'), Argument::any() )
			->willReturn( '<p>Description.</p>' );

		$sut = $this->getInstance( [], $this->getConfigFake() );

		$section = [
			'id'		=> 'unique-id',
			'title'		=> 'Title of the section',
			'callback'	=> null,
		];

		$sut->renderSection( $section );
		$this->assertStringContainsString(
			$this->getActualOutputForAssertion(),
			'<p>Description.</p>',
			''
		);
	}

	/**
	 * @test
	 */
	public function itShouldSetValuesInIdNameAndValueAndRenderField() {

		$option_name = 'option-name';
		$this->options->getName()->willReturn( $option_name );
		$this->options->toArray()->willReturn( [] );

		$field = [
			'callback'	=> null,
			'value'		=> 'the value is always set',
			'id'		=> 'some-unique-id',
		];

		$this->options->get( $field['id'], $field['value'] )->willReturn( $field['value'] );

		$html_returned_from_fake_fields_render = '<fake_html>';

		$this->fields->render(Argument::type('array'))->will(
			function ( array $args ) use ( $field, $option_name, $html_returned_from_fake_fields_render ) {

				Assert::assertEquals(
					$field['value'],
					$args[0]['value'],
					'This have to return default value in case is not set'
				);

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
		$this->options->toArray()->willReturn( [] );
		$this->options->get( Argument::type('string'), Argument::any() )->willReturn( [] );

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
	public function itShouldRenderField() {

		$option_name = 'option-name';
		$this->options->getName()->willReturn( $option_name );
		$this->options->toArray()->willReturn( [] );
		$this->options->get( Argument::type('string'), Argument::any() )->willReturn( [] );

		$field = [
			'callback'	=> null,
			'value'		=> 'the value is always set',
			'id'		=> 'some-unique-id',
		];

		$this->fields->render(Argument::type('array'))->will(
			function ( array $args ) {
				return '<fake_html>';
			}
		);

		$sut = $this->getInstance();
		$sut->renderField( $field );
		$this->assertStringContainsString(
			$this->getActualOutputForAssertion(),
			'<fake_html>',
			''
		);
	}

	/**
	 * @test
	 */
	public function itShouldRenderFieldMethodExecutedCallableIfDeclared() {

		$option_name = 'option-name';
		$this->options->getName()->willReturn( $option_name );
		$this->options->toArray()->willReturn( [] );

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
		global $wp_settings_sections, $wp_settings_fields, $wp_registered_settings;

		$option_name = 'option-name';
		$this->options->getName()->willReturn( $option_name );
		$this->options->toArray()->willReturn( [] );
		$this->parser->withSchema( Argument::type('array') )->willReturn( new Parser() );
		$this->parser->parseValues( Argument::type('array') )->willReturn([]);

		$sut = $this->getInstance( $this->sections_config );

		$page_slug = 'slug';
		$this->page->getSlug()->willReturn( $page_slug );
		$sut->forPage( $this->getPage() );

		$sut->register();

		$this->assertArrayHasKey( 'sanitize_callback', $wp_registered_settings[ $option_name ], '' );
		$callable = $wp_registered_settings['option-name']['sanitize_callback'];
		$this->assertStringContainsString(
			'parseValues',
			$wp_registered_settings['option-name']['sanitize_callback'][1],
			''
		);
		$this->assertTrue( \is_callable( $callable ), '' );

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
