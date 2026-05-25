<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController, MotorController, DashboardController, 
    PredictionController, OtpController, StokController, 
    PenjualanController, ClearDataController, OpiniController, 
    TrendController, PredictionSettingController, UserController
};

/*
|--------------------------------------------------------------------------
| 1. PUBLIC ROUTES (Tanpa Login)
|--------------------------------------------------------------------------
*/
Route::get('/', function () { return view('welcome'); })->name('welcome');
Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('send.otp');

// Breeze Auth Routes
require __DIR__.'/auth.php';


/*
|--------------------------------------------------------------------------
| 2. PROTECTED ROUTES (Harus Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /* --- 👤 PROFILE ROUTES (Bawaan Breeze) --- */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* --- 🟢 AKSES SEMUA ROLE (Superadmin, Admin, User) --- */
    /* Fitur: Hanya Lihat (Index) & Dashboard */
    Route::middleware('role:superadmin,admin,user')->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardController::class, 'dashboardData']);

        // Halaman Utama (Index Only)
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
        Route::get('/opini', [OpiniController::class, 'index'])->name('opini.index'); 
        Route::get('/trend', [TrendController::class, 'index'])->name('trend.index');
        Route::get('/prediction', [PredictionController::class, 'index'])->name('prediction.index');
        
        // Fitur Read-Only Tambahan
        Route::get('/penjualan/tersimpan', [PenjualanController::class, 'tersimpan'])->name('penjualan.tersimpan');
        Route::get('/penjualan/tersimpan/data', [PenjualanController::class, 'loadData'])->name('penjualan.loadData');
        Route::get('/stok/tersimpan/data', [StokController::class, 'loadData'])->name('stok.loadData');
        Route::get('/trend/tersimpan/data', [TrendController::class, 'loadData'])->name('trend.loadData');
        Route::get('/opini/tersimpan/data', [OpiniController::class, 'loadData'])->name('opini.loadData');
    });


    /* --- 🔴 AKSES KHUSUS ADMIN & SUPERADMIN --- */
    /* Fitur: Create, Update, Delete, Upload, Settings, Management */
    Route::middleware('role:superadmin,admin')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

        // Motor CRUD (Full)
        Route::resource('motor', MotorController::class);

        // Penjualan Actions
        Route::post('/penjualan/upload', [PenjualanController::class, 'upload'])->name('penjualan.upload');
        Route::post('/penjualan/simpan-raw', [PenjualanController::class, 'simpanRaw'])->name('penjualan.simpanRaw');
        Route::post('/penjualan/simpan-analisis', [PenjualanController::class, 'simpanAnalisis'])->name('penjualan.simpanAnalisis');
        Route::post('/penjualan/clear', [PenjualanController::class, 'clear'])->name('penjualan.clear');
        Route::post('/penjualan/group', [PenjualanController::class, 'groupPreview'])->name('penjualan.group');

        // Stok Actions
        Route::post('/stok/update', [StokController::class, 'update'])->name('stok.update');
        Route::post('/stok/upload', [StokController::class, 'upload'])->name('stok.upload');
        Route::post('/stok/reset', [StokController::class, 'reset'])->name('stok.reset');
        Route::post('/stok/simpan', [StokController::class, 'simpanPreview'])->name('stok.simpan');
        Route::post('/stok/clear-preview', [StokController::class, 'clearPreview'])->name('stok.clear');

        // Opini Actions
        Route::post('/opini/upload', [OpiniController::class, 'upload'])->name('opini.upload');
        Route::post('/opini/simpan', [OpiniController::class, 'simpan'])->name('opini.simpan');
        Route::post('/opini/clear', [OpiniController::class, 'clear'])->name('opini.clear');

        // Trend Actions
        Route::post('/trend/generate', [TrendController::class, 'generate'])->name('trend.generate');
        Route::post('/trend/simpan', [TrendController::class, 'simpan'])->name('trend.simpan');
        Route::post('/trend/clear', [TrendController::class, 'clear'])->name('trend.clear');

        // Prediksi Actions
        Route::get('/prediction/preview', fn() => redirect()->route('prediction.index')); 
        Route::post('/prediction/preview', [PredictionController::class, 'preview'])->name('prediction.preview');
        Route::get('/prediction/export', fn() => redirect()->route('prediction.index'));  

        Route::post('/prediction/export', [PredictionController::class, 'export'])->name('prediction.export');

        // Management Data (Clear All)
        Route::get('/management-data', [ClearDataController::class, 'index'])->name('management.index');
        Route::post('/data/clear-all', [ClearDataController::class, 'clearAll'])->name('data.clearAll');
        Route::post('/data/clear-selected', [ClearDataController::class, 'clearSelected'])->name('data.clearSelected');
        Route::get('/data/dataset-names', [ClearDataController::class, 'getDatasetNames'])->name('data.datasetNames');

        // Configurable Prediction Settings
        Route::get('/settings/prediction', [PredictionSettingController::class, 'index'])->name('settings.prediction');
        Route::post('/settings/prediction/update', [PredictionSettingController::class, 'update'])->name('settings.prediction.update');
        Route::post('/settings/prediction/reset', [PredictionSettingController::class, 'reset'])->name('settings.prediction.reset');
    });


    
});