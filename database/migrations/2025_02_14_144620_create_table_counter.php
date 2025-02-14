<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //function up dan down adalah kebalikan dari satu sama lain, down seperti rollback dari up
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //untuk mengalter table kita bisa gunakan
        //Schema::table("namaTable", function)
        //lalu kita bisa melakukan apa yang amu di alter di table tersebut di bagian function

        //create unutk menambahkan table
        Schema::create('counters', function (Blueprint $table) {
            $table->string("id", 100)->nullable(false)->primary(); //artinya kita buat id varchar(100) not null primary key
            $table->integer("counter")->nullable(false)->default(0); //counter int not null default 0
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counters');
    }
};
