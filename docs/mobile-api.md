# Mobile API Flutter

Base URL produksi: `https://intankemilau.com/api`.

Untuk aplikasi Flutter yang terhubung ke website live, gunakan domain produksi ini:

```dart
class ApiConfig {
  static const String baseUrl = 'https://intankemilau.com/api';
}
```

Tidak perlu memakai `http://127.0.0.1:8000/api`, `http://10.0.2.2:8000/api`, atau menjalankan `php artisan serve` jika APK Flutter memang akan sinkron dengan website hosting. Alamat lokal hanya dipakai saat pengembangan di komputer sendiri.

## Istilah

Website dan aplikasi Flutter akan berkomunikasi lewat API. Itu disebut integrasi. Data yang dibuat di Flutter tersimpan ke database website yang sama, jadi admin di website bisa langsung melihat laporan dari teknisi.

## Login

`POST /api/login`

Body JSON:

```json
{
  "username": "teknisi",
  "password": "password",
  "device_name": "android"
}
```

Response berisi `access_token`. Simpan token ini di Flutter, lalu kirim di endpoint berikutnya:

```http
Authorization: Bearer TOKEN_DARI_LOGIN
```

## Data Sinkron

`GET /api/teknisi/sync`

Mengambil data master untuk form Flutter:

- `rumah_sakits`
- `ruangans`
- `ac_units`
- `pemeriksaan_default`
- `pemeriksaan_siloam_baru`

## Kirim Laporan Service

`POST /api/teknisi/reports`

Gunakan `multipart/form-data` karena ada upload foto.

Field utama:

- `rumah_sakit_id`
- `ruangan_id`
- `gedung` isi `Baru`, `Lama`, atau kosong
- `merk_ac`
- `type_ac`
- `tanggal_service` format `YYYY-MM-DD`
- `saran`
- `nama_penerima`

Field item:

- `items[1][is_normal]`
- `items[1][keterangan]`
- ulangi sampai nomor item pemeriksaan terakhir

Field foto:

- `general_photos[]`
- `item_photos[1][]` untuk foto item nomor 1 yang tidak normal

## Riwayat Laporan

`GET /api/teknisi/reports`

`GET /api/teknisi/reports/{id}`

Endpoint ini hanya mengembalikan laporan milik teknisi yang sedang login.

## Logout

`POST /api/logout`

Menghapus token perangkat yang sedang dipakai.
