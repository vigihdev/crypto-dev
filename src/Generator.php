<?php

declare(strict_types=1);

namespace Vigihdev\CryptoDev;

/**
 * Generator
 *
 * Handles cryptographic key generation and environment variable encryption
 *
 * @author Vigih Dev
 */
final class Generator
{

    /**
     * Generate cryptographic key via CLI
     *
     * Creates a 32-byte random key and saves it to secrets directory
     *
     * @return void
     */
    public static function cliKey(): void
    {

        $self = new self(getcwd());

        // CryptoOpenssl
        // Generate kunci 32 byte random
        $key = openssl_random_pseudo_bytes(32);
        $fileKey = $self->getPathSecrets() . DIRECTORY_SEPARATOR . '.key';

        // Simpan ke file (format HEX)
        if (file_put_contents($fileKey, bin2hex($key))) {
            chmod($fileKey, 0600);
            echo "🔑 Kunci berhasil disimpan di: " . $fileKey . PHP_EOL;
        } else {
            echo "🔑 Kunci gagal disimpan di: " . $fileKey . PHP_EOL;
        }
    }

    /**
     * Encrypt environment variables via CLI
     *
     * Encrypts command line arguments and updates .env file with encrypted values
     *
     * @return void
     */
    public static function cliEnv(): void
    {

        $self = new self(getcwd());
        $key = $self->getPathSecrets() . DIRECTORY_SEPARATOR . '.key';

        if (!is_file($key)) {
            return;
        }

        $keyHex = file_get_contents($key);
        $fileEnv = $self->getFileEnv();

        $envArgs = [];
        array_map(function ($arg) use ($keyHex, &$envArgs) {
            $arg = preg_replace('/^--/', '', $arg);
            $envs = explode('=', $arg, 2);
            $key = $envs[0] ?? null;
            $value = $envs[1] ?? null;

            // openssl_encrypt
            $keySsl = hex2bin($keyHex);
            $iv = openssl_random_pseudo_bytes(16);
            $encrypted = openssl_encrypt($value, 'AES-256-CBC', $keySsl, 0, $iv);
            $encrypted = base64_encode($iv . $encrypted);

            $envArgs[$key] = $encrypted;
        }, $self->getArgs());


        // Filters
        $envLoaders = array_filter($self->readFileEnv(), function ($key) use ($envArgs) {
            $envArgsKeys = array_keys($envArgs);
            return !in_array($key, $envArgsKeys);
        }, ARRAY_FILTER_USE_KEY);


        // Merge
        $envLoaders = array_merge($envLoaders, $envArgs);

        // Save env
        $content = array_map(fn($value, $key) => "{$key}={$value}", $envLoaders, array_keys($envLoaders));
        $content = implode(PHP_EOL, $content);
        file_put_contents($fileEnv, $content);

        // Message
        printf("Env berhasil di Update %s \n", "");
    }

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
