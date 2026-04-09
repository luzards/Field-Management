<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sop_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_in_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->json('items'); // [{name, checked, value}]
            $table->json('photos')->nullable(); // array of photo paths
            $table->text('comments')->nullable();
            $table->unsignedTinyInteger('overall_value'); // 1-10
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sop_checklists');
    }
};
