<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use lucatume\WPBrowser\TestCase\WPTestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use ItalyStrap\Settings\SectionsInterface;
use ItalyStrap\Config\ConfigFactory;
use ItalyStrap\Settings\Page as P;
use ItalyStrap\Settings\PageInterface;
use ItalyStrap\Settings\ViewPageInterface;

/**
 * Class PageTest
 * @covers \ItalyStrap\Settings\Page
 */
class PageTest extends WPTestCase
{
    use ProphecyTrait;


    /**
     * @var \IntegrationTester
     */
    protected $tester;

    private array $sections_config;

    /**
     * @var array|\WP_UnitTest_Factory|null
     */
    private array $page_config;

    private ObjectProphecy $sections;

    private ObjectProphecy $view;

    /**
     * @return ViewPageInterface
     */
    public function getView()
    {
        return $this->view->reveal();
    }

    public function setUp(): void
    {
        // Before...
        parent::setUp();

        $this->sections_config = require codecept_data_dir('fixtures/config/') . 'sections.php';
        $this->page_config = (array)require codecept_data_dir('fixtures/config/') . 'page.php';

        wp_set_current_user(1);

        global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages,
               $submenu, $_wp_real_parent_file, $_wp_submenu_nopriv;

        $this->sections = $this->prophesize(SectionsInterface::class);
        $this->view = $this->prophesize(ViewPageInterface::class);
        // Your set up methods here.
    }

    public function tearDown(): void
    {
        // Your tear down methods here.

        global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages,
               $submenu, $_wp_real_parent_file, $_wp_submenu_nopriv;

        $menu = [];
        $admin_page_hooks = [];
        $_registered_pages = [];
        $_parent_pages = [];
        $submenu = [];
        $_wp_real_parent_file = [];
        $_wp_submenu_nopriv = [];

        // Then...
        parent::tearDown();
    }

    private function getSections()
    {
        return $this->sections->reveal();
    }

    private function getInstance(array $config = []): P
    {

        $config = ( new ConfigFactory() )->make($config);

        $sut = new P($config, $this->getView());
        $this->assertInstanceOf(PageInterface::class, $sut, '');
        $this->assertInstanceOf(P::class, $sut, '');
        return $sut;
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable(): void
    {
        $this->getInstance();
    }

    /**
     * @test
     */
    public function itShouldReturnTrueIfIsSubmenu(): void
    {

        $config = [
            P::PARENT   => 'test',
        ];

        $sut = $this->getInstance($config);
        $this->assertTrue($sut->isSubmenu(), '');
    }

    /**
     * @test
     */
    public function itShouldReturnParentPageSlug(): void
    {

        $config = [
            P::PARENT   => 'test',
        ];

        $sut = $this->getInstance($config);
        $this->assertStringContainsString('test', $sut->getParentPageSlug(), '');
    }

    /**
     * @test
     */
    public function itShouldReturnPageTitle(): void
    {

        $config = [
            P::PAGE_TITLE   => 'test',
        ];

        $sut = $this->getInstance($config);
        $this->assertStringContainsString('test', $sut->getPageTitle(), '');
    }

    /**
     * @test
     */
    public function itShouldReturnMenuTitle(): void
    {

        $config = [
            P::MENU_TITLE   => 'test',
        ];

        $sut = $this->getInstance($config);
        $this->assertStringContainsString('test', $sut->getMenuTitle(), '');
    }

    /**
     * @test
     */
    public function itShouldReturnPageSlug(): void
    {

        $config = [
            P::SLUG => 'test',
        ];

        $sut = $this->getInstance($config);
        $this->assertStringContainsString('test', $sut->getSlug(), '');
    }

    /**
     * @test
     */
    public function itShouldRegister(): void
    {
        $sut = $this->getInstance($this->page_config);
        $sut->register();
    }

    public static function invalidConfigProvider(): array
    {
        return [
            'if no menu title is provided'  => [
                [
                    P::SLUG => 'italystrap-dashboard'
                ],
            ],
            'if no slug is provided'    => [
                [
                    P::MENU_TITLE => \__('ItalyStrap', 'italystrap')
                ],
            ]
        ];
    }

    /**
     * @test
     * @dataProvider invalidConfigProvider
     */
    public function itShouldThrownError(array $config): void
    {
        $this->expectException(\RuntimeException::class);
        $sut = $this->getInstance($config);
        $sut->register();
    }

    /**
     * @test
     */
    public function itShouldRegisterMenuAndSubmenu(): void
    {

        global $menu, $admin_page_hooks, $_registered_pages, $_parent_pages,
               $submenu, $_wp_real_parent_file, $_wp_submenu_nopriv;

        $slug = 'test-dashboard';

        $pages = [
            P::MENU_TITLE => \__('Menu title', 'italystrap'),
            P::SLUG => $slug,
        ];

        $sut = $this->getInstance($pages);
        $hook = $sut->register();
        $this->assertStringContainsString($hook, 'toplevel_page_' . $slug, '');
        $this->assertArrayHasKey($slug, $admin_page_hooks, '');


        $sub_slug = 'sub-slug';

        $pages = [
            P::PARENT => $slug,
            P::MENU_TITLE => \__('Submenu title', 'italystrap'),
            P::SLUG => $sub_slug,
        ];

        $sut = $this->getInstance($pages);
        $hook = $sut->register();
        $this->assertStringContainsString($hook, 'menu-title_page_' . $sub_slug, '');
        $this->assertArrayHasKey($slug, $_parent_pages, '');
        $this->assertArrayHasKey($sub_slug, $_parent_pages, '');
    }
}
