<?php

declare(strict_types=1);

namespace Vigihdev\CryptoDev\Exception;

use Exception;

/**
 * KeyFileException
 *
 * Exception thrown when cryptographic key file operations fail
 *
 * @author Vigih Dev
 */
class KeyFileException extends Exception
{
    /**
     * Create exception for missing key file
     *
     * @param string $keyPath Path to the missing key file
     * @return self
     */
    public static function keyFileNotFound(string $keyPath): self
    {
        return new self("Key file tidak tersedia: {$keyPath}");
    }

    /**
     * Create exception for invalid key format
     *
     * @param string $keyPath Path to the invalid key file
     * @return self
     */
    public static function invalidKeyFormat(string $keyPath): self
    {
        return new self("Format key file tidak valid: {$keyPath}");
    }

    /**
     * Create exception for key generation failure
     *
     * @param string $reason Reason for generation failure
     * @return self
     */
    public static function keyGenerationFailed(string $reason): self
    {
        return new self("Gagal generate key: {$reason}");
    }
}
