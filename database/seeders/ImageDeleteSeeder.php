<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ImageDeleteSeeder extends Seeder
{
    /**
     * Remove old images form storage
     */
    public function run(): void
    {
        $directory = 'public/img';

        $files = Storage::files($directory);

        foreach ($files as $file) {
            if($file != 'public/img/default.jpg'){
                Storage::delete($file);
                $this->command->info("Deleted: $file");
            }
        }
    }
}
