<?php
declare(strict_types=1);

namespace ItalyStrap\DataParser\Filters;

/**
 * Trait DefaultSchema
 * @package ItalyStrap\DataParser\Filters
 */
trait DefaultSchema {

	/**
	 * @inheritDoc
	 */
	public function getDefault(): array {
		return [ self::KEY => false ];
	}
}
