<?php

declare(strict_types=1);

namespace Vigihdev\CryptoDev;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Vigihdev\CryptoDev\Exception\FileDirectoryException;

/**
 * CryptoDefuse
 *
 * Utility class untuk Defuse crypto operations
 *
 * @author Vigih Dev
 */
final class CryptoDefuse
{
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
}
