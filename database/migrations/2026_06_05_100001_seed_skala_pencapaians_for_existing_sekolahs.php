<?php

use App\Models\SkalaPencapaian;
use App\Models\Sekolah;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Sekolah::query()->pluck('id')->each(function (int $sekolahId) {
            SkalaPencapaian::seedDefaultsForSekolah($sekolahId);
        });
    }

    public function down(): void
    {
        // Data seed; tidak di-rollback agar tidak menghapus kustomisasi admin.
    }
};
