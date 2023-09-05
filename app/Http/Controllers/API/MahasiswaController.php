<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    
    // public function add_to_cart(){
    //     $item = Items::find($id);

    //     if (!$item) {
    //         return redirect()->route('itemslab.index');
    //     }

    //     // Cek apakah item sudah ada dalam keranjang
    //     $existingCart = Cart::where('user_id', Auth::user()->id)
    //         ->where('items_id', $id)
    //         ->first();

    //     if ($existingCart) {
    //         // Jika item sudah ada dalam keranjang, tambahkan jumlahnya
    //         $existingCart->increment('quantity');
    //     } else {
    //         // Jika item belum ada dalam keranjang, tambahkan item baru
    //         Cart::create([
    //             'user_id' => Auth::user()->id,
    //             'items_id' => $id,
    //             'quantity' => 1,
    //         ]);
    //     }

    //     return redirect()->route('itemslab.index');
    //     return response()->json([
    //         'message' => 'my cart'
    //     ]);
    // }
}
