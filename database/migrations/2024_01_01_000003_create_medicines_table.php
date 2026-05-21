<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("medicines", function (Blueprint $table) {
            $table->id();
            $table->string("code", 50)->unique();
            $table->string("name", 200);
            $table->foreignId("category_id")->constrained("categories")->onDelete("restrict");
            $table->foreignId("unit_id")->constrained("units")->onDelete("restrict");
            $table->string("generic_name", 200)->nullable();
            $table->string("manufacturer", 200)->nullable();
            $table->decimal("price", 12, 2)->default(0);
            $table->integer("min_stock")->default(10);
            $table->text("description")->nullable();
            $table->text("composition")->nullable();
            $table->text("side_effects")->nullable();
            $table->text("usage_instruction")->nullable();
            $table->boolean("is_active")->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("medicines");
    }
};
