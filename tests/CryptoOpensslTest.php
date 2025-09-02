<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Vigihdev\CryptoDev\CryptoOpenssl;

/**
 * CryptoOpensslTest
 *
 * Unit test untuk CryptoOpenssl class
 *
 * @author Vigih Dev
 */
class CryptoOpensslTest extends TestCase
{
    /**
     * @var string
     */
    private string $tempKeyFile;

    /**
     * @var string
     */
    private string $testKey;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create temporary key file
        $this->tempKeyFile = sys_get_temp_dir() . '/test_openssl_key_' . uniqid();
        $this->testKey = bin2hex(openssl_random_pseudo_bytes(32));
        file_put_contents($this->tempKeyFile, $this->testKey);
    }

    protected function tearDown(): void
    {
        // Clean up temporary files
        if (file_exists($this->tempKeyFile)) {
            unlink($this->tempKeyFile);
        }
        parent::tearDown();
    }

    /**
     * Test decrypt method dengan valid input
     */
    public function testDecryptWithValidInput(): void
    {
        $plaintext = 'Hello World OpenSSL Test';
        
        // Encrypt data manually untuk testing
        $key = hex2bin($this->testKey);
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($plaintext, 'AES-256-CBC', $key, 0, $iv);
        $ciphertext = base64_encode($iv . $encrypted);
        
        $result = CryptoOpenssl::decrypt($ciphertext, $this->tempKeyFile);
        
        $this->assertEquals($plaintext, $result);
    }

    /**
     * Test decrypt method dengan file tidak ada
     */
    public function testDecryptWithNonExistentFile(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('tidak tersedia');
        
        CryptoOpenssl::decrypt('dummy_ciphertext', '/path/that/does/not/exist');
    }

    /**
     * Test decrypt method dengan invalid ciphertext
     */
    public function testDecryptWithInvalidCiphertext(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error openssl decrypt');
        
        CryptoOpenssl::decrypt('invalid_base64_data', $this->tempKeyFile);
    }

    /**
     * Test decrypt method dengan empty ciphertext
     */
    public function testDecryptWithEmptyCiphertext(): void
    {
        $this->expectException(RuntimeException::class);
        
        CryptoOpenssl::decrypt('', $this->tempKeyFile);
    }
}
