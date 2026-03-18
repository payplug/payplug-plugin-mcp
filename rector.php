<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])

    // Upgrade from PHP 7.2 → 8.4 (includes all rules up to 8.4 automatically)
    ->withPhpSets(php84: true)

    // Safe automatic improvements: dead code, type coverage, code quality
    // Kept at 0 for the migration pass — bump these in a dedicated quality PR
    ->withDeadCodeLevel(0)
    ->withTypeCoverageLevel(0)
    ->withCodeQualityLevel(0)

    // Skip vendor
    ->withSkip([
        __DIR__ . '/vendor',
    ]);
