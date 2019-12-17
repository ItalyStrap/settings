<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\Config;
use ItalyStrap\Fields\FieldsInterface;

class Sections implements \Countable, SectionsInterface
{
	const TAB_TITLE = 'tab_title';
	const ID = 'id';
	const TITLE = 'title';
	const DESC = 'desc';
	const FIELDS = 'fields';
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

	public function getSections(): array {
		return $this->config->toArray();
	}

	public function load() {
		$this->loadSection();
		$this->register();
	}

	/**
	 *
	 */
	private function loadSection(): void {
		foreach ($this->config as $setting ) {
			if ( isset( $setting[ 'show_on' ] ) && false === $setting[ 'show_on' ] ) {
				continue;
			}

			\add_settings_section(
				$setting[ self::ID ],
				$setting[ self::TITLE ],
				[$this, 'renderSectionCallback'], //array( $this, $field['callback'] ),
				$this->getGroup() //$setting['page']
			);

			$this->loadFields( $setting );
		}
	}

	/**
	 * @param array $setting
	 */
	private function loadFields( $setting ): void {
		foreach ( $setting[ 'fields' ] as $field ) {
			if ( isset( $field[ 'show_on' ] ) && false === $field[ 'show_on' ] ) {
				continue;
			}

			\add_settings_field(
				$field[ self::ID ],
				$field[ self::TITLE ],
				[$this, 'renderField'], //array( $this, $field['callback'] ),
				$this->getGroup(), //$field['page'],
				$setting[ self::ID ],
				$field[ 'args' ]
			);
		}
	}

	/**
	 * Register settings.
	 * This allow you to override this method.
	 */
	private function register(): void {
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

	public function renderSectionCallback( array $args ) {

//		if ( \is_callable( $this->settings[ $args['id'] ]['desc'] ) ) {
//			\call_user_func( $this->settings[ $args['id'] ]['desc'], $args );
//		}

		echo $this->config[ $args['id'] ]['desc'] ?? ''; // XSS ok.
	}

	public function renderField( array $args ) {
		$args['_id'] = $args['_name'] = $this->options->getName() . '[' . $args['id'] . ']';
		echo $this->fields->render( $args, $this->options_values ); // XSS ok.
	}

	public function fieldsToArray() {

		$fields = [];
		foreach ( (array) $this->config as $section ) {
			foreach ( $section['fields'] as $fields_value ) {
				$fields[ $fields_value['id'] ] = $fields_value['args'];
			}
		}

		return $fields;
	}

	public function getGroup(): string {
		return $this->options->getName() . '_options_group';
	}

	public function count(): int {
		return $this->config->count();
	}
}
