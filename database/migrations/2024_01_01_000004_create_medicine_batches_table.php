<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("medicine_batches", function (Blueprint $table) {
            $table->id();
            $table->foreignId("medicine_id")->constrained("medicines")->onDelete("cascade");
            $table->string("batch_number", 100);
            $table->date("expired_date");
            $table->integer("quantity")->default(0);
            $table->decimal("purchase_price", 12, 2)->default(0);
            $table->decimal("selling_price", 12, 2)->default(0);
            $table->date("manufacture_date")->nullable();
            $table->boolean("is_active")->default(true);
            $table->timestamps();
            
            $table->unique(["medicine_id", "batch_number"]);
            $table->index(["expired_date", "is_active"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("medicine_batches");
    }
};
