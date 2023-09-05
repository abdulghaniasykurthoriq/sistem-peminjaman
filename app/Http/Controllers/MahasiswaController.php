<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Items;
use App\Models\Lab;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaController extends Controller
{
    public function index()
    {
        $lab = Lab::withCount('items')->get();
        // dd($lab);
        return view('mahasiswa.lab', [
            'lab' => $lab,
        ]);
    }
    public function items($items)
    {
        $lab = Lab::where('name', $items)
            ->with('items')
            ->first();
        // dd($lab->items);
        return view('mahasiswa.items', [
            'data' => $lab,
        ]);
    }

    public function addToCart(Request $request, $id)
    {
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
                'quantity' => 1,
            ]);
        }

        return redirect()->route('itemslab.index');
    }

    public function my_cart()
    {
        $myCart = Cart::with('item.lab')
            ->where('user_id', Auth::user()->id)
            ->get();
        // dd($myCart);
        return view('mahasiswa.my_cart', ['myCart' => $myCart]);
    }
    public function destroy_my_cart($id)
    {
        $myCart = Cart::find($id);
        if (!$myCart) {
            return 'kosong';
        }
        if ($myCart->quantity > 1) {
            $myCart->quantity = $myCart->quantity - 1;
            $myCart->save();
            return redirect()->route('itemslab.myCart');
        }
        $myCart->delete();
        return redirect()->route('itemslab.myCart');
    }
    public function add_item_my_cart($id)
    {
        $myCart = Cart::find($id);
        if (!$myCart) {
            return 'kosong';
        }

        $myCart->quantity = $myCart->quantity + 1;
        $myCart->save();

        $myCart->save();
        return redirect()->route('itemslab.myCart');
    }

    public function checkout()
    {
        $user = Auth::user();
        $myCart = Cart::where('user_id', $user->id)->get();

        foreach ($myCart as $cartItem) {
            // Dapatkan data dari item dalam cart
            $itemName = $cartItem->item->name; // Contoh: Mendapatkan nama item
            $itemPrice = $cartItem->item->price; // Contoh: Mendapatkan harga item
            // Dan sebagainya, sesuai dengan atribut yang ada dalam model Item
            if ($cartItem->quantity > 1) {
                for ($i = 0; $i < $cartItem->quantity; $i++) {
                    Transaction::create([
                        'user_id' => $user->id,
                        'item_id' => $cartItem->items_id,
                        'status' => 'proses',
                        'keterangan' => 'proses'
                    ]);
                }
            }else{
                Transaction::create([
                    'user_id' => $user->id,
                    'item_id' => $cartItem->items_id,
                    'status' => 'proses',
                    'keterangan' => 'proses'
                ]);
            }
            $cartItem->delete();
        }
        // dd('data pindah ke transaction');
        return redirect()->route('itemslab.peminjaman');
    }

    public function peminjaman(){
        $transaction = Transaction::with(['item','user'])->where('user_id',Auth::user()->id)->get();
        // dd($transaction);
        return view('mahasiswa.peminjaman',[
            'data' => $transaction
        ]);
    }
}
