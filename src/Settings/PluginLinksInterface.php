<?php

declare(strict_types=1);

namespace ItalyStrap\Settings;

/**
 * Class for Plugin_Links
 */
interface PluginLinksInterface
{
    /**
     * @param array<string> $attr
     * @return $this
     */
    public function addLink(string $key, string $url, string $text, array $attr = []);

    public function getLinks(): array;

    /**
     * @return $this
     */
    public function forPages(PageInterface ...$pages);
}
