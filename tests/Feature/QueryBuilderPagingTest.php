<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QueryBuilderPagingTest extends TestCase
{
    //Query Builder juga bisa melakukan paging atau melimit output dari query select, kita bisa menggunakan method take(number) untuk LIMIT dan skip(number) untuk OFFSET
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

    public function testTakeSkip()
    {
        $this->insertProducts();

        $collection = DB::table("categories")
            ->skip(2)
            ->take(2)
            ->get();

        self::assertCount(2, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }    
    }

    //lalu ketika kita melakukan query biasanya data tersebut di load ke dalam memory, jika data terlalu banyak bisa terjadi memory overload, kita bisa memotong
    //data hasil query nya secara bertahap dengan method chunk(berapaData, function) ini sama saja dengan paging, 
    //unutk menggunakan method ini kita perlu menambahakn ordering query nya juga
    public function testChunk()
    {
        $this->insertProducts();

        DB::table("categories")
            ->orderBy("id")
            ->chunk(1, function($categories) {//artinya setiap satu data akan di proses oleh function nya
                self::assertNotNull($categories);
                foreach ($categories as $category) {
                    LOg::info(json_encode($category));
                }
            });
    }

    //kadang Chunk Result sangat menyulitkan karena kita harus proses data nya satu persatu secara manual perchunk, untuk ini kita bisa bisa menggunakan Lazy chunk yang akan
    //menghasilkan Lazy Collection jadi yang data diambil di database akan bertahap, kita bisa gunakan method lazy(1) untuk mengaktifkan nya
    public function testLazyChunk()
    {
        $this->insertProducts();

        DB::table("categories")
            ->orderBy("id")
            ->lazy(1)
            ->each(function ($category) {
                self::assertNotNull($category);
                Log::info(json_encode($category));
            });
    }

    //ada cara lain untuk melakukan Paging Chunk Lazy yaitu dengan menggunakan Cursor, Cursor tidak melakukan Paging namun hanya akan melakukan query satu kali, lalu akan
    //mengambil datanya satu persatu menggunakan PDO::fetch, Cursor lebih hemat memory dari Chunk dan Lazy, kita bisa gunakan method cursor()
    public function testCursor()
    {
        $this->insertProducts();

        DB::table("categories")
            ->orderBy("id")
            ->cursor()
            ->each(function ($category) {
                self::assertNotNull($category);
                Log::info(json_encode($category));
            });
    }
}
