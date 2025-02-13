<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QueryBuilderOrderingTest extends TestCase
{
    //kita juga bisa melakukan Ordering dengan Query Builder dengan method orderBy(column, order) order bisa asc atau desc
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

    public function testOrdering()
    {
        $this->insertProducts();

        $collection = DB::table("products")
            ->orderBy("price", "desc")
            ->orderBy("name", "asc")
            ->get();

        self::assertCount(2, $collection);
        for ($i = 0; $i < count($collection); $i++) {
           Log::info(json_encode($collection[$i]));
        }    
    }
}
