<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentoController;

Route::get('/', function () {
    return view('home.welcome');
});


Route::get('/documentos', function () {
    if (! auth()->check()) {
        return redirect('/')->with('products-nologin-alert', 'Necesitas iniciar sesion para ver tus documentos!');
    }

    return view('documents.documents');
});

Route::get('/subir', function () {
    if (! auth()->check()) {
        return redirect('/')->with('products-nologin-alert', 'Necesitas iniciar sesion para esta opcion!');
    }

    return view('documents.subir');
});



Route::post('/document/upload', [DocumentoController::class, 'upload'])->name('document.upload');

Route::get('/document/edit/{id}', [DocumentoController::class, 'edit'])->name('document.edit');
Route::get('/document/download/{id}', [DocumentoController::class, 'download'])->name('document.download');
Route::get('/document/delete/{id}', [DocumentoController::class, 'delete'])->name('document.delete');
  