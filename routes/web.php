<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::get('/mahasiswa', function () {
    return view('admin.mahasiswa');
});

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', function () {
        return view('superadmin.index');
    });

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
    Route::middleware(['checkUserRole:admin'])->group(function () {
        Route::get('/items', [AdminController::class, 'index'])->name('items.index');
        Route::get('/items-create', [AdminController::class, 'create'])->name('items.create');
        Route::post('/items', [AdminController::class, 'store'])->name('items.store');
        Route::delete('/items/{id}', [AdminController::class, 'destroy'])->name('items.destroy');
        Route::get('/items/{id}/edit', [AdminController::class, 'edit'])->name('items.edit');
        Route::patch('/items/{id}/update', [AdminController::class, 'update'])->name('items.update');
    });
    Route::middleware(['checkUserRole:mahasiswa'])->group(function () {
        Route::get('/items-lab', [MahasiswaController::class, 'index'])->name('itemslab.index');
        Route::get('/items-lab/{item}', [MahasiswaController::class, 'items'])->name('itemslab.items');
        Route::post('/add-to-cart/{id}', [MahasiswaController::class, 'addToCart'])->name('itemslab.addToCart');
        Route::get('/my-cart', [MahasiswaController::class, 'my_cart'])->name('itemslab.myCart');
    });
});
