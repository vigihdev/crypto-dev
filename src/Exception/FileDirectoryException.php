<?php

declare(strict_types=1);

namespace Vigihdev\CryptoDev\Exception;

use Exception;

/**
 * FileDirectoryException
 *
 * Exception thrown when file or directory operations fail in crypto operations
 *
 * @author Vigih Dev
 */
class FileDirectoryException extends Exception
{
    /**
     * Create exception for file not found
     *
     * @param string $filePath Path to the missing file
     * @return self
     */
    public static function fileNotFound(string $filePath): self
    {
        return new self("File tidak ditemukan: {$filePath}");
    }

    /**
     * Create exception for directory not found
     *
     * @param string $dirPath Path to the missing directory
     * @return self
     */
    public static function directoryNotFound(string $dirPath): self
    {
        return new self("Directory tidak ditemukan: {$dirPath}");
    }

    /**
     * Create exception for invalid file permissions
     *
     * @param string $filePath Path to the file with invalid permissions
     * @return self
     */
    public static function invalidPermissions(string $filePath): self
    {
        return new self("File tidak memiliki permission yang valid: {$filePath}");
    }

    /**
     * Create exception for invalid argument path
     *
     * @param string $argument Invalid argument provided
     * @return self
     */
    public static function invalidArgument(string $argument): self
    {
        return new self("Argument path tidak valid: {$argument}");
    }
}
