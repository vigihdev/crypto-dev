<?php

declare(strict_types=1);

namespace Vigihdev\CryptoDev;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Exception;
use Vigihdev\CryptoDev\Exception\FileDirectoryException;


final class CryptoDefuse
{


    public static function createNewRandomKey(string $pathKey): bool
    {

        if (!is_dir($pathKey)) {
            throw new Exception("Error {$pathKey} tidak tersedia", 1);
        }

        $key = Key::createNewRandomKey();
        $content = $key->saveToAsciiSafeString();
        $filename = $pathKey . DIRECTORY_SEPARATOR . '.defuse.key';

        if ((bool)file_put_contents($filename, $content)) {
            chmod($filename, 0600);
            return true;
        }

        throw new Exception("Error gagal menyimpan file {$filename}", 1);
    }

    public static function encryptFromArray(array $data, string $keyFile): array
    {

        if (!is_file($keyFile)) {
            throw FileDirectoryException::fileNotFound($keyFile);
        }

        $key = Key::loadFromAsciiSafeString(file_get_contents($keyFile));
        foreach ($data as $name => $value) {
            $value = Crypto::encrypt($value, $key);
            $data[$name] = $value;
        }

        return $data;
    }

    public static function encryptEnv(string $plaintext, string $key) {}
    public static function encrypt(string $plaintext, string $key) {}

    public static function decrypt(string $ciphertext, string $keyFile): string
    {

        if (!is_file($keyFile)) {
            throw FileDirectoryException::fileNotFound($keyFile);
        }

        $key = Key::loadFromAsciiSafeString(file_get_contents($keyFile));
        return Crypto::decrypt($ciphertext, $key);
    }
}
