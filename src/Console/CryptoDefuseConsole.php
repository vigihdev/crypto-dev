<?php

declare(strict_types=1);

namespace Vigihdev\CryptoDev\Console;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use InvalidArgumentException;
use Vigihdev\CryptoDev\CryptoDefuse;
use Vigihdev\CryptoDev\Exception\FileDirectoryException;
use Vigihdev\CryptoDev\Exception\KeyFileException;


final class CryptoDefuseConsole extends AbstractConsole
{

    public static function generateKey(): void
    {

        $self = new self();
        if (!is_dir($self->getPathSecrets())) {
            throw FileDirectoryException::directoryNotFound($self->getPathSecrets());
        }

        $fileKey = $self->getPathSecrets() . DIRECTORY_SEPARATOR . '.defuse.key';

        try {
            $key = Key::createNewRandomKey();
            file_put_contents($fileKey, $key->saveToAsciiSafeString());
            chmod($fileKey, 0600);
            echo "🔑 Kunci berhasil disimpan di: " . $fileKey . PHP_EOL;
        } catch (KeyFileException $e) {
            echo "🔑 Kunci gagal disimpan di: " . $fileKey . PHP_EOL;
            echo $e->getMessage() . PHP_EOL;
        }
    }

    public static function writeEnvEncrypt(): void
    {

        $self = new self();
        $key = $self->getPathSecrets() . DIRECTORY_SEPARATOR . '.defuse.key';

        if (!is_file($key)) {
            throw FileDirectoryException::fileNotFound($key);
        }

        $key = file_get_contents($key);
        $keyHex = Key::loadFromAsciiSafeString($key);
        $fileEnv = $self->getFileEnv();

        $envArgs = [];
        array_map(function ($arg) use ($keyHex, &$envArgs) {
            $arg = preg_replace('/^--/', '', $arg);
            $envs = explode('=', $arg, 2);
            $key = $envs[0] ?? null;
            $value = $envs[1] ?? null;

            if ($key && $value) {
                // Crypto encrypt
                $encrypted = Crypto::encrypt($value, $keyHex);
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
        $key = $self->getPathSecrets() . DIRECTORY_SEPARATOR . '.defuse.key';

        if (!is_file($key)) {
            throw FileDirectoryException::fileNotFound($key);
        }

        $arg = $self->getArgs();
        $arg = current($arg);

        if (!is_string($arg)) {
            throw new InvalidArgumentException('Argument Not String');
        }

        echo CryptoDefuse::decrypt($arg, $key) . PHP_EOL;
    }

    public function __construct()
    {
        parent::__construct(
            getcwd()
        );
    }
}
