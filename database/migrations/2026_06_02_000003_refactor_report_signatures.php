<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        // Convert any existing base64 signature_cust values to PNG files
        DB::table('reports')
            ->whereNotNull('signature_cust')
            ->get()
            ->each(function ($report) {
                $sig = $report->signature_cust;
                if (str_starts_with($sig, 'data:image/')) {
                    $imgData = base64_decode(
                        preg_replace('/^data:image\/\w+;base64,/', '', $sig)
                    );
                    $hash = hash('sha256', $imgData);
                    $path = 'signatures/' . $hash . '.png';
                    if (!Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->put($path, $imgData);
                    }
                    DB::table('reports')->where('id', $report->id)
                        ->update(['signature_cust' => $path]);
                }
            });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('signature_tech');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->text('signature_tech')->nullable()->after('photos');
        });
    }
};
