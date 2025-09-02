<?php

declare(strict_types=1);

namespace Vigihdev\CryptoDev;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Vigihdev\CryptoDev\Exception\FileDirectoryException;


final class CryptoDefuse
{


    public static function decrypt(string $ciphertext, string $keyFile): string
    {

        if (!is_file($keyFile)) {
            throw FileDirectoryException::fileNotFound($keyFile);
        }

        $key = Key::loadFromAsciiSafeString(file_get_contents($keyFile));
        return Crypto::decrypt($ciphertext, $key);
    }
}
