<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\Lab;
use App\Models\Transaction;
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
    $user = Auth::user();
    
    // Periksa apakah pengguna adalah admin
    if ($user->role != 'admin') {
        return abort(403);
    }

    // Dapatkan lab terkait dengan admin
    $lab = $user->admin->lab;

    if (!$lab) {
        return abort(403);
    }

    // Dapatkan data item yang terkait dengan lab
    $items = Items::where('lab_id', $lab->id)->get();

    return view('admin.items', [
        'data' => $items,
        'admin' => $user,
    ]);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user()->load('admin.lab');
        // dd($user);
        $lab = Lab::all()->where('id', $user->admin->lab->id);
        return view('admin.items_create', [
            'lab' => $lab,
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
            'stock' => 'required',
        ]);
        Items::create([
            'name' => $attrs['name'],
            'lab_id' => $attrs['lab'],
            'type' => $attrs['jenis'],
            'stock' => $attrs['stock'],
            'borrowed' => 0,
        ]);

        return redirect()->route('items.index');
    }
    public function request_pinjaman()
    {
        $admin = User::with('admin.lab')
            ->where('id', Auth::user()->id)
            ->first();
        // $transaction = Transaction::with(['item.lab','user'])->get();
        $labId = $admin->admin->lab->id;
        $transactions = Transaction::with(['item.lab', 'user'])
            ->where('status', 'proses')
            ->whereHas('item.lab', function ($query) use ($labId) {
                $query->where('id', $labId);
            })
            ->get();

        // dd($admin);
        return view('admin.peminjaman_request', [
            'data' => $transactions,
            'lab' => $admin->admin->lab->name,
        ]);
    }
    public function peminjaman()
    {
        $admin = User::with('admin.lab')
            ->where('id', Auth::user()->id)
            ->first();
        // $transaction = Transaction::with(['item.lab','user'])->get();
        $labId = $admin->admin->lab->id;
        $transactions = Transaction::with(['item.lab', 'user'])
            ->where('status', 'accept')
            ->whereHas('item.lab', function ($query) use ($labId) {
                $query->where('id', $labId);
            })
            ->get();

        // dd($admin);
        return view('admin.peminjaman', [
            'data' => $transactions,
            'lab' => $admin->admin->lab->name,
        ]);
    }
    public function accept_pinjaman($id)
    {
        $transaction = Transaction::with('item')->find($id);
    
        if ($transaction) {
            // Pastikan stok mencukupi sebelum mengurangkan
            if ($transaction->item->stock > 0) {
                $transaction->status = 'accept';
    
                // Mengurangkan stok item
                $transaction->item->stock = $transaction->item->stock - 1;
                $transaction->item->borrowed = $transaction->item->borrowed + 1;

    
                // Simpan perubahan dalam transaksi dan item
                try {
                    $transaction->save();
                    $transaction->item->save();
                } catch (\Exception $e) {
                    // Tangani kesalahan jika gagal menyimpan
                    return redirect()->route('items.request_pinjaman')->with('error', 'Gagal menyimpan perubahan.');
                }
    
                return redirect()->route('items.request_pinjaman')->with('success', 'Transaksi diterima.');
            } else {
                return redirect()->route('items.request_pinjaman')->with('error', 'Stok tidak mencukupi.');
            }
        } else {
            return redirect()->route('items.request_pinjaman')->with('error', 'Transaksi tidak ditemukan.');
        }
    }
    
    public function deny_pinjaman($id)
    {
        $transaction = Transaction::find($id);
        $transaction->status = 'deny';
        $transaction->save();
        return redirect()->route('items.request_pinjaman');
    }
    public function done_pinjaman($id)
    {
        $transaction = Transaction::with('item')->find($id);
    
        if ($transaction) {
            // Pastikan stok mencukupi sebelum mengurangkan
            if ($transaction->item) {
                $transaction->status = 'done';
    
                // Mengurangkan stok item
                $transaction->item->stock = $transaction->item->stock + 1;
                $transaction->item->borrowed = $transaction->item->borrowed - 1;

    
                // Simpan perubahan dalam transaksi dan item
                try {
                    $transaction->save();
                    $transaction->item->save();
                } catch (\Exception $e) {
                    // Tangani kesalahan jika gagal menyimpan
                    return redirect()->route('items.peminjaman')->with('error', 'Gagal menyimpan perubahan.');
                }
    
                return redirect()->route('items.peminjaman')->with('success', 'Transaksi diterima.');
            } else {
                return redirect()->route('items.peminjaman')->with('error', 'Stok tidak mencukupi.');
            }
        } else {
            return redirect()->route('items.peminjaman')->with('error', 'Transaksi tidak ditemukan.');
        }
    }
    public function pengembalian()
    {
        $admin = User::with('admin.lab')
            ->where('id', Auth::user()->id)
            ->first();
        // $transaction = Transaction::with(['item.lab','user'])->get();
        $labId = $admin->admin->lab->id;
        $statuses = ['deny', 'done']; // Daftar status yang ingin Anda ambil

        $transactions = Transaction::with(['item.lab', 'user'])
            ->whereIn('status', $statuses)
            ->whereHas('item.lab', function ($query) use ($labId) {
                $query->where('id', $labId);
            })
            ->get();

        // dd($admin);
        return view('admin.pengembalian', [
            'data' => $transactions,
            'lab' => $admin->admin->lab->name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $lab = Lab::all();
        $barang = Items::with('lab')->find($id);
        // dd($barang);
        return view('admin.items_edit', [
            'lab' => $lab,
            'barang' => $barang,
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
            'stock' => 'required',
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
    public function destroy($id)
    {
        $item = Items::find($id);
        if (!$item) {
            return 'not found';
        }
        $item->delete();

        return redirect()->route('items.index');
    }
}
