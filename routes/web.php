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

//----------------------------- Ruta home ---------------------------------------------//
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//----------------------------- Rutas conexion inicial ---------------------------------------------//
Route::post('/host', [App\Http\Controllers\ConexionInicialController::class, 'host'])->name('host');
Route::get('/database', [App\Http\Controllers\ConexionInicialController::class, 'database'])->name('database');
Route::get('/schema', [App\Http\Controllers\ConexionInicialController::class, 'schema'])->name('schema');

//----------------------------- Rutas registro de las bases y grupos ---------------------------------------------//
//////////////////// Bases ///////////////////////////////
Route::get('/grupos_bases', [App\Http\Controllers\HostRegisterController::class, 'grupos_bases'])->name('grupos_bases');
Route::post('/create_base', [App\Http\Controllers\HostRegisterController::class, 'create_base'])->name('create_base');
Route::post('/edit_base/{id}', [App\Http\Controllers\HostRegisterController::class, 'edit_base'])->name('edit_base');
Route::post('/delete_base/{id}', [App\Http\Controllers\HostRegisterController::class, 'delete_base'])->name('delete_base');
Route::get('/exportar_grupos_bases_excel', [App\Http\Controllers\HostRegisterController::class, 'exportar_grupos_bases_excel'])->name('exportar_grupos_bases_excel');

//////////////////// Grupos ///////////////////////////////
Route::post('/create_grupo', [App\Http\Controllers\HostRegisterController::class, 'create_grupo'])->name('create_grupo');
Route::post('/edit_grupo/{id}', [App\Http\Controllers\HostRegisterController::class, 'edit_grupo'])->name('edit_grupo');
Route::post('/delete_grupo/{id}', [App\Http\Controllers\HostRegisterController::class, 'delete_grupo'])->name('delete_grupo');

//----------------------------- Rutas manejo de registros ---------------------------------------------//
Route::get('/tabla', [App\Http\Controllers\RegistrosController::class, 'tabla'])->name('tabla');
Route::get('/export_excel', [App\Http\Controllers\RegistrosController::class, 'export_excel'])->name('export_excel');
Route::get('/store', [App\Http\Controllers\RegistrosController::class, 'store'])->name('home.store');
Route::get('/destroy/{id}', [App\Http\Controllers\RegistrosController::class, 'destroy'])->name('home.destroy');
Route::get('/edit/{id}', [App\Http\Controllers\RegistrosController::class, 'edit'])->name('home.edit');

//----------------------------- Rutas ajax ---------------------------------------------//
Route::get('/ajax_columna',[App\Http\Controllers\AjaxController::class, 'ajax_columna'])->name('ajax_columna');
Route::get('/verificar_host',[App\Http\Controllers\AjaxController::class, 'ajax_host'])->name('verificar_host');
Route::get('/verificar_grupo',[App\Http\Controllers\AjaxController::class, 'ajax_grupo'])->name('verificar_grupo');
Route::get('/get_bases_string',[App\Http\Controllers\AjaxController::class, 'ajax_get_bases_string'])->name('get_bases_string');

//----------------------------- Rutas buscador de palabras en la base (en desarrollo) ---------------------------------------------//
Route::get('/buscador_string',[App\Http\Controllers\BuscadorController::class, 'buscador_string'])->name('buscador_string');

//----------------------------- En desuso ---------------------------------------------//
//Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');