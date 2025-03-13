<?php

use Illuminate\Support\Facades\Route;
use Modules\Url\Http\Controllers\V1\UrlController;

Route::group(['prefix' => 'api', 'as' => 'api.'], function () {

    Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {

        Route::group(['prefix' => 'urls', 'as' => 'urls.'], function () {

            Route::post('encode', [UrlController::class, 'encode'])->name('encode');
            Route::post('decode', [UrlController::class, 'decode'])->name('decode');

        });

    });

});
