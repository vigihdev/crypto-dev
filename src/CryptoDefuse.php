<?php

declare(strict_types=1);

namespace Vigihdev\CryptoDev;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Vigihdev\CryptoDev\Exception\FileDirectoryException;
use Vigihdev\CryptoDev\Exception\KeyFileException;

/**
 * CryptoDefuse
 *
 * Utility class untuk Defuse crypto operations
 *
 * @author Vigih Dev
 */
final class CryptoDefuse
{


    public static function generateKey(string $fileKey): void
    {

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

    /**
     * Decrypt ciphertext using Defuse library
     *
     * Decrypt encrypted string menggunakan key dari file
     *
     * @param string $ciphertext Encrypted text yang akan didecrypt
     * @param string $keyFile Path ke file yang berisi Defuse key
     * @return string Decrypted plaintext
     * @throws FileDirectoryException
     */
    public static function decrypt(string $ciphertext, string $keyFile): string
    {

        if (!is_file($keyFile)) {
            throw FileDirectoryException::fileNotFound($keyFile);
        }

        $key = Key::loadFromAsciiSafeString(file_get_contents($keyFile));
        return Crypto::decrypt($ciphertext, $key);
    }


    public function __construct(
        private readonly string $fileKey
    ) {
        if (!is_file($this->fileKey)) {
            throw FileDirectoryException::fileNotFound($this->fileKey);
        }
    }

    private function getKey(): Key
    {
        return Key::loadFromAsciiSafeString(
            file_get_contents($this->fileKey)
        );
    }

    public function encrypted(string $value): string
    {
        return Crypto::encrypt($value, $this->getKey());
    }

    public function decrypted(string $ciphertext): string
    {
        return Crypto::decrypt($ciphertext, $this->getKey());
    }
}
