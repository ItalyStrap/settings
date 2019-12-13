<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Fields\FieldsInterface;

/**
 * Class Sectons
 * @package ItalyStrap\Settings
 */
class Sections
{
	const TAB_TITLE = 'tab_title';
	const ID = 'id';
	const TITLE = 'title';
	const DESC = 'desc';
	const FIELDS = 'fields';
	/**
	 * Settings for plugin admin page
	 *
	 * @var array
	 */
	protected $sections = [];

	/**
	 * The plugin options
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * The type of fields to create
	 *
	 * @var FieldsInterface
	 */
	protected $fields;

	/**
	 * @var string
	 */
	private $options_group;

	/**
	 * @var string
	 */
	private $options_name;

	/**
	 * @var DataParser
	 */
	private $parser;

	/**
	 * Initialize Class
	 *
	 * @param array $options Get the plugin options.
	 * @param array $sections The configuration array plugin fields.
	 * @param FieldsInterface $fields_type The Fields object.
	 * @param string $options_name
	 * @param string $options_group
	 */
	public function __construct(
		FieldsInterface $fields_type,
		DataParser $parser,
		array $options,
		array $sections,
		string $options_name,
		string $options_group
	) {

		$this->fields = $fields_type;
		$this->parser = $parser;

		$this->sections = $sections;

		$this->options = $options;

		$this->options_name = $options_name;
		$this->options_group = $options_group;
	}

	/**
	 * Init settings for admin area
	 */
	public function load() {
		$this->add();
		$this->register();
	}

	/**
	 *
	 */
	private function add(): void {
		foreach ( $this->sections as $setting ) {
			if ( isset( $setting[ 'show_on' ] ) && false === $setting[ 'show_on' ] ) {
				continue;
			}

			\add_settings_section(
				$setting[ self::ID ],
				$setting[ self::TITLE ],
				[$this, 'renderSectionCallback'], //array( $this, $field['callback'] ),
				$this->options_group //$setting['page']
			);

			$this->addFields( $setting );
		}
	}

	/**
	 * @param $setting
	 */
	private function addFields( $setting ): void {
		foreach ( $setting[ 'fields' ] as $field ) {
			if ( isset( $field[ 'show_on' ] ) && false === $field[ 'show_on' ] ) {
				continue;
			}

			\add_settings_field(
				$field[ self::ID ],
				$field[ self::TITLE ],
				[$this, 'renderField'], //array( $this, $field['callback'] ),
				$this->options_group, //$field['page'],
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
			$this->options_group,
			$this->options_name,
			[
				'sanitize_callback'	=>
					[ $this->parser->withFields( $this->fieldsToArray() ), 'parse' ],
				'show_in_rest'      => false,
				'description'       => '',
			]
		);
	}

	/**
	 * Render section CB
	 *
	 * @param  array $args The arguments for section CB.
	 */
	public function renderSectionCallback( array $args ) {

//		if ( \is_callable( $this->settings[ $args['id'] ]['desc'] ) ) {
//			\call_user_func( $this->settings[ $args['id'] ]['desc'], $args );
//		}

		echo $this->sections[ $args['id'] ]['desc'] ?? ''; // XSS ok.
	}

	/**
	 * Get the field type
	 *
	 * @param array $args Array with arguments.
	 */
	public function renderField( array $args ) {
		$args['_id'] = $args['_name'] = $this->options_name . '[' . $args['id'] . ']';
		echo $this->fields->render( $args, $this->options ); // XSS ok.
	}

	/**
	 * Get the plugin fields
	 *
	 * @return array The plugin fields
	 */
	public function fieldsToArray() {

		$fields = [];
		foreach ( (array) $this->sections as $section ) {
			foreach ( $section['fields'] as $fields_value ) {
				$fields[ $fields_value['id'] ] = $fields_value['args'];
			}
		}

		return $fields;
	}
}
