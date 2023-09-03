<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Items;
use App\Models\Lab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaController extends Controller
{
    public function index(){
        $lab = Lab::withCount('items')->get();
        // dd($lab);
        return view('mahasiswa.lab',[
            'lab' => $lab
        ]);
    }
    public function items($items){
        $lab = Lab::where('name',$items)->with('items')->first();
        // dd($lab->items);
        return view('mahasiswa.items',[
            'data' => $lab
        ]);
    }

    public function addToCart(Request $request, $id){
        $item = Items::find($id);
        
        if (!$item) {
            return redirect()->route('itemslab.index');
        }
        
        // Cek apakah item sudah ada dalam keranjang
        $existingCart = Cart::where('user_id', Auth::user()->id)
            ->where('items_id', $id)
            ->first();
    
        if ($existingCart) {
            // Jika item sudah ada dalam keranjang, tambahkan jumlahnya
            $existingCart->increment('quantity');
        } else {
            // Jika item belum ada dalam keranjang, tambahkan item baru
            Cart::create([
                'user_id' => Auth::user()->id,
                'items_id' => $id,
                'quantity' => 1
            ]);
        }
    
        return redirect()->route('itemslab.index');

    }
    

    public function my_cart(){
        $myCart = Cart::with('item')->where('user_id', Auth::user()->id)->get();
        // dd($myCart); 
        return view('mahasiswa.my_cart',
        ['myCart' => $myCart]
    );
    }
}
