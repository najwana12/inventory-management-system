<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TransactionInController;
use App\Http\Controllers\TransactionOutController;
use App\Http\Controllers\ReportGoodsInController;
use App\Http\Controllers\ReportGoodsOutController;
use App\Http\Controllers\ReportStockController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportFinancialController;

Route::middleware(["localization"])->group(function () {

    Route::get('/', [LoginController::class, 'index'])->name('login');
    Route::post('/', [LoginController::class, 'auth'])->name('login.auth');

});

Route::middleware(['auth', "localization"])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | KHUSUS ADMIN & SUPER ADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware(['employee.middleware'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | BARANG
        |--------------------------------------------------------------------------
        */

        Route::controller(ItemController::class)->prefix("barang")->group(function () {

            Route::get('/', 'index')->name('barang');
            Route::post('/kode', 'detailByCode')->name('barang.code');
            Route::get('/daftar-barang', 'list')->name('barang.list');

            Route::post('/simpan', 'save')->name('barang.save');
            Route::post('/info', 'detail')->name('barang.detail');
            Route::post('/ubah', 'update')->name('barang.update');
            Route::delete('/hapus', 'delete')->name('barang.delete');
        });

        /*
        |--------------------------------------------------------------------------
        | JENIS BARANG
        |--------------------------------------------------------------------------
        */

        Route::controller(CategoryController::class)->prefix("barang/jenis")->group(function () {

            Route::get('/', 'index')->name('barang.jenis');
            Route::get('/daftar', 'list')->name('barang.jenis.list');

            Route::post('/simpan', 'save')->name('barang.jenis.save');
            Route::post('/info', 'detail')->name('barang.jenis.detail');
            Route::put('/ubah', 'update')->name('barang.jenis.update');
            Route::delete('/hapus', 'delete')->name('barang.jenis.delete');
        });

        /*
        |--------------------------------------------------------------------------
        | SATUAN BARANG
        |--------------------------------------------------------------------------
        */

        Route::controller(UnitController::class)->prefix('/barang/satuan')->group(function () {

            Route::get('/', 'index')->name('barang.satuan');
            Route::get('/daftar', 'list')->name('barang.satuan.list');

            Route::post('/simpan', 'save')->name('barang.satuan.save');
            Route::post('/info', 'detail')->name('barang.satuan.detail');
            Route::put('/ubah', 'update')->name('barang.satuan.update');
            Route::delete('/hapus', 'delete')->name('barang.satuan.delete');
        });

        /*
        |--------------------------------------------------------------------------
        | MERK BARANG
        |--------------------------------------------------------------------------
        */

        Route::controller(BrandController::class)->prefix("/barang/merk")->group(function () {

            Route::get('/', 'index')->name('barang.merk');
            Route::get('/daftar', 'list')->name('barang.merk.list');

            Route::post('/simpan', 'save')->name('barang.merk.save');
            Route::post('/info', 'detail')->name('barang.merk.detail');
            Route::put('/ubah', 'update')->name('barang.merk.update');
            Route::delete('/hapus', 'delete')->name('barang.merk.delete');
        });

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        */

        Route::controller(CustomerController::class)->prefix('/customer')->group(function () {

            Route::get('/', 'index')->name('customer');
            Route::get('/daftar', 'list')->name('customer.list');

            Route::post('/simpan', 'save')->name('customer.save');
            Route::post('/info', 'detail')->name('customer.detail');
            Route::put('/ubah', 'update')->name('customer.update');
            Route::delete('/hapus', 'delete')->name('customer.delete');
        });

        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        Route::controller(SupplierController::class)->prefix('/supplier')->group(function () {

            Route::get('/', 'index')->name('supplier');
            Route::get('/daftar', 'list')->name('supplier.list');

            Route::post('/simpan', 'save')->name('supplier.save');
            Route::post('/info', 'detail')->name('supplier.detail');
            Route::put('/ubah', 'update')->name('supplier.update');
            Route::delete('/hapus', 'delete')->name('supplier.delete');
        });

        /*
        |--------------------------------------------------------------------------
        | TRANSAKSI MASUK
        |--------------------------------------------------------------------------
        */

        Route::controller(TransactionInController::class)
            ->prefix('/transaksi/masuk')
            ->group(function () {

                Route::get('/', 'index')->name('transaksi.masuk');
                Route::get('/list', 'list')->name('transaksi.masuk.list');

                Route::post('/save', 'save')->name('transaksi.masuk.save');
                Route::post('/detail', 'detail')->name('transaksi.masuk.detail');

                Route::put('/update', 'update')->name('transaksi.masuk.update');
                Route::delete('/delete', 'delete')->name('transaksi.masuk.delete');

                Route::get('/barang/list/in', 'listIn')->name('barang.list.in');

                // VERIFY KHUSUS ADMIN
                Route::post('/verify', 'verifyIn')
                    ->middleware('employee.middleware')
                    ->name('transaksi.masuk.verify');

                // VALIDASI KHUSUS SUPER ADMIN
                Route::post('/validasi', 'validateIn')
                    ->middleware('super.admin')
                    ->name('transaksi.masuk.validate');

                // REJECT KHUSUS SUPER ADMIN
                Route::post('/reject', 'reject')
                    ->middleware('super.admin')
                    ->name('transaksi.masuk.reject');
            });

        /*
        |--------------------------------------------------------------------------
        | TRANSAKSI KELUAR
        |--------------------------------------------------------------------------
        */

        Route::controller(TransactionOutController::class)
            ->prefix('/transaksi/keluar')
            ->group(function () {

                Route::get('/', 'index')->name('transaksi.keluar');
                Route::get('/list', 'list')->name('transaksi.keluar.list');

                Route::post('/simpan', 'save')->name('transaksi.keluar.save');
                Route::post('/info', 'detail')->name('transaksi.keluar.detail');

                Route::put('/ubah', 'update')->name('transaksi.keluar.update');
                Route::delete('/hapus', 'delete')->name('transaksi.keluar.delete');

                // TAMBAHAN
                Route::get('/barang/list/out', 'listOut')
                    ->name('barang.list.out');

                // VALIDASI KHUSUS SUPER ADMIN
                Route::post('/validasi', 'validateOut')
                    ->middleware('super.admin')
                    ->name('transaksi.keluar.validate');

                // VERIFY KHUSUS ADMIN
                Route::post('/verify', 'verifyOut')
                    ->middleware('employee.middleware')
                    ->name('transaksi.keluar.verify');

                // REJECT KHUSUS SUPER ADMIN
                Route::post('/reject', 'reject')
                    ->middleware('super.admin')
                    ->name('transaksi.keluar.reject');
            });

        /*
        |--------------------------------------------------------------------------
        | PENGATURAN USER
        |--------------------------------------------------------------------------
        */

        Route::controller(EmployeeController::class)
            ->prefix('/settings/employee')
            ->group(function () {

                Route::get('/', 'index')->name('settings.employee');
                Route::get('/list', 'list')->name('settings.employee.list');

                Route::post('/save', 'save')->name('settings.employee.save');
                Route::post('/detail', 'detail')->name('settings.employee.detail');

                Route::put('/update', 'update')->name('settings.employee.update');
                Route::delete('/delete', 'delete')->name('settings.employee.delete');
            });
    });

    /*
    |--------------------------------------------------------------------------
    | LAPORAN (BOLEH DIAKSES STAFF & PIMPINAN)
    |--------------------------------------------------------------------------
    */

    // laporan barang masuk
    Route::controller(ReportGoodsInController::class)
        ->prefix('/laporan/masuk')
        ->group(function () {

            Route::get('/', 'index')->name('laporan.masuk');
            Route::get('/list', 'list')->name('laporan.masuk.list');
        });

    // laporan barang keluar
    Route::controller(ReportGoodsOutController::class)
        ->prefix('/laporan/keluar')
        ->group(function () {

            Route::get('/', 'index')->name('laporan.keluar');
            Route::get('/list', 'list')->name('laporan.keluar.list');
        });

    // laporan stok
    Route::controller(ReportStockController::class)
        ->prefix('/laporan/stok')
        ->group(function () {

            Route::get('/', 'index')->name('laporan.stok');
            Route::get('/list', 'list')->name('laporan.stok.list');
            Route::get('/grafik', 'grafik')->name('laporan.stok.grafik');
        });

    /*
    |--------------------------------------------------------------------------
    | LAPORAN PENDAPATAN
    |--------------------------------------------------------------------------
    */

    Route::get('/master/laporan/income', [ReportFinancialController::class, 'income'])
        ->name('laporan.pendapatan');

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/settings/profile', [ProfileController::class, 'index'])
        ->name('settings.profile');

    Route::post('/settings/profile', [ProfileController::class, 'update'])
        ->name('settings.profile.update');

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    Route::get('/logout', [LoginController::class, 'logout'])
        ->name('login.delete');
});