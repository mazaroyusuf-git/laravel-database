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
        Schema::create('products', function (Blueprint $table) {
            $table->string("id", 100)->nullable(false)->primary(); //id varchar(100) not null primary key
            $table->string("name", 100)->nullable(false); //name varchar(100) not null
            $table->text("description")->nullable(true);
            $table->integer("price")->nullable(false);
            $table->string("category_id", 100)->nullable(false);
            $table->timestamp("created_at")->nullable(false)->useCurrent();
            
            $table->foreign("category_id")->references("id")->on("categories"); //foreign key fk_category_products categoryid refrences id.categories
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
