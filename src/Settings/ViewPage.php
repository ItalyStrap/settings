<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

class ViewPage implements ViewPageInterface {

	use ShowableTrait;

	const DS = DIRECTORY_SEPARATOR;

	/**
	 * @var string
	 */
	private $capability = 'manage_options';

	/**
	 * @var string
	 */
	private $pagenow;

	/**
	 * @var SectionsInterface
	 */
	private $sections;

	/**
	 * @var Page
	 */
	private $page;

	/**
	 * @inheritDoc
	 */
	public function withCapability( $capability ): ViewPageInterface {
		$this->capability = $capability;
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function withSections( SectionsInterface $sections ): void {
		$this->sections = $sections;
	}

	/**
	 * @inheritDoc
	 */
	public function forPage( PageInterface $page ): ViewPage {
		$this->page = $page;
		return $this;
	}

	/**
	 * @return string
	 */
	private function getPageSlug() {
		return $this->page->getSlug();
	}

	/**
	 * @inheritDoc
	 */
	public function render( $view = '' ): void {
		$this->assertCurrentUserCanSeeThePage();
		require $this->findView( $view );
	}

	/**
	 * The add_submenu_page callback
	 */
	private function findView( $file_name ) {

		if ( ! \is_readable( $file_name ) ) {
			return __DIR__ . self::DS . 'view' . self::DS . 'form.php';
		}

		return $file_name;
	}

	/**
	 * Prints out all settings sections added to a particular settings page
	 *
	 * Part of the Settings API. Use this in a settings page callback function
	 * to output all the sections and fields that were added to that $page with
	 * add_settings_section() and add_settings_field()
	 *
	 * @global array $wp_settings_sections Storage array of all settings sections added to admin pages
	 * @global array $wp_settings_fields Storage array of settings fields and info about their pages/sections
	 * @since 2.7.0
	 *
	 * @param string $page The slug name of the page whose settings sections you want to output.
	 */
	private function doSettingsSections( $page ) {

		global $wp_settings_sections, $wp_settings_fields;

		if ( ! isset( $wp_settings_sections[ $page ] ) ) {
			return;
		}

		$count = 1;

		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			echo '<div id="tabs-' . $count . '" class="wrap">'; // XSS ok.
			if ( $section['title'] ) {
				echo "<h2>{$section['title']}</h2>\n"; // XSS ok.
			}

			if ( $section['callback'] ) {
				\call_user_func( $section['callback'], $section );
			}

			if (
				! isset( $wp_settings_fields )
				|| ! isset( $wp_settings_fields[ $page ] )
				|| ! isset( $wp_settings_fields[ $page ][ $section['id'] ] )
			) {
				continue;
			}
			echo '<table class="form-table">';
			\do_settings_fields( $page, $section['id'] );
			echo '</table>';
			echo '</div>';
			$count++;
		}
	}

	/**
	 * Create the nav tabs for section in admin plugin area
	 */
	private function createNavTab() {

		if ( $this->sections->count() <= 1 ) {
			return '';
		}

		$count = 1;

		$out = '<ul>';

		foreach ( $this->sections->getSections() as $key => $section ) {
			if ( isset( $section[ 'show_on' ] ) && ! $this->showOn( $section[ 'show_on' ] ) ) {
				continue;
			}

			$out .= '<li><a href="#tabs-' . $count . '">' . $section['tab_title'] . '</a></li>';
			$count++;
		}

		$out .= '</ul>';

		echo $out; // XSS ok.
		return '';
	}

	private function assertCurrentUserCanSeeThePage(): void {
		if ( ! \current_user_can( $this->capability ) ) {
			\wp_die( \esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}
	}

	private function assertHasSections() {
		if ( ! $this->sections instanceof SectionsInterface ) {
			$message = \sprintf(
				'You must assign an object that implements %1$s to %2$s::withSections() before calling %2$s::render()',
				SectionsInterface::class,
				__CLASS__
			);

			throw new \RuntimeException( $message );
		}
	}
}
