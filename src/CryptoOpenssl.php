<?php

declare(strict_types=1);

namespace Vigihdev\CryptoDev;

use Exception;
use RuntimeException;

/**
 * CryptoOpenssl
 *
 * Handles OpenSSL encryption and decryption operations using AES-256-CBC
 *
 * @author Vigih Dev
 */
final class CryptoOpenssl
{

    /**
     * Decrypt encrypted data using AES-256-CBC
     *
     * Decrypts base64 encoded ciphertext using key from file with AES-256-CBC algorithm
     *
     * @param string $ciphertext Base64 encoded encrypted data
     * @param string $keyFile Path to file containing hex encoded key
     * @return string Decrypted plaintext data
     * @throws Exception When key file is not available
     * @throws RuntimeException When decryption fails
     */
    public static function decrypt(string $ciphertext, string $keyFile): string
    {

        if (!is_file($keyFile)) {
            throw new Exception("Error {$keyFile} tidak tersedia", 1);
        }

        $keyHex = file_get_contents($keyFile);
        $key = hex2bin($keyHex);

        $decoded = base64_decode($ciphertext);
        $iv = substr($decoded, 0, 16);
        $data = substr($decoded, 16);
        $decrypted = openssl_decrypt($data, 'AES-256-CBC', $key, 0, $iv);

        if (!is_string($decrypted)) {
            throw new RuntimeException("Error openssl decrypt", 1);
        }

        return $decrypted;
    }
}
