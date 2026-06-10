<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("stock_opnames", function (Blueprint $table) {
            $table->id();
            $table->string("opname_number", 50)->unique();
            $table->date("opname_date");
            $table->text("description")->nullable();
            $table->enum("status", ["draft", "completed", "cancelled"])->default("draft");
            $table->foreignId("user_id")->constrained()->onDelete("restrict");
            $table->text("notes")->nullable();
            $table->timestamps();
        });

        Schema::create("stock_opname_items", function (Blueprint $table) {
            $table->id();
            $table->foreignId("stock_opname_id")->constrained("stock_opnames")->onDelete("cascade");
            $table->foreignId("medicine_id")->constrained("medicines")->onDelete("cascade");
            $table->foreignId("medicine_batch_id")->nullable()->constrained("medicine_batches")->onDelete("cascade");
            $table->integer("system_quantity")->default(0);
            $table->integer("actual_quantity")->default(0);
            $table->integer("difference")->default(0);
            $table->text("notes")->nullable();
            $table->timestamps();

            $table->index(["stock_opname_id", "medicine_id"]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("stock_opname_items");
        Schema::dropIfExists("stock_opnames");
    }
};
