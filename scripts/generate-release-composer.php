<?php

declare(strict_types=1);

/**
 * Generates a PHP 7.2-compatible composer.json in release/ from the root composer.json.
 *
 * Changes applied:
 *  - php requirement → ^7.2
 *  - captainhook removed from require (dev-only tool)
 *  - require-dev, autoload-dev, and scripts sections stripped
 *  - autoload psr-4 path changed from src/ to "" (release/ is the package root)
 */

$root = dirname(__DIR__);
$source = $root . '/composer.json';
$target = $root . '/payplug-core/composer.json';

if (!file_exists($source)) {
    fwrite(STDERR, "Error: composer.json not found at {$source}\n");
    exit(1);
}

if (!is_dir($root . '/payplug-core')) {
    fwrite(STDERR, "Error: payplug-core/ directory does not exist. Run the release step first.\n");
    exit(1);
}

$data = json_decode(file_get_contents($source), true, 512, JSON_THROW_ON_ERROR);

// Downgrade PHP requirement
$data['require']['php'] = '^7.2';

// Remove dev-only dependencies from require
unset($data['require']['captainhook/captainhook']);

// Strip dev-only sections
unset($data['require-dev']);
unset($data['autoload-dev']);
unset($data['scripts']);

// Fix autoload: release/ is the package root, not src/
$data['autoload']['psr-4'] = ['PayplugPluginCore\\' => ''];

$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";

file_put_contents($target, $json);

echo "Generated release/composer.json (PHP ^7.2)\n";
