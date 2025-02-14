<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MigrationTest extends TestCase
{
    //untuk membuat file Database Migration baru kita bisa gunakan command php artisan make:migration namafile, secara otomatis akan dibuatkan file php yang digunakan
    //unutk melakukan perubahan schema di database folder database/migrations, untuk membuat perubahan schema kita bisa gunakan Schema Builder
    //https://laravel.com/api/10.x/Illuminate/Support/Facades/Schema.html
    //https://laravel.com/api/10.x/Illuminate/Database/Schema/Blueprint.html 

    //Schema Builder mendukung banyak tipe data, https://laravel.com/docs/10.x/migrations#available-column-types 

    //setelah file migration dibuat kita bisa menjalankan nya, kita bisa gunakan, 
    //php artisan migrate:status unutk melihat status
    //dan unutk menjalankan file migration kita bisa gunakan
    //php artisan migrate

    //setelah migration dijalankan status file yang pernah dijalankan akan disimpan di table migration, jika file sudah dijalankan maka tidak ada gunanya lagi dijalankan
    //jika mau buat perubahan lagi silahkan buat file migration lagi

    //ketika kita misal membuast kesalahahn kita bisa merollback file migration nya, kita harus tentukan berapa jumlah file migration yang akan di rollback
    //kita bisa gunakan
    //php artisan migrate:rollback --step=jumlah, jumlah berisi angka jumlah file migration yang akan di rollback
    //dan yang akan dipanggil adalah method down pada file migration

}
