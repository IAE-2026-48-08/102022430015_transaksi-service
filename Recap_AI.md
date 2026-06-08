Sesi 1 — Menjalankan Laravel di Docker
Prompt:

"aku lagi jalanin framework laravel menggunakan docker namun hasilnya seperti ini jika dicoba" (melampirkan screenshot error: Please provide a valid cache path)

Respons AI:
AI menjelaskan bahwa error InvalidArgumentException terjadi karena folder storage dan bootstrap/cache tidak memiliki permission yang benar di dalam container Docker.
Solusi yang diberikan:
bashchmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
php artisan cache:clear
Pelajaran:

Saat menjalankan Laravel di Docker, permission folder storage dan bootstrap/cache harus diatur dengan benar
Perintah Linux seperti chmod dan chown tidak bisa dijalankan di PowerShell Windows — harus masuk ke dalam container dulu dengan docker exec -it <nama_container> bash


Sesi 2 — Menemukan Nama Container
Prompt:

(Melampirkan screenshot error PowerShell karena menjalankan docker exec -it <nama_container> bash secara literal)

Respons AI:
AI menjelaskan bahwa <nama_container> harus diganti dengan nama container yang sebenarnya, dan memberikan perintah untuk mencarinya.
Solusi yang diberikan:
bashdocker ps
Pelajaran:

Selalu jalankan docker ps dulu untuk melihat nama container yang aktif
Nama container terlihat di kolom NAMES — dalam kasus ini: transaction-service-app
Perbedaan terminal PowerShell (Windows) vs terminal di dalam container sangat penting — prompt PS C:\> berarti masih di Windows, prompt root@...:/var/www# berarti sudah di dalam container


Sesi 3 — Error: Call to a member function make() on null
Prompt:

"In Command.php line 173: Call to a member function make() on null"

Respons AI:
AI menjelaskan bahwa Laravel Application Container belum ter-inisialisasi dengan benar, kemungkinan karena file .env bermasalah atau APP_KEY belum di-set.
Solusi yang diberikan:
bashphp artisan key:generate
composer dump-autoload
Pelajaran:

Error make() on null biasanya berarti Laravel tidak bisa boot — penyebab umum: APP_KEY kosong, bootstrap/providers.php tidak ada, atau bootstrap/app.php bermasalah
File bootstrap/providers.php adalah file wajib di Laravel 12 yang mendaftarkan semua Service Provider


Sesi 4 — Swagger: Failed to load API definition
Prompt:

(Melampirkan screenshot Swagger UI dengan error "Failed to load API definition. Fetch error Not Found")

Respons AI:
AI menjelaskan bahwa file api-docs.json belum di-generate oleh L5-Swagger.
Solusi yang diberikan:
bashphp artisan l5-swagger:generate
Pelajaran:

Swagger UI membutuhkan file JSON yang di-generate dari anotasi @OA\ di controller
Setiap kali ada perubahan pada anotasi Swagger, harus generate ulang dengan php artisan l5-swagger:generate
Anotasi @OA\Info, @OA\SecurityScheme, dan @OA\Server harus ada di controller utama


Sesi 5 — GraphQL Playground: 404 Not Found
Prompt:

(Melampirkan screenshot localhost:8080/graphql-playground yang menampilkan 404)

Respons AI:
AI menjelaskan bahwa route /graphql-playground belum terdaftar di routes/web.php.
Solusi yang diberikan:
php// Tambahkan di routes/web.php
Route::get('/graphql-playground', function () {
    return response()->make('<html>...GraphQL Playground HTML...</html>', 200, 
        ["Content-Type" => "text/html"]);
});
Pelajaran:

Lighthouse tidak otomatis membuat route playground di semua versi
Bisa membuat playground sendiri menggunakan HTML dengan CDN graphql-playground-react
Route web dan route API terpisah — playground masuk ke routes/web.php bukan routes/api.php


Sesi 6 — GraphQL Error: Table cache doesn't exist
Prompt:

