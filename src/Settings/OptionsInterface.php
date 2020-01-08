<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;

use ItalyStrap\Config\ConfigInterface;

/**
 * Class Options
 * @package ItalyStrap\Settings
 */
interface OptionsInterface extends ConfigInterface {
	/**
	 * @return string
	 */
	public function getName();
}
