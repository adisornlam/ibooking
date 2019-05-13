<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Som Jeed',
            'email' => 'som@mail.com',
            'password' => Hash::make('12345678'),
            'mobile' => '0888888888'
        ]);
    }
}
