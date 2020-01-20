<?php
declare(strict_types=1);

namespace ItalyStrap\FileHeader;

/**
 * Interface CustomTemplate
 * @package ItalyStrap\FileHeader
 */
interface CustomTemplate {

	const TEMPLATE_NAME = 'TemplateName';
	const TEMPLATE_POST_TYPE = 'Template Post Type';

	const HEADERS = [
		self::TEMPLATE_NAME			=> 'Template Name',
		self::TEMPLATE_POST_TYPE	=> 'Template Post Type',
	];
}
