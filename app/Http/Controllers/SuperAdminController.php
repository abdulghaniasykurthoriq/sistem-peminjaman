<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Lab;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    public function admin()
    {
        $usersWithAdmin = User::has('admin')->with('admin')->get();

        foreach ($usersWithAdmin as $user) {
            $admin = $user->admin;
            $lab = $admin->lab;
            dd($admin); // Lakukan sesuatu dengan $lab
        }
     
        return view('superadmin.admin');      
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create_admin()
    {
        $lab = Lab::all();
        return view('superadmin.admin_create',[
            'lab' => $lab
        ]);      
    }
    public function store_admin(Request $request)
    {
        // dd('kucing');

        $attrs = $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
            'jabatan' => 'required',
            'lab' => 'required',
        ]);

        // dd($attrs['name']) ; 
        // dd($request->input()) ; 

        $user = User::create([
            'name' => $attrs['name'],
            'username' => $attrs['username'],
            'email' => $attrs['email'],
            'password' => $attrs['password'],
            'role' => 'admin'
        ]);
        // dd($user->id);
        
        Admin::create([
            'user_id' => $user->id,
            'lab_id' => $attrs['lab'],
            'jabatan' => $attrs['jabatan']
        ]);
        

        dd('berhasdil');



    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
