<?php

declare(strict_types=1);

namespace ItalyStrap\Tests;

use lucatume\WPBrowser\TestCase\WPTestCase;
use ItalyStrap\Settings\ViewPageInterface;
use ItalyStrap\Settings\Page;
use ItalyStrap\Settings\Sections;
use ItalyStrap\Settings\ViewPage;

class ViewPageTest extends WPTestCase
{
    /**
     * @var \IntegrationTester
     */
    protected $tester;

    private string $page_name;

    /**
     * @var array|\WP_UnitTest_Factory|null
     */
    private array $sections;

    public function setUp(): void
    {
        // Before...
        parent::setUp();
        \wp_set_current_user('1', 'admin');
        // Your set up methods here.

        $this->page_name = 'group_name';

        $this->sections = [
            [
                'tab_title' => 'Standard'
            ],
            [
                'tab_title' => 'Advanced'
            ],
        ];
    }

    public function tearDown(): void
    {
        // Your tear down methods here.

        // Then...
        parent::tearDown();
    }

    private function getInstance(): ViewPage
    {
        $sut = new ViewPage();
        $this->assertInstanceOf(ViewPageInterface::class, $sut, '');
        $this->assertInstanceOf(ViewPage::class, $sut, '');
        return $sut;
    }

    private function getSections()
    {

        return $this->make(Sections::class, [
            'count'     => \count($this->sections),
            'getPageSlug'   => fn(): string => $this->page_name,
            'getSections'   => fn(): array => $this->sections,
        ]);
    }

    private function getPage()
    {

        return $this->make(Page::class, [
            'getSlug'   => fn(): string => $this->page_name,
        ]);
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
    public function itShouldBeRenderDefaultPageIfNoViewFileIsProvided(): void
    {
        $sut = $this->getInstance();
        $sut->withSections($this->getSections());
        $sut->forPage($this->getPage());
        $sut->render();
        $output = $this->getActualOutputForAssertion();
        $this->assertStringContainsString('<form method="post" action="options.php"', $output, '');
    }

    /**
     * @test
     */
    public function itShouldBeRenderCustomPageProvided(): void
    {
        $sut = $this->getInstance();
        $sut->withSections($this->getSections());
        $sut->render(codecept_data_dir('fixtures/view/') . 'empty_form.php');
        $output = $this->getActualOutputForAssertion();
        $this->assertStringContainsString('<h1>Form Test</h1>', $output, '');
    }

    /**
     * @test
     */
    public function itShouldBeRenderTab(): void
    {
        $sut = $this->getInstance();
        $sut->withSections($this->getSections());
        $sut->forPage($this->getPage());
        $sut->render();
        $output = $this->getActualOutputForAssertion();

        $count = 1;
        foreach ($this->sections as $section) {
            $this->assertStringContainsString('<a href="#tabs-' . $count . '">', $output, '');
            $this->assertStringContainsString($section['tab_title'], $output, '');
            $count++;
        }
    }

    /**
     * @test
     */
    public function itShouldThrownRuntimeExceptionIfHasNoSections(): void
    {
        $this->expectException(\RuntimeException::class);
        $sut = $this->getInstance();
        $sut->render('');
    }
}
