<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->constrained('notes')->cascadeOnDelete();
            $table->enum('style_type', ['default', 'learning', 'formal']);
            $table->longText('content_markdown');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_notes');
    }
};
