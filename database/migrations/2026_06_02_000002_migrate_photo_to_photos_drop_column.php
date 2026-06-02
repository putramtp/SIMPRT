<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Move existing single photo into the photos JSON array
        DB::table('reports')
            ->whereNotNull('photo')
            ->get()
            ->each(function ($report) {
                DB::table('reports')->where('id', $report->id)->update([
                    'photos' => json_encode([$report->photo]),
                ]);
            });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('status');
        });

        // Restore first photo back to the photo column
        DB::table('reports')
            ->whereNotNull('photos')
            ->get()
            ->each(function ($report) {
                $photos = json_decode($report->photos, true);
                if (!empty($photos)) {
                    DB::table('reports')->where('id', $report->id)->update([
                        'photo' => $photos[0],
                    ]);
                }
            });
    }
};
