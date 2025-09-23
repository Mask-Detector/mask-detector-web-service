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
        Schema::create('mask_detections', function (Blueprint $table) {
            $table->id();
            $table->string('image_path')->nullable(); // Path gambar hasil deteksi
            $table->json('detection_results'); // Hasil deteksi dalam format JSON
            $table->integer('total_persons'); // Total orang yang terdeteksi
            $table->integer('wearing_mask'); // Jumlah orang dengan masker
            $table->integer('not_wearing_mask'); // Jumlah orang tanpa masker
            $table->decimal('confidence_avg', 5, 2)->nullable(); // Rata-rata confidence
            $table->timestamp('detected_at'); // Waktu deteksi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mask_detections');
    }
};
