<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.welcome');
});



// Route::get('/documentos', function () {
//     if (! auth()->check()) {
//       return view('home.welcome');
//     }
  
//     return view('documents.documents');
//   });


Route::get('/documentos', function () {
    if (! auth()->check()) {
        return redirect('/')->with('products-nologin-alert', 'Necesitas iniciar sesion para ver tus documentos!');
    }

    return view('documents.documents');
});