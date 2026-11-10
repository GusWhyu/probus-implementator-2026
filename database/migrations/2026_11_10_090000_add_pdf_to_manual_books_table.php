<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('manual_books', 'pdf_path')) {
            Schema::table('manual_books', function (Blueprint $table) {
                $table->string('pdf_path')->nullable()->after('description');
                $table->string('pdf_name')->nullable()->after('pdf_path');
                $table->unsignedBigInteger('pdf_size')->nullable()->after('pdf_name');
            });
        }

        // Ubah kolom content agar nullable jika dokumen hanya berupa file PDF
        try {
            DB::statement('ALTER TABLE manual_books MODIFY COLUMN content LONGTEXT NULL');
        } catch (\Throwable $e) {
            // Abaikan jika tidak didukung driver selain MySQL
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('manual_books', 'pdf_path')) {
            Schema::table('manual_books', function (Blueprint $table) {
                $table->dropColumn(['pdf_path', 'pdf_name', 'pdf_size']);
            });
        }
    }
};
