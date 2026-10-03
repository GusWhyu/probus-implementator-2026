<?php

namespace Database\Seeders;

use App\Models\Outlet;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tag;
use App\Models\Kategori;
use App\Models\Status;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        //Tag
        Tag::create([
            'name' => 'Programmer'
        ]);
        Tag::create([
            'name' => 'E-Commerce'
        ]);
        Tag::create([
            'name' => 'Trainer'
        ]);
        Tag::create([
            'name' => 'IT'
        ]);
        Tag::create([
            'name' => 'Admin'
        ]);
        Tag::create([
            'name' => 'Supervisor'
        ]);


        //User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'usertype' => 'admin',
            'tag_id' => '5',
            'password' => bcrypt('12345678')
        ]);
        User::create([
            'name' => 'Supervisor',
            'email' => 'spv@mail.com',
            'usertype' => 'supervisor',
            'tag_id' => '6',
            'password' => bcrypt('12345678')
        ]);

        //Kategori
        Kategori::create([
            'name' => 'PHIS'
        ]);
        Kategori::create([
            'name' => 'PRESTO'
        ]);
        Kategori::create([
            'name' => 'PPOS'
        ]);

        //Status
        Status::create([
            'name' => 'Urgent'
        ]);
        Status::create([
            'name' => 'Open'
        ]);
        Status::create([
            'name' => 'Progress'
        ]);
        Status::create([
            'name' => 'Closed'
        ]);

        Outlet::create([
            'nm_out' => 'Bapak Bakery',
            'lokasi' => 'BALI'
        ]);
        Outlet::create([
            'nm_out' => 'Burger King',
            'lokasi' => 'BALI'
        ]);
        Outlet::create([
            'nm_out' => 'Blackcanyon Batam',
            'lokasi' => 'BATAM'
        ]);
        Outlet::create([
            'nm_out' => 'The Plumber Arms',
            'lokasi' => 'CANGGU'
        ]);
    }
}
