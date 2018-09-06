<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::any('api/browsersync/{event?}/{path}', '\ABetterToolkitController@handle')->where('path','.*');
