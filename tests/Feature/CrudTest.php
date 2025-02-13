<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

use function Laravel\Prompts\select;

class CrudTest extends TestCase
{
    //laravel punya command di artisan bernama db:

    //Untuk mengkonfigurasi Database kita bisa mengatur nya di config/database.php rata2 pengaturan nya dari env, kita juga bisa berinteraksi dengan database
    //menggunakan Facade DB https://laravel.com/api/10.x/Illuminate/Support/Facades/DB.html 

    //untuk membuat quesry ke database kita bisa gunakan Eloquent ORM atau Query Builder, namun jika kita ingin menghemat performa kita bisa buat dalam bentuk
    //Raw Query

    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM categories");
    }

    //untuk melihat command CRUD nya bisa lihat di chapter 148

    public function testCrud(): void
    {
        DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?, ?, ?, ?)", [
            "GADGET", "Gadget", "Gadget Categories", "2025-01-01 00:00:00"
        ]);  
        
        $result = DB::select("SELECT * FROM categories WHERE id = ?", ["GADGET"]);

        self::assertEquals(1, count($result));
        self::assertEquals("GADGET", $result[0]->id);
        self::assertEquals("Gadget", $result[0]->name);
        self::assertEquals("Gadget Categories", $result[0]->description);
        self::assertEquals("2025-01-01 00:00:00", $result[0]->created_at);
    }

    //kita juga bisa menggunakan named binding dibandingkan menggunakan tanda tanya kita bisa gunakan :key lalu kita bisa isi data nya dengan array seperti biasa
    //namun dengan key sesuai nama binding nya

    public function testNamedBinding(): void
    {
        DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(:id, :name, :description, :created_at)", [
            "id" => "GADGET", 
            "name" => "Gadget", 
            "description" => "Gadget Categories", 
            "created_at" => "2025-01-01 00:00:00"
        ]);  
        
        $result = DB::select("SELECT * FROM categories WHERE id = :id", ["id" => "GADGET"]);

        self::assertEquals(1, count($result));
        self::assertEquals("GADGET", $result[0]->id);
        self::assertEquals("Gadget", $result[0]->name);
        self::assertEquals("Gadget Categories", $result[0]->description);
        self::assertEquals("2025-01-01 00:00:00", $result[0]->created_at);
    }
}
