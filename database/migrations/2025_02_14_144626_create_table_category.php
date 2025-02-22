<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) { //create table categories (
            $table->string("id", 100)->nullable(false)->primary(); //id varchar(100) not null primary key
            $table->string("name", 100)->nullable(false); //name varchar(100) not null
            $table->text("description")->nullable(true);
            $table->timestamp("created_at")->nullable(false)->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
