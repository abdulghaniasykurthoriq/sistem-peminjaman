<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'super admin',
            'username' => 'superadmin',
            'role' => 'superadmin',
            'email' => 'superadmin@polindra.ac.id',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt(123456)
        ]);
        $user = User::create([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@polindra.ac.id',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt(123456),
            'role' => 'admin'
        ]);
        // dd($user->id);
        
        Admin::create([
            'user_id' => $user->id,
            'lab_id' => 1,
            'jabatan' => 'admin'
        ]);
        $user = User::create([
            'name' => 'mahasiswa',
            'username' => 'mahasiswa',
            'email' => 'mahasiswa@polindra.ac.id',
            'email_verified_at' => Carbon::now(),
            'password' => bcrypt(123456),
            'role' => 'mahasiswa'
        ]);
        // dd($user->id);
        
        Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => 2105001,
            'jurusan' => 'TI',
            'kelas' => 'D4RPL3A'
        ]);
    }
}
