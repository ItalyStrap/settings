<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigInterface as Config;

/**
 * Class Page
 * @package ItalyStrap\Settings
 */
class Page implements PageInterface
{
    use ShowableTrait;

    public const DS            = DIRECTORY_SEPARATOR;

    public const PAGE_TITLE    = 'page_title';
    public const MENU_TITLE    = 'menu_title';
    public const CAPABILITY    = 'capability';
    public const SLUG          = 'menu_slug';
    public const CALLBACK      = 'callback';
    public const ICON          = 'icon_url';
    public const POSITION      = 'position';
    public const VIEW          = 'view';
    public const PARENT        = 'parent';

    public const EVENT         = 'admin_menu';

    private ?SectionsInterface $sections = null;

    /**
     * Pages constructor.
     */
    public function __construct(private Config $config, private ViewPageInterface $view)
    {
    }

    /**
     * @inheritDoc
     */
    public function isSubmenu(): bool
    {
        return $this->config->has(self::PARENT);
    }

    /**
     * @inheritDoc
     */
    public function getParentPageSlug(): string
    {
        return $this->config->{self::PARENT};
    }

    /**
     * @inheritDoc
     */
    public function getPageTitle(): string
    {
        return $this->config->get(self::PAGE_TITLE, __('Settings'));
    }

    /**
     * @inheritDoc
     */
    public function getMenuTitle(): string
    {
        return $this->config->get(self::MENU_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getSlug(): string
    {
        return \sanitize_key($this->config->{self::SLUG});
    }

    /**
     * @inheritDoc
     */
    public function withSections(SectionsInterface $sections): Page
    {
        $this->sections = $sections;
        $this->sections->forPage($this);
        $this->view->forPage($this);
        $this->view->withSections($sections);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function register()
    {

        if (! $this->showOn($this->config->get('show_on', true))) {
            return false;
        }

        $this->assertHasMinimumValueSet($this->config);

        $callable = $this->config->get(self::CALLBACK);

        if ($this->isSubmenu()) {
            return \add_submenu_page(
                $this->getParentPageSlug(),
                $this->getPageTitle(),
                $this->getMenuTitle(),
                $this->config->get(self::CAPABILITY, 'manage_options'),
                $this->getSlug(),
                $this->getCallable($callable, $this->config)
            );
        }

        return \add_menu_page(
            $this->getPageTitle(),
            $this->getMenuTitle(),
            $this->config->get(self::CAPABILITY, 'manage_options'),
            $this->getSlug(),
            $this->getCallable($callable, $this->config),
            $this->config->get(self::ICON),
            $this->config->get(self::POSITION)
        );
    }

    private function assertHasMinimumValueSet(Config $config): void
    {
        if (! $config->{self::MENU_TITLE}) {
            throw new \RuntimeException(\sprintf('%s must be not empty', self::MENU_TITLE));
        }

        if (! $config->{self::SLUG}) {
            throw new \RuntimeException(\sprintf('%s must be not empty', self::SLUG));
        }
    }

    /**
     * @param mixed $callable
     */
    private function getCallable($callable, Config $config): callable
    {
        return \is_callable($callable) ? $callable : function () use ($config): void {
            $this->view->render($config->get(self::VIEW, ''));
        };
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
