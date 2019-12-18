<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\Config;
use ItalyStrap\Fields\FieldsInterface;

class Sections implements \Countable, SectionsInterface {

	use ShowableTrait;

	const TAB_TITLE = 'tab_title';
	const ID = 'id';
	const TITLE = 'title';
	const DESC = 'desc';
	const FIELDS = 'fields';
	const LABEL_CLASS = 'class_for_label';

	/**
	 * Settings for plugin admin page
	 *
	 * @var Config
	 */
	protected $config;

	/**
	 * The plugin options
	 *
	 * @var array
	 */
	protected $options_values = [];

	/**
	 * The type of fields to create
	 *
	 * @var FieldsInterface
	 */
	protected $fields;

	/**
	 * @var DataParser
	 */
	private $parser;

	/**
	 * @var Options
	 */
	private $options;

	/**
	 * @var array
	 */
	private $field_class = [];
	private $section_key;

	/**
	 * Initialize Class
	 *
	 * @param FieldsInterface $fields The Fields object.
	 * @param DataParser $parser
	 * @param Options $options Get the plugin options.
	 * @param Config $config The configuration array plugin fields.
	 */
	public function __construct(
		Config $config,
		FieldsInterface $fields,
		DataParser $parser,
		Options $options
	) {
		$this->config = $config;

		$this->fields = $fields;
		$this->parser = $parser;

		$this->options = $options;
		$this->options_values = (array) $options->get();
	}

	/**
	 * @inheritDoc
	 */
	public function register() {
		$this->addSettingsSections();
		$this->registerSetting();
	}

	/**
	 *
	 */
	private function addSettingsSections(): void {
		foreach ( $this->config as $key => $section ) {
			$this->parseSectionWithDefault( $section );
			$this->section_key[ $section[ self::ID ] ] = $key;

			if ( ! $this->showOn( $section[ 'show_on' ] ) ) {
				continue;
			}

			\add_settings_section(
				$section[ self::ID ],
				$section[ self::TITLE ],
				[ $this, 'renderSection' ], //array( $this, $field['callback'] ),
				$this->getGroup() //$section['page']
			);

			$this->addSettingsFields( $section );
		}
	}

	public function renderSection( array $args ) {

		$section = $this->config->get( $this->section_key[ $args[ self::ID ] ] . '.desc', '' );

		if ( \is_callable( $section ) ) {
			$section = \call_user_func( $section, $args );
		}

		echo $section; // XSS ok.
	}

	/**
	 * @param array $section
	 */
	private function addSettingsFields( $section ): void {
		foreach ( $section[ 'fields' ] as $field ) {
			$this->parseFieldWithDefault( $field );
			if ( ! $this->showOn( $field[ 'show_on' ] ) ) {
				continue;
			}

			$this->field_class[ $field[ self::ID ] ] = $field['class'];
			$field['class'] = $field[ self::LABEL_CLASS ];

			\add_settings_field(
				$field[ self::ID ],
				$field['label'],
				[ $this, 'renderField' ], //array( $this, $field['callback'] ),
				$this->getGroup(), //$field['page'],
				$section[ self::ID ],
				$field // $args
			);
		}
	}

	private function parseFieldWithDefault( array &$field ) {
		$field = \array_merge( [
			'show_on'			=> true,
			'label_for'			=> $this->getStringForLabel( $field ),
			'class'				=> '',
			self::LABEL_CLASS	=> '',
			'callback'			=> null,
		], $field );
	}

	public function renderField( array $args ) {

		if ( \is_callable( $args['callback'] ) ) {
			return \call_user_func( $args['callback'], $args );
		}

		// Unset label because it is already rendered by settings_field API
		unset( $args['label'], $args['show_on'], $args['label_for'], $args[ self::LABEL_CLASS ], $args['callback'] );

		$args['class'] = $this->field_class[ $args['id'] ];
		$args['id'] = $args['name'] = $this->getStringForLabel( $args );
		echo $this->fields->render( $args, $this->options_values ); // XSS ok.
		return '';
	}

	/**
	 * Register settings.
	 * This allow you to override this method.
	 */
	private function registerSetting(): void {
		\register_setting(
			$this->getGroup(),
			$this->options->getName(),
			[
				'sanitize_callback'	=>
					[ $this->parser->withFields( $this->fieldsToArray() ), 'parse' ],
				'show_in_rest'      => false,
				'description'       => '',
			]
		);
	}

	private function fieldsToArray() {

		$fields = [];
		foreach ( (array) $this->config as $section ) {
			foreach ( $section['fields'] as $field ) {
				$fields[] = $field;
			}
		}

		return $fields;
	}

	public function getGroup(): string {
		return $this->options->getName() . '_options_group';
	}

	public function getSections(): array {
		return $this->config->toArray();
	}

	public function count(): int {
		return $this->config->count();
	}

	private function parseSectionWithDefault( array &$section ) {
		$title = (array) \explode( ' ', $section[ self::TITLE ] );

		$section = \array_merge( [
			'show_on'	=> true,
			'tab_title'	=> \ucfirst( \strval( $title[0] ) ),
		], $section );
	}

	/**
	 * @param array $args
	 * @return string
	 */
	private function getStringForLabel( array $args ): string {
		return $this->options->getName() . '[' . $args[ 'id' ] . ']';
	}
}
