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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->dateTime('mulai')->index();
            $table->dateTime('selesai')->index();
            $table->string('lokasi');
            $table->foreignId('pj_id')->nullable()->constrained('penguruses')->nullOnDelete();
            $table->enum('status', ['rencana', 'berjalan', 'selesai', 'batal'])->default('rencana')->index();
            $table->foreignId('user_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
