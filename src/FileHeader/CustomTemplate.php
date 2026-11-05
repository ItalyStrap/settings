<?php

declare(strict_types=1);

namespace ItalyStrap\FileHeader;

/**
 * Interface CustomTemplate
 * @package ItalyStrap\FileHeader
 */
interface CustomTemplate
{
    public const TEMPLATE_NAME = 'TemplateName';
    public const TEMPLATE_POST_TYPE = 'Template Post Type';

    public const HEADERS = [
        self::TEMPLATE_NAME         => 'Template Name',
        self::TEMPLATE_POST_TYPE    => 'Template Post Type',
    ];
}
