<?php

// Legacy Laravel 6 -> 5

if (!function_exists('_array_except')) {

	function _array_except($array,$except) {
		return (class_exists('Arr')) ? Arr::except($array,$except) : array_except($array,$except);
	}

}

if (!function_exists('_array_last')) {

	function _array_last($array) {
		return (class_exists('Arr')) ? Arr::last($array) : array_last($array);
	}

}
