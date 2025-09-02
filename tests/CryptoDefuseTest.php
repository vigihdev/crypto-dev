<?php

declare(strict_types=1);

namespace Tests;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use PHPUnit\Framework\TestCase;
use Vigihdev\CryptoDev\CryptoDefuse;
use Vigihdev\CryptoDev\Exception\FileDirectoryException;

/**
 * CryptoDefuseTest
 *
 * Unit test untuk CryptoDefuse class
 *
 * @author Vigih Dev
 */
class CryptoDefuseTest extends TestCase
{
    /**
     * @var string
     */
    private string $tempKeyFile;

    /**
     * @var Key
     */
    private Key $testKey;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create temporary key file
        $this->tempKeyFile = sys_get_temp_dir() . '/test_defuse_key_' . uniqid();
        $this->testKey = Key::createNewRandomKey();
        file_put_contents($this->tempKeyFile, $this->testKey->saveToAsciiSafeString());
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
        $plaintext = 'Hello World Test';
        $ciphertext = Crypto::encrypt($plaintext, $this->testKey);
        
        $result = CryptoDefuse::decrypt($ciphertext, $this->tempKeyFile);
        
        $this->assertEquals($plaintext, $result);
    }

    /**
     * Test decrypt method dengan file tidak ada
     */
    public function testDecryptWithNonExistentFile(): void
    {
        $this->expectException(FileDirectoryException::class);
        
        CryptoDefuse::decrypt('dummy_ciphertext', '/path/that/does/not/exist');
    }

    /**
     * Test decrypt method dengan empty ciphertext
     */
    public function testDecryptWithEmptyCiphertext(): void
    {
        $this->expectException(\Exception::class);
        
        CryptoDefuse::decrypt('', $this->tempKeyFile);
    }
}
