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
        $userId = Auth::user()->id;
        $admin = User::with('admin')
            ->where('id', $userId)
            ->first();

        if (!$admin) {
            return abort(403);
        }
        $lab = Lab::find($admin->admin->lab_id);
        if (!$lab) {
            return abort(403);
        }
        $data = Items::where('lab_id', $lab->id)
            ->with('lab')
            ->get();
        return view('admin.items', [
            'data' => $data,
            'admin' => $lab,
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
        $transaction = Transaction::find($id);
        $transaction->status = 'accept';
        $transaction->save();
        return redirect()->route('items.request_pinjaman');
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
        $transaction = Transaction::find($id);
        $transaction->status = 'done';
        $transaction->save();
        return redirect()->route('items.peminjaman');
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
