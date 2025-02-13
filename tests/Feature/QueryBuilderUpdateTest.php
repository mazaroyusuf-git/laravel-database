<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class QueryBuilderUpdateTest extends TestCase
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

    //untuk melakukan update kita bisa menggunakan method update(array)
    public function testUpdate()
    {
        $this->testWhere();

        DB::table("categories")->where("id", "=", "SMARTPHONE")->update([
            "name" => "Handphone"
        ]);

        $collection = DB::table("categories")->where("name", "=", "Handphone")->get();
        self::assertCount(1, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }

    //kita juga bisa menggunakan method updateOrInsert(attributes, value) jika data yang diupdate tidak ada maka akan di insert data yang baru
    public function testUpdateOrInsert()
    {
        DB::table("categories")->updateOrInsert([
            "id" => "VOUCHER"
        ], [
            "name" => "Voucher",
            "description" => "Ticket and Voucher",
            "created_at" => "2025-10-10 10:10:10"
        ]);

        $collection = DB::table("categories")->where("id", "=", "VOUCHER")->get();
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }

    //kita juga bisa melakukan increment(column, increment) dan decrement(column, increment)
    public function testIncrement()
    {
        DB::table("counters")->where("id", "=", "sample")->increment("counter", 1);

        $collection = DB::table("counters")->where("id", "=", "sample")->get();
        self::assertCount(1, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }

    //kita bisa melakukan delete() dan truncate() untuk TRUNCATE table
    public function testDelete()
    {
        $this->testWhere();

        DB::table("categories")->where("id", "=", "SMARTPHONE")->delete();

        $collection = DB::table("categories")->where("id", "=", "SMARTPHONE")->get();
        self::assertCount(0, $collection);
    }
}
