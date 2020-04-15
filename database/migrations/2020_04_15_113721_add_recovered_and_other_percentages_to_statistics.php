<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecoveredAndOtherPercentagesToStatistics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->integer('death_diff')->nullable();
            $table->integer('recovered')->nullable();
            $table->integer('recovered_diff')->nullable();
            $table->decimal('recovered_percent', 8, 4)->nullable();
            $table->decimal('total_percent_vs_population', 8, 4)->nullable();
            $table->decimal('actives_percent_vs_population', 8, 4)->nullable();
            $table->decimal('death_percent_vs_population', 8, 4)->nullable();
            $table->decimal('recovered_percent_vs_population', 8, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('statistics', function (Blueprint $table) {
            $table->dropColumn('death_diff');
            $table->dropColumn('recovered');
            $table->dropColumn('recovered_diff');
            $table->dropColumn('recovered_percent');
            $table->dropColumn('total_percent_vs_population');
            $table->dropColumn('actives_percent_vs_population');
            $table->dropColumn('death_percent_vs_population');
            $table->dropColumn('recovered_percent_vs_population');
        });
    }
}
