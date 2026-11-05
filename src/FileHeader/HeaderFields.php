<?php

declare(strict_types=1);

namespace ItalyStrap\FileHeader;

use SplFileObject;

/**
 * Class PluginData
 * @link https://codex.wordpress.org/File_Header
 * @link https://developer.wordpress.org/themes/basics/main-stylesheet-style-css/
 * @link https://developer.wordpress.org/plugins/plugin-basics/header-requirements/
 * @package ItalyStrap\FileHeader
 */
class HeaderFields implements HeaderFieldsInterface
{
    /**
     * PluginData constructor.
     */
    public function __construct(private readonly SplFileObject $file, private readonly array $headers)
    {
    }

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        $content = $this->fileContent();

        $headers = [];
        foreach ($this->headers as $field => $regex) {
            $headers[ $field ] = '';
            if (
                \preg_match(
                    '/^[ \t\/*#@]*' . \preg_quote($regex, '/') . ':(.*)$/mi',
                    $content,
                    $match
                ) && $match[ 1 ]
            ) {
                $headers[ $field ] = \trim(
                    \preg_replace('/\s*(?:\*\/|\?>).*/', '', $match[ 1 ])
                );
            }
        }

        return $headers;
    }

    private function fileContent(): string
    {
        return \strval(\str_replace("\r", "\n", $this->file->fread(8 * 1024)));
    }
}
