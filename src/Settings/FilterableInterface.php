<?php
declare(strict_types=1);

namespace ItalyStrap\Settings;


interface FilterableInterface
{
	public function filter( $schema, $data );
}
