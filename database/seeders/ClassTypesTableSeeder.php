<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('class_types')->delete();

       $data = [
            ['name' => 'Level 1', 'code' => 'L1'],
            ['name' => 'Level 2', 'code' => 'L2'],
            ['name' => 'Level 3', 'code' => 'L3'],
            ['name' => 'Level 4', 'code' => 'L4'],
            ['name' => 'Level 5', 'code' => 'L5'],
        ];

        DB::table('class_types')->insert($data);

    }
}
