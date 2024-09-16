<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('value');
            $table->boolean('is_boolean');
            $table->timestamps();
        });

        DB::table('settings')->insert([
            'name' => 'gcs_credentials',
            'value' => '',
            'is_boolean' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('settings')->insert([
            'name' => 'gcs_bucket',
            'value' => '',
            'is_boolean' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('settings')->insert([
            'name' => 'gcs_project_id',
            'value' => '',
            'is_boolean' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('settings')->insert([
            'name' => 'gcs_folder',
            'value' => '',
            'is_boolean' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
