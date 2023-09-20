<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['verify' => true]);
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    Route::middleware(['checkUserRole:superadmin'])->group(function () {
        Route::get('/admin', [SuperAdminController::class, 'admin'])->name('admin.index');
        Route::get('/admin-create', [SuperAdminController::class, 'create_admin'])->name('admin.create');
        Route::post('/admin', [SuperAdminController::class, 'store_admin'])->name('admin.store');
        Route::delete('/admin/{id}', [SuperAdminController::class, 'destroy_admin'])->name('admin.destroy');
        Route::get('/admin/{id}/edit', [SuperAdminController::class, 'edit_admin'])->name('admin.edit');
        Route::patch('/admin/{id}/update', [SuperAdminController::class, 'update_admin'])->name('admin.update');

        Route::get('/mahasiswa', [SuperAdminController::class, 'mahasiswa'])->name('mahasiswa.index');
        Route::get('/mahasiswa-create', [SuperAdminController::class, 'create_mahasiswa'])->name('mahasiswa.create');
        Route::post('/mahasiswa', [SuperAdminController::class, 'store_mahasiswa'])->name('mahasiswa.store');
        Route::delete('/mahasiswa/{id}', [SuperAdminController::class, 'destroy_mahasiswa'])->name('mahasiswa.destroy');
        Route::get('/mahasiswa/{id}/edit', [SuperAdminController::class, 'edit_mahasiswa'])->name('mahasiswa.edit');
        Route::patch('/mahasiswa/{id}/update', [SuperAdminController::class, 'update_mahasiswa'])->name('mahasiswa.update');
    });

    Route::middleware(['checkUserRole:superadmin,admin'])->group(function () {
        Route::get('/items', [AdminController::class, 'index'])->name('items.index');
        Route::get('/items-create', [AdminController::class, 'create'])->name('items.create');
        Route::post('/items', [AdminController::class, 'store'])->name('items.store');
        Route::delete('/items/{id}', [AdminController::class, 'destroy'])->name('items.destroy');
        Route::get('/items/{id}/edit', [AdminController::class, 'edit'])->name('items.edit');
        Route::patch('/items/{id}/update', [AdminController::class, 'update'])->name('items.update');

        Route::get('/peminjaman/admin', [AdminController::class, 'peminjaman'])->name('items.peminjaman');
        Route::get('/peminjaman/admin/request', [AdminController::class, 'request_pinjaman'])->name('items.request_pinjaman');
        Route::post('/peminjaman/{id}/accept}', [AdminController::class, 'accept_pinjaman'])->name('items.accept_pinjaman');
        Route::post('/peminjaman/{id}/deny}', [AdminController::class, 'deny_pinjaman'])->name('items.deny_pinjaman');
        Route::post('/peminjaman/{id}/done}', [AdminController::class, 'done_pinjaman'])->name('items.done_pinjaman');

        Route::get('/pengembalian/admin', [AdminController::class, 'pengembalian'])->name('items.pengembalian');
    });

    Route::middleware(['checkUserRole:mahasiswa'])->group(function () {
        Route::get('/items-lab', [MahasiswaController::class, 'index'])->name('itemslab.index');
        Route::get('/items-lab/{item}', [MahasiswaController::class, 'items'])->name('itemslab.items');
        Route::post('/add-to-cart/{id}', [MahasiswaController::class, 'addToCart'])->name('itemslab.addToCart');
        Route::get('/my-cart', [MahasiswaController::class, 'my_cart'])->name('itemslab.myCart');
        Route::delete('/my-cart/{id}', [MahasiswaController::class, 'destroy_my_cart'])->name('itemslab.destroy_my_cart');
        Route::post('/my-cart/{id}/add', [MahasiswaController::class, 'add_item_my_cart'])->name('itemslab.add_item_my_cart');

        Route::post('/items-checkout', [MahasiswaController::class, 'checkout'])->name('itemslab.checkout');
        Route::get('/peminjaman', [MahasiswaController::class, 'peminjaman'])->name('itemslab.peminjaman');

    });
});
