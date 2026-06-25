<?php

declare(strict_types=1);

/**
 * Generates a PHP 7.1-compatible composer.json in payplug-core/ from the root composer.json.
 *
 * Changes applied:
 *  - php requirement → ^7.1
 *  - captainhook removed from require (dev-only tool)
 *  - require-dev replaced with PHP 7.1-compatible versions (phpunit ^7.5, mockery ^1.3)
 *  - autoload psr-4: PayPlugPluginCore\ → src/; tests/ added for class resolution
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

$jsonContent = file_get_contents($source);
if ($jsonContent === false) {
    fwrite(STDERR, "Error: Failed to read {$source}\n");
    exit(1);
}

$data = json_decode($jsonContent, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    fwrite(STDERR, "Error: Failed to decode {$source} as JSON: " . json_last_error_msg() . "\n");
    exit(1);
}

// Downgrade PHP requirement
$data['require']['php'] = '^7.1';

// Remove dev-only tools from require and scripts
unset($data['require']['captainhook/captainhook'], $data['scripts']);

// Replace require-dev with PHP 7.1-compatible test dependencies
$data['require-dev'] = [
    'phpunit/phpunit' => '^7.5',  // PHPUnit 7.x requires PHP >=7.1
    'mockery/mockery' => '^1.3',  // Mockery 1.3.x requires PHP >=5.4
];

// Fix autoload: src/ and tests/ are subdirectories of the package root.
$data['autoload']['psr-4'] = [
    'PayPlugPluginCore\\Tests\\' => 'tests/',
    'PayPlugPluginCore\\'        => 'src/',
];
unset($data['autoload-dev']);

$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
if ($json === false) {
    fwrite(STDERR, "Error: Failed to encode composer.json: " . json_last_error_msg() . "\n");
    exit(1);
}

$bytesWritten = file_put_contents($target, $json);

if ($bytesWritten === false || $bytesWritten < strlen($json)) {
    fwrite(STDERR, "Error: Failed to write composer.json to {$target}\n");
    exit(1);
}

echo "Generated {$target} (PHP ^7.1)\n";