(Melampirkan screenshot GraphQL error: SQLSTATE[42S02] Table 'transaction_db.cache' doesn't exist)

Respons AI:
AI menjelaskan bahwa Lighthouse menggunakan database cache secara default, tapi tabel cache belum dibuat.
Solusi yang diberikan:
sql-- Buat tabel cache manual via MySQL
CREATE TABLE IF NOT EXISTS cache (
  `key` VARCHAR(255) NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
);
Atau ubah cache driver di .env:
CACHE_DRIVER=file
Pelajaran:

Lighthouse memiliki fitur caching schema yang membutuhkan tabel database
Jika tidak ingin menggunakan database cache, set CACHE_DRIVER=file di .env
Tabel bisa dibuat manual via MySQL jika php artisan migrate tidak bisa dijalankan


Sesi 7 — GraphQL Error: Table transactions doesn't exist
Prompt:

(Melampirkan screenshot GraphQL error: Table 'transaction_db.transactions' doesn't exist)

Respons AI:
AI menjelaskan bahwa migration gagal dijalankan karena php artisan migrate selalu error, sehingga tabel belum terbuat.
Solusi yang diberikan:
sql-- Buat tabel manual via MySQL
CREATE TABLE IF NOT EXISTS transactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  account_id VARCHAR(255) NOT NULL,
  type ENUM('credit','debit') NOT NULL,
  amount DECIMAL(15,2) NOT NULL,
  description TEXT,
  reference_number VARCHAR(255),
  transaction_date DATETIME,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
Pelajaran:

Jika artisan tidak bisa dijalankan, tabel bisa dibuat manual langsung di MySQL
Akses MySQL di Docker: docker exec -it transaction-service-db mysql -ularavel -psecret transaction_db
Struktur tabel harus sesuai dengan Model Laravel yang digunakan


Sesi 8 — Fix Entrypoint Docker (Root Cause)
Prompt:

(Melampirkan screenshot error berulang saat restart container)

Respons AI:
AI menemukan root cause utama — file docker/entrypoint.sh menjalankan composer dump-autoload --optimize tanpa flag --no-scripts, sehingga setiap kali container restart selalu menjalankan php artisan package:discover yang gagal.
Solusi yang diberikan:
bash# Ubah di docker/entrypoint.sh
# Dari:
composer dump-autoload --optimize
# Menjadi:
composer dump-autoload --optimize --no-scripts
Kemudian rebuild:
bashdocker compose down
docker compose up --build -d
Pelajaran:

Flag --no-scripts pada composer mencegah script post-autoload (termasuk package:discover) dijalankan
Selalu cek docker/entrypoint.sh untuk memahami apa yang terjadi saat container start
docker logs <container_name> --tail 50 sangat membantu untuk debug masalah container


Rangkuman Teknik Prompting yang Efektif
1. Sertakan Screenshot atau Pesan Error Lengkap
Prompt yang baik menyertakan pesan error secara lengkap, bukan hanya "ada error". Contoh yang baik:

"Muncul error: SQLSTATE[42S02]: Base table or view not found: 1146 Table 'transaction_db.cache' doesn't exist"

2. Berikan Konteks Lingkungan
Selalu sebutkan environment yang digunakan:

"Saya menggunakan Laravel 12 dengan Docker, PHP 8.4, MySQL 8.0"

3. Tunjukkan yang Sudah Dicoba
Ini membantu AI tidak memberikan solusi yang sama:

"Sudah mencoba php artisan cache:clear tapi masih error"

4. Tanya Satu Masalah Per Sesi
Prompt yang fokus pada satu masalah menghasilkan jawaban yang lebih tepat daripada menggabungkan banyak pertanyaan sekaligus.
5. Minta Penjelasan, Bukan Hanya Solusi

"Kenapa error ini bisa terjadi dan bagaimana cara mengatasinya?"

Ini membantu memahami konsep, bukan hanya copy-paste solusi.

Tools yang Digunakan

AI Assistant: Claude (Anthropic)
Framework: Laravel 12
Container: Docker + Docker Compose
Database: MySQL 8.0
GraphQL: Lighthouse v6
API Docs: L5-Swagger
