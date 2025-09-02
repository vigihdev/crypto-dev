<?php

declare(strict_types=1);

namespace Vigihdev\CryptoDev\Console;

/**
 * AbstractConsole
 *
 * Base abstract class untuk console operations crypto development
 *
 * @author Vigih Dev
 */
abstract class AbstractConsole
{
    /**
     * Generate encryption key
     *
     * @return void
     */
    abstract public static function generateKey(): void;

    /**
     * Write encrypted environment variables
     *
     * @return void
     */
    abstract public static function writeEnvEncrypt(): void;

    /**
     * Test decryption functionality
     *
     * @return void
     */
    abstract public static function testDecrypt(): void;


    /**
     * Constructor
     *
     * Initialize Generator with current working directory
     *
     * @param string $cwd Current working directory path
     */
    public function __construct(
        private readonly string $cwd
    ) {}

    /**
     * Read environment file content
     *
     * Parses .env file and returns key-value pairs as associative array
     *
     * @return array<string, string> Environment variables as key-value pairs
     */
    protected function readFileEnv(): array
    {

        $lines = file($this->getFileEnv(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $results = [];
        foreach ($lines as $line) {
            if (strpos(trim($line), '=') !== false) {
                list($name, $value) = explode('=', trim($line), 2);
                $results[$name] = $value;
            }
        }
        return $results;
    }

    /**
     * Get environment file path
     *
     * Returns path to .env file, creates if not exists
     *
     * @return string Path to .env file
     */
    protected function getFileEnv(): string
    {

        $env = $this->cwd . DIRECTORY_SEPARATOR . '.env';
        if (!is_file($env)) {
            touch($env);
        }
        return $env;
    }

    /**
     * Get secrets directory path
     *
     * Returns path to secrets directory, creates with 0700 permissions if not exists
     *
     * @return string Path to secrets directory
     */
    protected function getPathSecrets(): string
    {

        $secrets = $this->getPathConfig() . DIRECTORY_SEPARATOR . 'secrets';
        if (!is_dir($secrets)) {
            mkdir($secrets, 0700, true);
            chmod($secrets, 0700);
        }
        return $secrets;
    }

    /**
     * Get config directory path
     *
     * Returns path to config directory, creates if not exists
     *
     * @return string Path to config directory
     */
    protected function getPathConfig(): string
    {

        $config = $this->cwd . DIRECTORY_SEPARATOR . 'config';
        if (!is_dir($config)) {
            mkdir($config, 0777, true);
        }
        return $config;
    }

    /**
     * Get command line arguments
     *
     * Returns CLI arguments starting from index 3 (skipping script name and first 2 args)
     *
     * @return array<int, string> Command line arguments
     */
    protected function getArgs(): array
    {
        $argv = $_SERVER['argv'] ?? [];
        return array_slice($argv, 3);
    }
}
