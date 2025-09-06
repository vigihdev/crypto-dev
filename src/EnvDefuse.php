<?php

declare(strict_types=1);

namespace Vigihdev\CryptoDev;

use Vigihdev\CryptoDev\Exception\FileDirectoryException;

final class EnvDefuse
{


    public function __construct(
        private readonly string $fileEnv,
        private readonly CryptoDefuse $crypto,
    ) {
        if (!is_file($this->fileEnv)) {
            throw FileDirectoryException::fileNotFound($this->fileEnv);
        }
    }

    protected function readFileEnv(): array
    {

        $lines = file($this->fileEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $results = [];
        foreach ($lines as $line) {
            if (strpos(trim($line), '=') !== false) {
                list($name, $value) = explode('=', trim($line), 2);
                $results[$name] = $value;
            }
        }
        return $results;
    }

    public function encrypt(array $envData): bool
    {

        foreach ($envData as $key => $value) {
            $envData[$key] = $this->crypto->encrypted($value);
        }

        // Filters
        $envLoaders = array_filter($this->readFileEnv(), function ($key) use ($envData) {
            $envArgsKeys = array_keys($envData);
            return !in_array($key, $envArgsKeys);
        }, ARRAY_FILTER_USE_KEY);

        // Merge
        $envLoaders = array_merge($envLoaders, $envData);

        // Save env
        $content = array_map(fn($value, $key) => "{$key}={$value}", $envLoaders, array_keys($envLoaders));
        $content = implode(PHP_EOL, $content);
        return (bool)file_put_contents($this->fileEnv, $content);
    }
}
