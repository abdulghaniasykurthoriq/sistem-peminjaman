<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\Lab;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $userId = Auth::user()->id;
    $admin = User::with('admin')->where('id', $userId)->first();
   
    if (!$admin) {
        return abort(403);
    }
    $lab = Lab::find($admin->admin->lab_id);
    if (!$lab) {
        return abort(403);
    }
    $data = Items::where('lab_id', $lab->id)->with('lab')->get();
    return view('admin.items', [
        'data' => $data,
        'admin' => $lab
    ]);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lab = Lab::all();
        return view('admin.items_create',[
            'lab' => $lab
        ]);      
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->input());

        $attrs = $request->validate([
            'name' => 'required',
            'lab' => 'required',
            'jenis' => 'required',
            'stock' => 'required'
        ]);
        Items::create([
            'name' => $attrs['name'],
            'lab_id' => $attrs['lab'],
            'type' => $attrs['jenis'],
            'stock' => $attrs['stock'],
            'borrowed' => 0
        ]);
        
        return redirect()->route('items.index');


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
    public function edit($id)
    {
        $lab = Lab::all();
        $barang = Items::with('lab')->find($id);
        // dd($barang);
        return view('admin.items_edit',[
            'lab' => $lab,
            'barang' => $barang
        ]);  
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = Items::find($id);

        $attrs = $request->validate([
            'name' => 'required',
            'lab_id' => 'required',
            'jenis' => 'required',
            'stock' => 'required'
        ]);

        $item->name = $attrs['name'];
        $item->lab_id = $attrs['lab_id'];
        $item->type = $attrs['jenis'];
        $item->stock = $attrs['stock'];
        $item->save();
        return redirect()->route('items.index');

        
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id){
        $item  = Items::find($id);
        if(!$item){
            return "not found";
        }
        $item->delete();

        return redirect()->route('items.index');


    }
}
