### Panduan Membuat Unit Test

- `@workspace/tests`
- Gunakan `test.html` sebagai `Document`
- Nama class Test selalu nama class **Test** contoh jika mau test class DaftarHargaBackup nama testnya **DaftarHargaBackupTest**
- Gunakan namespace `Tests` untuk semua file unit tests.
- Gunakan `PHPUnit\Framework\TestCase` sebagai parent class.
- Buat tests untuk semua public method.
- Gunakan class `PHPUnit\Framework\Assert`.
- Ikuti standar **PSR-12** untuk format kode.
- Sertakan contoh pengujian dengan input dan output yang bervariasi, termasuk _edge case_.
- Jika testMethod sudah ada lewati aja
