- [Backend](#backend)
  * [Requirements](#requirements)
  * [Installation](#installation)
  * [Configuration](#configuration)
  * [Publish sanctum providers](#publish-sanctum-providers)
  * [Database migration](#database-migration)
  * [Running](#running)
  * [Usage](#usage)
  * [Points](#points)

# Backend

## Requirements

- PHP ^7.4
- MySQL ^5.7
- Composer

## Installation

Run command berikut untuk menginstall package-package composer:
```
composer install
```

## Configuration

Set nilai pada environment variables di file .env untuk APP_NAME dan DB_* untuk konfigurasi DB.

## Publish sanctum providers

Run command berikut untuk publish sanctum provider yang digunakan pada project:
```
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

## Database migration

Run command berikut untuk melakukan migrasi database
```
php artisan migrate
```

## Running
Untuk menjalankan aplikasi, run command
```
php artisan serve
```

untuk menjalankan web server PHP CLI.

## Usage

Berikut tautan dokumentasi lengkap penggunaan RestAPI https://documenter.getpostman.com/view/11989055/2s935oKibJ

1. Get token

    Aplikasi ini menggunakan CSRF token untuk keamanan, ikuti tautan berikut untuk mendapatkan CSRF token.

    https://documenter.getpostman.com/view/11989055/2s935oKibJ#d7e5a9ec-daf6-4cb7-aba2-36c8fcb021a2

2. Register

    Gunakan header X-CSRF-TOKEN dengan token yang sudah didapatkan pada step 1. Lalu ikuti tautan berikut untuk melakukan registrasi akun.

    - Register Admin

        https://documenter.getpostman.com/view/11989055/2s935oKibJ#9c64952d-825c-4222-b2ea-264e8fb677a5

    - Register User

        https://documenter.getpostman.com/view/11989055/2s935oKibJ#1c226301-6d98-41d4-8244-19518e7ac893

    Apabila sudah berhasil registrasi maka otomatis sudah login.

3. Cek Login

    Untuk mengecek apakah user masih login dapat ikuti tautan berikut.

    https://documenter.getpostman.com/view/11989055/2s935oKibJ#36fc071f-fc9c-4c88-8938-db3e1e6153fb

## Points
1. Terdapat 2 role, admin dan user

    Berikut tautan dokumentasi https://documenter.getpostman.com/view/11989055/2s935oKibJ#59a83140-53dc-4b69-ad44-7b9f2a95bc24

2. Admin dapat menambahkan Product dengan detail
    - SKU
    - Nama Product
    - Harga Satuan

    Berikut tautan dokumentasi https://documenter.getpostman.com/view/11989055/2s935oKibJ#7ba239d9-434d-4a52-a46b-c807613e2df6

3. Admin dapat menambahkan Stock dengan membuat Purchase Order dengan detail
    - Invoice Number
    - List Pembelian Product dengan detail
        - Quantity
        - Harga Satuan
        
    Berikut tautan dokumentasi https://documenter.getpostman.com/view/11989055/2s935oKibJ#9c7357b4-999a-4073-827e-7627b90d9bb2
4. User dapat melihat list Product beserta stock nya

    Berikut tautan dokumentasi https://documenter.getpostman.com/view/11989055/2s935oKibJ#d5314c5f-7527-42e6-ae5a-3f15764d76e3

5. User dapat membuat Pending Sale Order dengan detail
    - Customer
    - List Product dengan detail
        - Quantity
        - Harga Satuan

    Berikut tautan dokumentasi https://documenter.getpostman.com/view/11989055/2s935oKibJ#4c53b46d-8c17-440e-82d7-d31a45b7ce53

6. Admin dapat mengapprove Pending Sale Order menjadi Sale Order

    Berikut tautan dokumentasi https://documenter.getpostman.com/view/11989055/2s935oKibJ#7a18f829-da0d-4818-861e-7a24f528fdcf

7. Admin juga dapat membuat langsung Sale Order dengan detail
    - Invoice Number
    - Customer
    - List Product dengan detail
        - Quantity
        - Harga Satuan
    
    Berikut tautan dokumentasi https://documenter.getpostman.com/view/11989055/2s935oKibJ#4c53b46d-8c17-440e-82d7-d31a45b7ce53
