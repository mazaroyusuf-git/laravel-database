<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    //Laravel DB juga mendukung fitur Transaction, kita bisa gunakan DB::transaction(function) didalam function nya kita bisa lakukan perintah database,
    //jika error otomatis akan di rollback

    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM categories");
    }

    public function testTransaction()
    {
        DB::transaction(function () {
            DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?, ?, ?, ?)", [
                "GADGET", "Gadget", "Gadget Categories", "2025-01-01 00:00:00"
            ]);  
            DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?, ?, ?, ?)", [
                "FOOD", "Food", "Food Categories", "2025-01-01 00:00:00"
            ]);  
        });

        $result = DB::select("SELECT * FROM categories");
        self::assertEquals(2, count($result));
    }

    //kita juga bisa melakukan nya dengan cara manual dengan begin commit dan rollback

    public function testManualTransaction()
    {
        try {
            DB::beginTransaction();
            DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?, ?, ?, ?)", [
                "GADGET", "Gadget", "Gadget Categories", "2025-01-01 00:00:00"
            ]);  
            DB::insert("INSERT INTO categories(id, name, description, created_at) VALUES(?, ?, ?, ?)", [
                "FOOD", "Food", "Food Categories", "2025-01-01 00:00:00"
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $result = DB::select("SELECT * FROM categories");
        self::assertEquals(2, count($result));
    }
}
