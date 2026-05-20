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
        Schema::table('tasks', function (Blueprint $table) {
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])
                  ->default('normal')->after('due_date');
            $table->foreignId('template_id')
                  ->nullable()->after('priority')
                  ->constrained('templates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn(['priority', 'template_id']);
        });
    }
};
