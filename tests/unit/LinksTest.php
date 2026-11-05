<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use Codeception\Test\Unit;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use ItalyStrap\Settings\Page;
use ItalyStrap\Settings\PluginLinks;
use ItalyStrap\HTML\Tag;
use ItalyStrap\HTML\Attributes;

use function tad\FunctionMockerLe\define;

class LinksTest extends Unit
{
    use ProphecyTrait;


    /**
     * @var \UnitTester
     */
    protected $tester;

    private ?ObjectProphecy $page = null;

    private ?string $admin_url = null;

	// phpcs:ignore -- Method from Codeception
	protected function _before() {
		// phpcs:ignore -- This is not a constant definition
		define( 'apply_filters', fn($filter_name, $value) => $value);
		// phpcs:ignore -- This is not a constant definition
		define( 'esc_attr', fn($value) => $value);
		// phpcs:ignore -- This is not a constant definition
		define( 'esc_html', fn($value) => $value);
		// phpcs:ignore -- This is not a constant definition
		define( 'esc_url', fn($value) => $value);

        $this->admin_url = 'http://wordpress/wp-admin/';

		// phpcs:ignore -- This is not a constant definition
		define( 'admin_url', fn($value = ''): string => $this->admin_url . $value);

        $this->page = $this->prophesize(Page::class);
    }

	// phpcs:ignore -- Method from Codeception
	protected function _after() {
    }

    private function getInstance(): PluginLinks
    {
        $tag = new Tag(new Attributes());
        $sut = new PluginLinks($tag, 'plugin_base_name');
        $this->assertInstanceOf(PluginLinks::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable(): void
    {
        $this->getInstance();
    }

    private function getPage()
    {
        return $this->page->reveal();
    }

    /**
     * @test
     */
    public function itShouldAddsLinksForPage(): void
    {
        $sut = $this->getInstance();

        $slug = 'some-test-slug';
        $content = 'Title';
        $this->page->getSlug()->willReturn($slug);
        $this->page->getPageTitle()->willReturn($content);
        $this->page->isSubmenu()->willReturn(false);

        $sut->forPages($this->getPage());

        $links = $sut->getLinks();
        $this->assertIsArray($links, '');

        foreach ($links as $link) {
            $this->assertStringContainsString(\sprintf(
                '<a href="%1$s" aria-label="%2$s">%2$s</a>',
                $this->admin_url . 'admin.php?page=' . $slug,
                $content
            ), $link, '');
        }
    }

    /**
     * @test
     */
    public function itShouldAddsLinksForSubPage(): void
    {
        $sut = $this->getInstance();

        $slug = 'some-test-slug';
        $content = 'Title';
        $this->page->getSlug()->willReturn($slug);
        $this->page->getPageTitle()->willReturn($content);
        $this->page->isSubmenu()->willReturn(true);
        $this->page->getParentPageSlug()->willReturn('parent');

        $sut->forPages($this->getPage());

        $links = $sut->getLinks();
        $this->assertIsArray($links, '');

        foreach ($links as $link) {
            $this->assertStringContainsString(\sprintf(
                '<a href="%1$s" aria-label="%2$s">%2$s</a>',
                $this->admin_url . 'admin.php?page=' . $slug,
                $content
            ), $link, '');
        }
    }

    /**
     * @test
     */
    public function itShouldAddsLinksForSubPageOfWPPages(): void
    {
        $sut = $this->getInstance();

        $parents = $sut->getDefaultPages();

        foreach ($parents as $parent) {
            $slug = 'some-test-slug';
            $content = 'Title';
            $this->page->getSlug()->willReturn($slug);
            $this->page->getPageTitle()->willReturn($content);
            $this->page->isSubmenu()->willReturn(true);
            $this->page->getParentPageSlug()->willReturn($parent);

            $sut->forPages($this->getPage());

            $links = $sut->getLinks();
            $this->assertIsArray($links, '');

            foreach ($links as $link) {
                $this->assertStringContainsString(\sprintf(
                    '<a href="%1$s" aria-label="%2$s">%2$s</a>',
                    $this->admin_url . $parent . '?page=' . $slug,
                    $content
                ), $link, '');
            }
        }
    }

    /**
     * @test
     */
    public function itShouldAddCustomLink(): void
    {
        $sut = $this->getInstance();

        $key = 'some-key';
        $url = 'http://localhost.com';
        $content = 'Title';

        $sut->addLink($key, $url, $content);

        $links = $sut->getLinks();

        $this->assertArrayHasKey($key, $links, '');

        $this->assertStringContainsString(\sprintf(
            '<a href="%1$s" aria-label="%2$s">%2$s</a>',
            $url,
            $content
        ), $links[ $key ], '');
    }
}
