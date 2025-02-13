<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Query\Builder;
use Tests\TestCase;

class QueryBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM products");
        DB::delete("DELETE FROM categories");
    }

    //Selain Menggunakan RawSQl laravel punya fitur Query Builder, kita bisa gunakan class Illuminate/Database/Query/Builder
    //untuk membuat Query Builder kita bisa gunakan method DB::table(nama)

    //kita bisa melakukan insert di QueryBuilder dengan menggunakan prefix insert dengan parameter assosiative array dimana key nya adalah kolom, dan value nya
    //adalah nilai yang akan disimpan di database.

    //insert() untuk memasukkan data ke database, throw exception jika terjadi error misal duplicate Primary key
    //insertGetId() untuk memasukkan data ke database, dan mengembalikan primary key yang diset secara auto generate, cocok unutk table dengan fitur auto incerement
    //insertOrIgnore() unutk memasukkan data ke database, dan jika error, maka akan di ignore
    public function testInsert()
    {
        DB::table("categories")->insert([
            "id" => "GADGET",
            "name" => "Gadget"
        ]);
        DB::table("categories")->insert([
            "id" => "FOOD",
            "name" => "Food"
        ]);

        $result = DB::select("SELECT COUNT(id) as total FROM categories");
        self::assertEquals(2, $result[0]->total);
    }
    
    //lalu QueryBuilder punya perintah select dengan method select(columns), lalu kita bisa gunakan method get() mengambil serluruh data, first() mengambil data pertama
    //pluck() mengambil satu kolom, hasil dari method ini adalan Laravel Collection
    public function testSelect()
    {
        $this->testInsert();

        $collection = DB::table("categories")->select(["id", "name"])->get();
        self::assertNotNull($collection);

        $collection->each(function ($record) {
            Log::info(json_encode($record));
        });
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

    //lalu QueryBuilder juga punya where, kita bisa menggunakan method dengan prefix where...(), contoh nya where(column, operator, value), where([conditioon1, condition2]),
    //where(callback(builder)), orWhere(column, operator, value), orWhere([conditioon1, condition2]), orWhere(callback(builder)) selengkapnya lihat di chapter 154
    public function testWhereOperator()
    {
        $this->testWhere();

        $collection = DB::table("categories")->orWhere(function (Builder $builder) {
            $builder->where("id", "=", "SMARTPHONE");
            $builder->orWhere("id", "=", "LAPTOP");
            //ini sama dengan,SELECT * FROM categories WHERE (id = SMARTPHONE OR id = LAPTOP)
        })->get();

        self::assertCount(2, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }

    public function testWhereBetween()
    {
        $this->testWhere();

        $collection = DB::table("categories")
            ->whereBetween("created_at", ["2025-10-10 00:00:00", "2025-10-10 23:59:59"])->get();
        self::assertCount(4, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }

    public function testWhereIn()
    {
        $this->testWhere();

        $collection = DB::table("categories")->whereIn("id", ["SMARTPHONE", "LAPTOP"])->get();
        self::assertCount(2, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }

    public function testWhereNull()
    {
        $this->testWhere();

        $collection = DB::table("categories")->whereNull("description")->get();
        self::assertCount(4, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }

    public function testWhereDate()
    {
        $this->testWhere();

        $collection = DB::table("categories")->whereDate("created_at", "2025-10-10")->get();
        self::assertCount(4, $collection);
        for ($i = 0; $i < count($collection); $i++) {
            Log::info(json_encode($collection[$i]));
        }
    }
}
