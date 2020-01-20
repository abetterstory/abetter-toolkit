<?php

// Legacy Laravel 5 -> 6

if (class_exists('Arr') && !function_exists('array_except')) {

	function array_except($array,$except) {
		return Arr::except($array,$except);
	}

}
