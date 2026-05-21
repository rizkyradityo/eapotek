ORIGINAL CODE:
- Has `use` statements at the top
- Has comments for `up()` and `down()` methods
- Has empty function bodies in both `up()` and `down()`

SUGGESTED EDIT:
- Removes the `use` statements
- Removes the comments
- Adds the actual migration code in both `up()` and `down()` methods

The user says to:
- Output ONLY code
- Leave existing comments in place unless changes require modifying them
- Preserve all unchanged code exactly as-is

So I need to keep the `use` statements and add the migration code inside the function bodies, while keeping the comments in place.

Let me construct the complete modified file:


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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'apoteker', 'kasir'])->default('kasir')->after('email');
            $table->string('phone', 20)->nullable()->after('role');
            $table->string('address', 255)->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'address', 'is_active']);
        });
    }
};
