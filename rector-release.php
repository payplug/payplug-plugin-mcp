<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

/**
 * Release configuration: downgrades src/ to PHP 7.2 into the release/ directory.
 *
 * Workflow:
 *   1. Copy src/ → release/  (done by the Makefile `release` target)
 *   2. Run: vendor/bin/rector process --config rector-release.php
 */
return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/payplug-core',
    ])

    // Target PHP 7.2 — Rector applies all downgrade rules from current version down to 7.2
    ->withDowngradeSets(php72: true)

    ->withSkip([
        __DIR__ . '/vendor',
    ]);
