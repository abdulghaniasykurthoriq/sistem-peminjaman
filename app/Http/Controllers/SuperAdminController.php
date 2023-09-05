<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Items;
use App\Models\Lab;
use App\Models\Mahasiswa;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
    
        $transactions = Transaction::with(['item.lab', 'user'])->get();
        $totalSedangPinjam = Transaction::where('status','accept')->count();

        $totalItems = Items::sum('stock');

        $totalAdmin = Admin::count();



        // dd($transactions);
            return view('superadmin.index',[
                'data' => $transactions,
                'total_items' => $totalItems ,
                'total_admin' => $totalAdmin ,
                'items_sedang_dipinjam' => $totalSedangPinjam
            ]);  
    }
    public function admin()
    {
        $usersWithAdmin = User::where('role','admin')->with('admin')->get();
        // dd($usersWithAdmin);        
        return view('superadmin.admin',[
            'admin' => $usersWithAdmin
        ]);      
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
        
        return redirect()->route('admin.index');




    }
    public function edit_admin($id){
        $lab = Lab::all();
        $user = User::with('admin')->find($id);
        // dd($user);
        return view('superadmin.admin_edit',[
            'lab' => $lab,
            'user' => $user
        ]);
    }
    public function update_admin(Request $request, $id){
        $user = User::with('admin')->find($id);
        
        if (!$user) {
            return redirect()->route('admin.index')->with('alert', 'User tidak ditemukan');
        }

        $attrs = $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'jabatan' => 'required',
            'lab' => 'required',
        ]);
        if($request->input('password')){
            $user->password = $request->input('password');
        }
        $user->name = $attrs['name'];
        $user->username = $attrs['username'];
        $user->email = $attrs['email'];
        $user->admin->jabatan = $attrs['jabatan'];
        $user->admin->lab_id = $attrs['lab'];
        $user->save();
        return redirect()->route('admin.index')->with('alert', 'Berhasil memperbarui user');



    }
    

    public function destroy_admin($id){
        $user  = User::find($id);
        if(!$user){
            return "not found";
        }
        $user->admin->delete();
        $user->delete();

        return redirect()->route('admin.index');


    }


    public function mahasiswa()
    {
        $usersWithMahasiswa = User::where('role','mahasiswa')->with('mahasiswa')->get();
        // dd($usersWithAdmin);        
        return view('superadmin.mahasiswa',[
            'mahasiswa' => $usersWithMahasiswa
        ]);      
    }
    public function create_mahasiswa()
    {

        return view('superadmin.mahasiswa_create');      
    }
    public function store_mahasiswa(Request $request)
    {
        $attrs = $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
            'nim' => 'required',
            'jurusan' => 'required',
            'kelas' => 'required',

        ]);

        $user = User::create([
            'name' => $attrs['name'],
            'username' => $attrs['username'],
            'email' => $attrs['email'],
            'password' => $attrs['password'],
            'role' => 'mahasiswa'
        ]);
        // dd($user->id);
        
        Mahasiswa::create([
            'user_id' => $user->id,
            'nim' => $attrs['nim'],
            'jurusan' => $attrs['jurusan'],
            'kelas' => $attrs['kelas']
        ]);
        
        return redirect()->route('mahasiswa.index');
    }
    public function destroy_mahasiswa($id){
        $user  = User::find($id);
        if(!$user){
            return "not found";
        }
        $user->mahasiswa->delete();
        $user->delete();

        return redirect()->route('mahasiswa.index');


    }

    public function edit_mahasiswa($id){
        $user = User::with('mahasiswa')->find($id);
        // dd($user);
        return view('superadmin.mahasiswa_edit',[
            'user' => $user
        ]);
    }
    public function update_mahasiswa(Request $request, $id){
        $user = User::with('mahasiswa')->find($id);
        // dd($user);
        
        if (!$user) {
            return redirect()->route('mahasiswa.index')->with('alert', 'User tidak ditemukan');
        }

        $attrs = $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'nim' => 'required',
            'jurusan' => 'required',
            'kelas' => 'required',
        ]);
        if($request->input('password')){
            $user->password = $request->input('password');
        }
        $user->name = $attrs['name'];
        $user->username = $attrs['username'];
        $user->email = $attrs['email'];
        $user->mahasiswa->nim = $attrs['nim'];
        $user->mahasiswa->kelas = $attrs['kelas'];
        $user->mahasiswa->jurusan = $attrs['jurusan'];
        $user->save();
        return redirect()->route('mahsiswa.index')->with('alert', 'Berhasil memperbarui user');



    }
   
}
