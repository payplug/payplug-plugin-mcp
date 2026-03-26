<?php

declare(strict_types=1);

/**
 * Generates a PHP 7.2-compatible composer.json in release/ from the root composer.json.
 *
 * Changes applied:
 *  - php requirement → ^7.2
 *  - captainhook removed from require (dev-only tool)
 *  - require-dev replaced with PHP 7.2-compatible versions (phpunit ^8.5, mockery ^1.3)
 *  - autoload psr-4: PayplugPluginCore\ → src/; tests/ added so class resolution works with --no-dev
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

try {
    $jsonContent = file_get_contents($source);
    if ($jsonContent === false) {
        fwrite(STDERR, "Error: Failed to read {$source}\n");
        exit(1);
    }
    $data = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    fwrite(STDERR, "Error: Failed to decode {$source} as JSON: " . $e->getMessage() . "\n");
    exit(1);
}

// Downgrade PHP requirement
$data['require']['php'] = '^7.2';

// Remove dev-only tools from require and scripts
unset($data['require']['captainhook/captainhook'], $data['scripts']);

// Replace require-dev with PHP 7.2-compatible test dependencies
// (phpunit ^11 requires PHP >=8.2; mockery ^1.6 requires PHP >=7.3)
$data['require-dev'] = [
    'phpunit/phpunit'   => '^8.5',  // PHPUnit 8.x requires PHP >=7.2
    'mockery/mockery'   => '^1.3',  // Mockery 1.3.x requires PHP >=5.4
];

// Fix autoload: src/ is now a proper subdirectory of the package root.
// The Tests\ prefix is more specific and must be declared before PayplugPluginCore\ so that
// PSR-4 resolves test classes to tests/ regardless of whether composer install runs with --no-dev.
$data['autoload']['psr-4'] = [
    'PayplugPluginCore\\Tests\\' => 'tests/',
    'PayplugPluginCore\\'        => 'src/',
];

// Mirror in autoload-dev for tools that rely on it
// $data['autoload-dev'] = ['psr-4' => ['PayplugPluginCore\\Tests\\' => 'tests/']];

$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";

$bytesWritten = file_put_contents($target, $json);

if ($bytesWritten === false || $bytesWritten < strlen($json)) {
    fwrite(STDERR, "Error: Failed to write composer.json to {$target}\n");
    exit(1);
}

echo "Generated {$target} (PHP ^7.2)\n";
