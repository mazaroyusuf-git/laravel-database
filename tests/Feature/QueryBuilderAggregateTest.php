<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QueryBuilderAggregateTest extends TestCase
{
    //kita juga bisa melakukan Aggregate Query menggunakan Query Builder seperti count(column), min(column), max(column), avg(column), sum(column)
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

    public function testAggregate()
    {
        $this->insertProducts();

        $collection = DB::table("products")->count("id");
        self::assertEquals(2, $collection);

        $collection = DB::table("products")->max("price");
        self::assertEquals(20000000, $collection);

        $collection = DB::table("products")->min("price");
        self::assertEquals(18000000, $collection);

        $collection = DB::table("products")->min("price");
        self::assertEquals(18000000, $collection);

        $collection = DB::table("products")->avg("price");
        self::assertEquals(19000000, $collection);

        $collection = DB::table("products")->sum("price");
        self::assertEquals(38000000, $collection);
    }

    //sayang nya laravel tidak menyediakan gabungan operasi Aggregate, namun kita bisa menggunakan Query Builder Raw dengan method raw()
    public function testRaw()
    {
        $this->insertProducts();

        $collection = DB::table("products")
            ->select(
                DB::raw("count(*) as total_product"),
                DB::raw("min(price) as min_price"),
                DB::raw("max(price) as max_price")
            )->get();

        self::assertEquals(2, $collection[0]->total_product);
        self::assertEquals(18000000, $collection[0]->min_price);
        self::assertEquals(20000000, $collection[0]->max_price);
    }

    public function insertProductsFood()
    {
        DB::table("products")->insert([
            "id" => "3",
            "name" => "Bakso",
            "category_id" => "FOOD",
            "price" => 20000
        ]);
        DB::table("products")->insert([
            "id" => "4",
            "name" => "Mie Ayam",
            "category_id" => "FOOD",
            "price" => 20000
        ]);
    }

    //saat kita melakukan aggregate kadang kita ingin melakukan grouping, kita bisa menggunakan method groupBy(value)
    public function testGouping()
    {
        $this->insertProducts();
        $this->insertProductsFood();

        $collection = DB::table("products")
            ->select("category_id", DB::raw("count(*) as total_product"))
            ->groupBy("category_id")
            ->orderBy("category_id", "desc")
            ->get();
        
        self::assertCount(2, $collection);
        self::assertEquals("SMARTPHONE", $collection[0]->category_id);
        self::assertEquals("FOOD", $collection[1]->category_id);
        self::assertEquals(2, $collection[0]->total_product);
        self::assertEquals(2, $collection[1]->total_product);
    }

    //kita juga bisa menambahkan having saat grupBy menggunakan method having(column, operator, value)
    public function testGoupingHaving()
    {
        $this->insertProducts();
        $this->insertProductsFood();

        $collection = DB::table("products")
            ->select("category_id", DB::raw("count(*) as total_product"))
            ->groupBy("category_id")
            ->orderBy("category_id", "desc")
            ->having(DB::raw("count(*)"), ">", 2)
            ->get();
        
        self::assertCount(0, $collection);
    }
}
