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
        Schema::table('menus', function (Blueprint $table) {
            $table->string('label_ml')->nullable()->after('label');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->string('title_ml')->nullable()->after('title');
            $table->string('seo_title_ml')->nullable()->after('seo_title');
            $table->text('seo_description_ml')->nullable()->after('seo_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['label_ml']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['title_ml', 'seo_title_ml', 'seo_description_ml']);
        });
    }
};
