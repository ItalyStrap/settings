<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigInterface as Config;
use ItalyStrap\DataParser\ParserInterface;
use ItalyStrap\Fields\FieldsInterface;

class Sections implements \Countable, SectionsInterface
{
    use ShowableTrait;

    public const TAB_TITLE = 'tab_title';
    public const ID = 'id';
    public const TITLE = 'title';
    public const DESC = 'desc';
    public const FIELDS = 'fields';
    public const LABEL_CLASS = 'class_for_label';

    public const EVENT         = 'admin_init';

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

    private array $field_class = [];
    private $section_key;

    private ?PageInterface $page = null;

    /**
     * Sections constructor.
     * @param Config $config The configuration array plugin fields.
     * @param FieldsInterface $fields The Fields object.
     * @param OptionsInterface $options Get the plugin options.
     */
    public function __construct(
        Config $config,
        FieldsInterface $fields,
        private ParserInterface $parser,
        private OptionsInterface $options
    ) {
        $this->config = $config;
        $this->fields = $fields;
//      if ( empty( $this->options->toArray() ) ) {
//          $this->options->addAll( $this->defaultValue() );
//      }
    }

    /**
     * @inheritDoc
     */
    public function forPage(PageInterface $page): Sections
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPageSlug(): string
    {
        return $this->page->getSlug();
    }

    /**
     * @inheritDoc
     */
    public function getSections(): array
    {
        return $this->config->toArray();
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->config->count();
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->addSettingsSections();
        $this->registerSetting();
    }

    /**
     *
     */
    private function addSettingsSections(): void
    {
        foreach ($this->config as $key => $section) {
            $this->parseSectionWithDefault($section);
            $this->section_key[ $section[ self::ID ] ] = $key;

            if (! $this->showOn($section[ 'show_on' ])) {
                continue;
            }

            \add_settings_section(
                $section[ self::ID ],
                $section[ self::TITLE ],
                $this->renderSection(...),
                $this->getPageSlug()
            );

            $this->addSettingsFields($section);
        }
    }

    private function parseSectionWithDefault(array &$section): void
    {
        $title = \explode(' ', $section[ self::TITLE ]);

        $section = \array_merge([
            'show_on'   => true,
            'tab_title' => \ucfirst(\strval($title[0])),
        ], $section);
    }

    /**
     * @inheritDoc
     */
    public function renderSection(array $section): void
    {

        $content = $this->config->get(( $this->section_key[ $section[ self::ID ] ] ?? '' ) . '.desc', '');

        if (\is_callable($content)) {
            $content = \call_user_func($content, $section);
        }

        echo \wp_kses_post(\strval($content));
    }

    private function addSettingsFields(array $section): void
    {
        foreach ($section[ self::FIELDS ] as $field) {
            $this->parseFieldArgsWithDefaultBeforePassingToRenderField($field);
            if (! $this->showOn($field[ 'show_on' ])) {
                continue;
            }

            $this->field_class[ $field[ self::ID ] ] = $field['class'];
            $field['class'] = $field[ self::LABEL_CLASS ];

            \add_settings_field(
                $field[ self::ID ],
                $field['label'],
                $this->renderField(...), //array( $this, $field['callback'] ),
                $this->getPageSlug(), //$field['page'],
                $section[ self::ID ],
                $field // $args Value passed to the renderField method
            );
        }
    }

    private function parseFieldArgsWithDefaultBeforePassingToRenderField(array &$field): void
    {
        $field = \array_merge([
            'show_on'           => true,
            'label_for'         => $this->getStringForLabel($field),
            'class'             => '',
            self::LABEL_CLASS   => '',
            'callback'          => null,
            'value'             => '',
        ], $field);
    }

    /**
     * @inheritDoc
     */
    public function renderField(array $args_for_field): void
    {

        if (\is_callable($args_for_field['callback'])) {
            $content = \call_user_func($args_for_field['callback'], $args_for_field);
        } else {
            $this->parseArgsForFieldsBeforeRenderMethod($args_for_field);
            $content = $this->fields->render($args_for_field); // XSS ok.
        }

        echo $content;
    }

    /**
     * Register settings.
     * This allow you to override this method.
     */
    private function registerSetting(): void
    {
        \register_setting(
            $this->getPageSlug(),
            $this->getOptionsName(),
            [
                'sanitize_callback' => [ $this->parser->withSchema($this->schemaForDataParser()), 'parseValues' ],
                'show_in_rest'      => false,
                'description'       => '',
            ]
        );
    }

    /**
     * @return mixed[]
     */
    private function schemaForDataParser(): array
    {

        $schema = [];
        foreach ($this->config->toArray() as $section) {
            foreach ($section['fields'] as $field) {
                $schema[ $field['id'] ] = $field;
            }
        }

        return $schema;
    }

//  private function defaultValue() {
    //
    //      $default = [];
    //      foreach ( $this->config->toArray() as $section ) {
    //          foreach ( $section['fields'] as $field ) {
    //              $default[ $field['id'] ] = $field['value'] ?? '';
    //          }
    //      }
    //
    //      return $default;
    //  }
    private function getStringForLabel(array $args): string
    {
        return $this->getOptionsName() . '[' . $args[ 'id' ] . ']';
    }

    private function getOptionsName(): string
    {
        return $this->options->getName();
    }

    private function parseArgsForFieldsBeforeRenderMethod(array &$args_for_fields): void
    {
        // Unset label because it is already rendered by settings_field API
        unset(
            $args_for_fields[ 'label' ],
            $args_for_fields[ 'show_on' ],
            $args_for_fields[ 'label_for' ],
            $args_for_fields[ self::LABEL_CLASS ],
            $args_for_fields[ 'callback' ]
        );

        $args_for_fields[ 'class' ] = $this->field_class[ $args_for_fields[ 'id' ] ] ?? '';

        $args_for_fields[ 'value' ] = $this->options->get($args_for_fields[ 'id' ], $args_for_fields[ 'value' ]);
        $args_for_fields[ 'id' ] = $args_for_fields[ 'name' ] = $this->getStringForLabel($args_for_fields);
    }

    /**
     * @return bool
     */
    public function boot()
    {
        return \add_action(self::EVENT, $this->register(...));
    }

    /**
     * @return bool
     */
    public function unBoot()
    {
        return \remove_action(self::EVENT, $this->register(...));
    }
}
