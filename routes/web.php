<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/host', [App\Http\Controllers\HomeController::class, 'host'])->name('host');

Route::post('/create_base', [App\Http\Controllers\HomeController::class, 'create_base'])->name('create_base');

Route::post('/create_grupo', [App\Http\Controllers\HomeController::class, 'create_grupo'])->name('create_grupo');

Route::get('/database', [App\Http\Controllers\HomeController::class, 'database'])->name('database');

Route::get('/schema', [App\Http\Controllers\HomeController::class, 'schema'])->name('schema');

Route::get('/tabla', [App\Http\Controllers\HomeController::class, 'tabla'])->name('tabla');

Route::get('/export_excel', [App\Http\Controllers\HomeController::class, 'export_excel'])->name('export_excel');

Route::get('/store', [App\Http\Controllers\HomeController::class, 'store'])->name('home.store');

Route::get('/destroy/{id}', [App\Http\Controllers\HomeController::class, 'destroy'])->name('home.destroy');

Route::get('/edit/{id}', [App\Http\Controllers\HomeController::class, 'edit'])->name('home.edit');

//Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('/ajax_columna',[App\Http\Controllers\AjaxController::class, 'ajax_columna'])->name('ajax_columna');

Route::get('/verificar_host',[App\Http\Controllers\AjaxController::class, 'ajax_host'])->name('verificar_host');

Route::get('/verificar_grupo',[App\Http\Controllers\AjaxController::class, 'ajax_grupo'])->name('verificar_grupo');

Route::get('/get_bases_string',[App\Http\Controllers\AjaxController::class, 'ajax_get_bases_string'])->name('get_bases_string');

Route::get('/buscador_string',[App\Http\Controllers\BuscadorController::class, 'buscador_string'])->name('buscador_string');