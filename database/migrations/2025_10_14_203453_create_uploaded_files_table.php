<?php

use App\Enums\UploadedFileImportStatus;
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
        Schema::create('uploaded_files', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('stored_name', 50);
            $table->enum('import_status', array_column(UploadedFileImportStatus::cases(), 'value'))->default(UploadedFileImportStatus::Pending);
            $table->timestamps();

            $table->index(['import_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploaded_files');
    }
};
