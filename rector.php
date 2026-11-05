<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        __DIR__ . '/tests/_data',
        __DIR__ . '/tests/_output',
        __DIR__ . '/tests/_support/_generated',
        // The view/form.php template calls its private methods with $this
        __DIR__ . '/src/Settings/ViewPage.php',
    ])
    ->withPhpSets(php82: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
    )
    ->withImportNames(importShortClasses: false, removeUnusedImports: true);
