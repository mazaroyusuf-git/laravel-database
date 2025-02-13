<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QueryBuilderJoinTest extends TestCase
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

    //kita juga bisa melakukan join di laravel dengan join(), leftJoin(), rightJoin(), dan crossJoin()
    public function testJoin()
    {
        $this->insertProducts();

        $collection = DB::table("products")
            ->join("categories", "products.category_id", "=", "categories.id")
            ->select("products.id", "products.name", "categories.name as category_name", "products.price")
            ->get();
        
        self::assertCount(2, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }
}
