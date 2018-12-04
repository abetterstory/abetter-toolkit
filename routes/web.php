<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::any('browsersync/{event?}/{path}', '\ABetterToolkitController@handle')->where('path','.*');
Route::any('image/{style?}/{path}', '\ABetterToolkitController@handle')->where('path','.*');
Route::any('proxy/{path}', '\ABetterToolkitController@handle')->where('path','.*');
Route::any('aws/{path?}.{type}', '\ABetterToolkitController@handle')->where(['path'=>'.*','type'=>'json']);
