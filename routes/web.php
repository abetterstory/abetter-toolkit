<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::any('image/{style?}/{path}', '\ABetterToolkitController@handle')->where('path','.*');
Route::any('proxy/{path}', '\ABetterToolkitController@handle')->where('path','.*');
Route::any('browsersync/{event?}/{path}', '\ABetterToolkitController@handle')->where('path','.*');
Route::any('service/{path?}.{type}', '\ABetterToolkitController@handle')->where(['path'=>'.*','type'=>'json']);
