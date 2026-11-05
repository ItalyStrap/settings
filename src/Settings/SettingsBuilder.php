<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Cleaner\Sanitization;
use ItalyStrap\Cleaner\Validation;
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\DataParser\Filters\SanitizeFilter;
use ItalyStrap\DataParser\Filters\TranslateFilter;
use ItalyStrap\DataParser\Filters\ValidateFilter;
use ItalyStrap\DataParser\LazyParser;
use ItalyStrap\DataParser\ParserInterface;
use ItalyStrap\Fields\Fields;
use ItalyStrap\FileHeader\HeaderFields;
use ItalyStrap\FileHeader\Plugin;
use ItalyStrap\HTML\Attributes;
use ItalyStrap\HTML\Tag;
use ItalyStrap\I18N\Translator;

/**
 * Class SettingsFactory
 * @package ItalyStrap\Settings
 */
class SettingsBuilder
{
    private ?Options $options = null;

    private ?PluginLinks $links = null;

    /**
     * @var string
     */

    private $domain;

    /**
     * @var array
     */
    private $pages_obj;

    /**
     * @var array
     */
    private $pages;

    public function __construct(
        private readonly string $option_name,
        private readonly string $base_name = '',
        private readonly string $plugin_file = '',
    ) {
    }

    public function links(): PluginLinks
    {

        if (!$this->links instanceof PluginLinks) {
            $this->links = new PluginLinks(new Tag(new Attributes()), $this->base_name);
        }

        return $this->links;
    }

    public function options(): OptionsInterface
    {

        if (!$this->options instanceof Options) {
            $this->options = new Options($this->option_name);
        }

        return $this->options;
    }

    protected function parser(): ParserInterface
    {

        $callable = function (): array {

            $filters = [
                new SanitizeFilter(new Sanitization()),
                new ValidateFilter(new Validation())
            ];

            if (!empty($this->domain)) {
                $filters[] = new TranslateFilter(new Translator($this->domain));
            }

            return $filters;
        };

        return new LazyParser($callable);
    }

    /**
     * @return $this
     */
    public function addCustomPluginLink(string $key, string $url, string $text, array $attr = []): static
    {
        $this->links()->addLink(...\func_get_args());
        return $this;
    }

    public function addPage(array $page, array $sections = []): SettingsBuilder
    {

        $this->pages[ $page[ Page::SLUG ] ][] = $page;
        $this->pages[ $page[ Page::SLUG ] ][] = $sections;

        $pages_obj = new Page(
            (new ConfigFactory())->make($page),
            new ViewPage()
        );

        $this->pages_obj[ $pages_obj->getSlug() ][] = $pages_obj;

        if ($sections !== []) {
            $sections_obj = new Sections(
                (new ConfigFactory())->make($sections),
                new Fields(),
                $this->parser(),
                $this->options()
            );

            $pages_obj->withSections($sections_obj);
            $this->pages_obj[ $pages_obj->getSlug() ][] = $sections_obj;
        }

        if (! empty($this->base_name)) {
            $this->links()->forPages($pages_obj);
        }

        return $this;
    }

    public function build(): void
    {

        $file = new \SplFileObject($this->plugin_file);
        $headers_info = new HeaderFields($file, Plugin::HEADERS);

        $this->domain = $headers_info->fields()[ Plugin::TEXT_DOMAIN ];

        \array_map(function (array $to_boot): void {
            foreach ($to_boot as $bootable) {
                $bootable->boot();
            }
        }, $this->pages_obj);

        $this->links()->boot();

        /**
         * Load script for Tabbed admin page
         */
        $asset = new AssetLoader();
        \add_action('admin_enqueue_scripts', $asset->load(...));
    }
}
