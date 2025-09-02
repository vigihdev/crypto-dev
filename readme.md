# Crypto Dev

Library PHP untuk enkripsi dan dekripsi menggunakan OpenSSL dan Defuse Crypto.

## Features

- ✅ OpenSSL encryption/decryption
- ✅ Defuse Crypto encryption/decryption  
- ✅ Console commands untuk key generation
- ✅ Environment variable encryption
- ✅ Secure key storage

## Installation

```bash
composer install
```

## Usage

### Generate Keys

```bash
# Generate OpenSSL key
php console.php openssl generateKey

# Generate Defuse key  
php console.php defuse generateKey
```

### Encrypt Environment Variables

```bash
# OpenSSL
php console.php openssl writeEnvEncrypt --DB_PASSWORD=secret123

# Defuse
php console.php defuse writeEnvEncrypt --API_KEY=myapikey
```

### Test Decryption

```bash
# OpenSSL
php console.php openssl testDecrypt "encrypted_string"

# Defuse  
php console.php defuse testDecrypt "encrypted_string"
```

## Directory Structure

```
src/
├── Console/           # Console commands
├── Exception/         # Custom exceptions
├── CryptoOpenssl.php  # OpenSSL crypto class
└── CryptoDefuse.php   # Defuse crypto class

config/
└── secrets/          # Key storage (auto-created)

tests/                # Unit tests
```

## Security

- Keys disimpan dengan permission 0600
- Secrets directory dengan permission 0700
- Gunakan environment variables untuk sensitive data

## Requirements

- PHP 8.0+
- OpenSSL extension
- Defuse/php-encryption

## Author

Vigih Dev
