<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM products");
        DB::delete("DELETE FROM categories");
    }

    public function testWhere()
    {
        DB::table("categories")
            ->insert(["id" => "SMARTPHONE", "name" => "Smartphone", "created_at" => "2025-10-10 10:10:10"]);
        DB::table("categories")
            ->insert(["id" => "FOOD", "name" => "Food", "created_at" => "2025-10-10 10:10:10"]);
        DB::table("categories")
            ->insert(["id" => "LAPTOP", "name" => "Laptop", "created_at" => "2025-10-10 10:10:10"]);
        DB::table("categories")
            ->insert(["id" => "FASHION", "name" => "Fashion", "created_at" => "2025-10-10 10:10:10"]); 

        $collection = DB::table("categories")->select(["id", "name"])->get();
        self::assertNotNull($collection);
    }

    public function insertProducts()
    {
        $this->testWhere();

        DB::table("products")->insert([
            "id" => "1",
            "name" => "Iphone 14 Pro Max",
            "category_id" => "SMARTPHONE",
            "price" => 20000000
        ]);
        DB::table("products")->insert([
            "id" => "2",
            "name" => "SAMSUNG Galaxy s21 Ultra",
            "category_id" => "SMARTPHONE",
            "price" => 18000000
        ]);
    }

    //saat kita buat aplikasi web atau restfulApi yang mengembalikan data di database, kita sering memberi informasi tentang pagination, misal jumlah record, jumlah page,
    //page saat ini, dan lain-lain, laravel punya fitur pagination kita bisa gunakan method paginate() dan secara otoamtis akan mengembalikan object LengthAwarePagination()
    //https://laravel.com/api/10.x/Illuminate/Database/Query/Builder.html#method_paginate 
    //https://laravel.com/api/10.x/Illuminate/Contracts/Pagination/LengthAwarePaginator.html 

    public function testPagination()
    {
        $this->insertProducts();

        $paginate = DB::table("categories")->paginate(2);

        self::assertEquals(1, $paginate->currentPage()); //currentPage
        self::assertEquals(2, $paginate->perPage()); //itemsPerPage
        self::assertEquals(2, $paginate->lastPage()); //lastPage
        self::assertEquals(4, $paginate->total());

        $collection = $paginate->items();
        self::assertCount(2, $collection);
        foreach ($collection as $item) {
            Log::info(json_encode($item));
        }
    }

    //kita bisa melakukan iterasi semua page dengan cara menaikkan nilai dari parameter page dari 1 sampai page terakhir
    public function testPageIteration()
    {
        $this->insertProducts();

        $page = 1;
        while (true) {
            $paginate = DB::table("categories")->paginate(perPage: 2, page: $page);
            if ($paginate->isEmpty()) {
                break;
            } else {
                $page++;
                foreach ($paginate->items() as $item) {
                    self::assertNotNull($item);
                    Log::info(json_encode($item));
                }
            }
        }
    }

    //lalu karena pagination menggunakan query limit offset kita bisa lihat terlebih dahulu masalahnya di chapter 168, salah satu cara untuk optimisasi pagination adalah 
    //menggunakan SEARCH AFTER dimana kita tidak menggunakan page number melainkan menampilkan data setelah data terakhir yang kita lihat, kita tidak melakukan offset dengan
    //metode ini, https://mariadb.com/kb/en/pagination-optimization/ 

    //namun search after juga punya kekurangan yaitu tidak bisa loncat ke satu page ke page lainnya, karena query nya harus selalu harus diubah, cursor pagination harus
    //melakukan sort dan filter berdasarkan sati kolom yang unique, misal primary key, cuma bisa nextPage dan perviousPage

    //untuk implement search after kita tidak perlu secara manual kita bisa gunakan method cursorPaginate() balikan nya adalah CursorPaginator
    //https://laravel.com/api/10.x/Illuminate/Database/Query/Builder.html#method_cursorPaginate 
    //https://laravel.com/api/10.x/Illuminate/Contracts/Pagination/CursorPaginator.html 
    public function testCursorPaginate() 
    {
        $this->insertProducts();

        $cursor = "id";
        while (true) {
            $paginate = DB::table("categories")->orderBy("id")->cursorPaginate(perPage: 2, cursor: $cursor);

            foreach ($paginate->items() as $item) {
                self::assertNotNull($item);
                Log::info(json_encode($item));
            }

            $cursor = $paginate->nextCursor();
            if ($cursor == null) {
                break;
            }
        }
    }

}
