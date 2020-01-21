<?php
declare(strict_types=1);

namespace ItalyStrap\FileHeader;

/**
 * Interface HeaderFieldsInterface
 * @package ItalyStrap\FileHeader
 */
interface HeaderFieldsInterface {

	/**
	 * @return array
	 */
	public function fields(): array;
}
