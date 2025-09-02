<?php

declare(strict_types=1);

namespace Vigihdev\CryptoDev\Console;

use InvalidArgumentException;
use Vigihdev\CryptoDev\CryptoOpenssl;
use Vigihdev\CryptoDev\Exception\FileDirectoryException;


final class CryptoOpensslConsole extends AbstractConsole
{

    public static function generateKey(): void
    {

        $self = new self();
        if (!is_dir($self->getPathSecrets())) {
            throw FileDirectoryException::directoryNotFound($self->getPathSecrets());
        }

        // Generate kunci 32 byte random
        $key = openssl_random_pseudo_bytes(32);
        $fileKey = $self->getPathSecrets() . DIRECTORY_SEPARATOR . '.key';

        // Simpan ke file (format HEX)
        if ((bool)file_put_contents($fileKey, bin2hex($key))) {
            chmod($fileKey, 0600);
            echo "🔑 Kunci berhasil disimpan di: " . $fileKey . PHP_EOL;
        } else {
            echo "🔑 Kunci gagal disimpan di: " . $fileKey . PHP_EOL;
        }
    }

    public static function writeEnvEncrypt(): void
    {

        $self = new self();
        $key = $self->getPathSecrets() . DIRECTORY_SEPARATOR . '.key';

        if (!is_file($key)) {
            throw FileDirectoryException::fileNotFound($key);
        }

        $keyHex = file_get_contents($key);
        $fileEnv = $self->getFileEnv();

        $envArgs = [];
        array_map(function ($arg) use ($keyHex, &$envArgs) {
            $arg = preg_replace('/^--/', '', $arg);
            $envs = explode('=', $arg, 2);
            $key = $envs[0] ?? null;
            $value = $envs[1] ?? null;

            if ($value && $key) {
                // openssl_encrypt
                $keySsl = hex2bin($keyHex);
                $iv = openssl_random_pseudo_bytes(16);
                $encrypted = openssl_encrypt($value, 'AES-256-CBC', $keySsl, 0, $iv);
                $encrypted = base64_encode($iv . $encrypted);

                $envArgs[$key] = $encrypted;
            }
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


    public static function testDecrypt(): void
    {
        $self = new self();
        $key = $self->getPathSecrets() . DIRECTORY_SEPARATOR . '.key';

        if (!is_file($key)) {
            throw FileDirectoryException::fileNotFound($key);
        }

        $arg = $self->getArgs();
        $arg = current($arg);

        if (!is_string($arg)) {
            throw new InvalidArgumentException('Argument Not String');
        }

        echo CryptoOpenssl::decrypt($arg, $key) . PHP_EOL;
    }


    public function __construct()
    {
        parent::__construct(
            getcwd()
        );
    }
}
