<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("stock_movements", function (Blueprint $table) {
            $table->id();
            $table->foreignId("medicine_id")->constrained("medicines")->onDelete("cascade");
            $table->foreignId("medicine_batch_id")->nullable()->constrained("medicine_batches")->onDelete("set null");
            $table->enum("type", ["in", "out", "adjustment", "expired", "return"]);
            $table->integer("quantity");
            $table->integer("previous_stock");
            $table->integer("new_stock");
            $table->string("reference_type", 100)->nullable();
            $table->unsignedBigInteger("reference_id")->nullable();
            $table->text("notes")->nullable();
            $table->foreignId("user_id")->constrained()->onDelete("restrict");
            $table->timestamps();
            
            $table->index(["type", "created_at"]);
            $table->index(["medicine_id", "created_at"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("stock_movements");
    }
};
