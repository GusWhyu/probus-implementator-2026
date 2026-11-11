<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixRelativeImagePathsInManualBooks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Perbaiki src gambar relatif yang tidak memiliki leading slash /
        $books = DB::table('manual_books')
            ->where('content', 'like', '%src="uploads/editor/%')
            ->orWhere('content', 'like', "%src='uploads/editor/%")
            ->get();

        foreach ($books as $b) {
            $updated = preg_replace('/src=(["\'])uploads\/editor\//i', 'src=$1/uploads/editor/', $b->content);
            DB::table('manual_books')->where('id', $b->id)->update(['content' => $updated]);
        }

        $articles = DB::table('articles')
            ->where('content', 'like', '%src="uploads/editor/%')
            ->orWhere('content', 'like', "%src='uploads/editor/%")
            ->get();

        foreach ($articles as $a) {
            $updated = preg_replace('/src=(["\'])uploads\/editor\//i', 'src=$1/uploads/editor/', $a->content);
            DB::table('articles')->where('id', $a->id)->update(['content' => $updated]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No-op
    }
}
