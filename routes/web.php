<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MotorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ClearDataController;
use App\Http\Controllers\OpiniController;
use App\Http\Controllers\TrendController;
use App\Http\Controllers\PredictionSettingController;


/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});
 /*
    |--------------------------------------------------------------------------
    | OTP 
    |--------------------------------------------------------------------------
    */
    Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('send.otp');

/*
|--------------------------------------------------------------------------
| Auth (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // ✅ DASHBOARD (1x aja, jangan duplicate)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Navbar pages
    Route::view('/prediksi', 'prediksi')->name('prediksi');
    Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan');    
    Route::get('/opini', [OpiniController::class, 'index'])->name('opini');
    Route::view('/stok', 'stok')->name('stok');
    

    /*
    |--------------------------------------------------------------------------
    | MOTOR CRUD
    |--------------------------------------------------------------------------
    */
    Route::get('/motor', [MotorController::class, 'index'])->name('motor.index');
    Route::get('/motor/create', [MotorController::class, 'create'])->name('motor.create');
    Route::post('/motor/store', [MotorController::class, 'store'])->name('motor.store');
    Route::get('/motor/{id}/edit', [MotorController::class, 'edit'])->name('motor.edit');
    Route::put('/motor/{id}', [MotorController::class, 'update'])->name('motor.update');
    Route::delete('/motor/{id}', [MotorController::class, 'destroy'])->name('motor.destroy');
    /*
    |--------------------------------------------------------------------------
    | penjualan CRUD & UPLOAD
    |--------------------------------------------------------------------------
    */
    Route::post('/penjualan/upload', [PenjualanController::class, 'upload'])->name('penjualan.upload');
    Route::post('/penjualan/simpan', [PenjualanController::class, 'simpan'])->name('penjualan.simpan');
    Route::post('/penjualan/clear', [PenjualanController::class, 'clear'])->name('penjualan.clear');

    
    /*
    |--------------------------------------------------------------------------
    | penjualan analisis & UPLOAD
    |--------------------------------------------------------------------------
    */
    Route::post('/penjualan/upload', [PenjualanController::class, 'upload'])->name('penjualan.upload');
    Route::post('/penjualan/simpan-raw', [PenjualanController::class, 'simpanRaw'])->name('penjualan.simpanRaw');
    Route::post('/penjualan/simpan-analisis', [PenjualanController::class, 'simpanAnalisis'])->name('penjualan.simpanAnalisis');
    Route::post('/penjualan/clear-preview', [PenjualanController::class, 'clear'])->name('penjualan.clear');
    Route::post('/penjualan/group', [PenjualanController::class, 'groupPreview'])->name('penjualan.group');
  
    //preview penjualan
    Route::get('/penjualan/tersimpan', [PenjualanController::class, 'tersimpan'])->name('penjualan.tersimpan');
    Route::get('/penjualan/tersimpan/data', [PenjualanController::class, 'loadData'])->name('penjualan.loadData');


    /*
    |--------------------------------------------------------------------------
    | OPINI
    |--------------------------------------------------------------------------
    */
    
    Route::get('/opini', [OpiniController::class, 'index'])->name('opini'); 
    Route::post('/opini/upload', [OpiniController::class, 'upload'])->name('opini.upload');
    Route::post('/opini/simpan', [OpiniController::class, 'simpan'])->name('opini.simpan');
    Route::post('/opini/clear', [OpiniController::class, 'clear'])->name('opini.clear');
    Route::get('/opini/tersimpan/data', [OpiniController::class, 'loadData'])->name('opini.loadData');


    /*
    |--------------------------------------------------------------------------
    | TREND
    |--------------------------------------------------------------------------
    */

    Route::get('/trend', [App\Http\Controllers\TrendController::class, 'index'])->name('trend.index');
    Route::post('/trend/generate', [App\Http\Controllers\TrendController::class, 'generate'])->name('trend.generate');
    Route::post('/trend/simpan', [App\Http\Controllers\TrendController::class, 'simpan'])->name('trend.simpan');
    Route::post('/trend/clear', [App\Http\Controllers\TrendController::class, 'clear'])->name('trend.clear');
    Route::get('/trend/tersimpan/data', [TrendController::class, 'loadData'])->name('trend.loadData');
    /*
    |--------------------------------------------------------------------------
    | PREDIKSI
    |--------------------------------------------------------------------------
    */
    Route::get('/prediction', [PredictionController::class, 'index'])->name('prediction.index');
    Route::post('/prediction/preview', [PredictionController::class, 'preview'])->name('prediction.preview');
    Route::post('/prediction/export', [PredictionController::class, 'export'])->name('prediction.export');

    
    //Clear All
    Route::post('/data/clear-all', [ClearDataController::class, 'clearAll'])->name('data.clearAll');
    Route::post('/data/clear-selected', [ClearDataController::class, 'clearSelected'])->name('data.clearSelected');
    Route::get('/data/dataset-names', [ClearDataController::class, 'getDatasetNames'])->name('data.datasetNames');

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD AJAX
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard/data', [DashboardController::class, 'dashboardData']);

    /*
    |--------------------------------------------------------------------------
    | STOK
    |--------------------------------------------------------------------------
    */
    Route::get('/stok', [StokController::class, 'index'])->name('stok.index');
    Route::post('/stok/update', [StokController::class, 'update'])->name('stok.update');
    Route::post('/stok/upload', [StokController::class, 'upload'])->name('stok.upload');
    Route::post('/stok/reset', [StokController::class, 'reset'])->name('stok.reset');
    Route::post('/stok/simpan', [StokController::class, 'simpanPreview'])->name('stok.simpan');
    Route::post('/stok/clear-preview', [StokController::class, 'clearPreview'])->name('stok.clear');
    Route::get('/stok/tersimpan/data', [StokController::class, 'loadData'])->name('stok.loadData');
    });

    //CONFIGURABLE PREDICTION SETTINGS
    Route::get('/settings/prediction', [PredictionSettingController::class, 'index'])->name('settings.prediction');
    Route::post('/settings/prediction', [PredictionSettingController::class, 'update'])->name('settings.prediction.update');    
    Route::get('/settings/prediction', [PredictionSettingController::class, 'index'])->name('settings.prediction');
    Route::post('/settings/prediction', [PredictionSettingController::class, 'update'])->name('settings.prediction.update');
    Route::post('/settings/prediction/reset', [PredictionSettingController::class, 'reset'])->name('settings.prediction.reset');
/*
|--------------------------------------------------------------------------
| tombol welcome (non-authenticated)
|--------------------------------------------------------------------------
*/
    Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});