<?php

if (!function_exists('_is_current')) {

	function _is_current($url, $return='current') {
		if ($item_hash = parse_url($url,PHP_URL_FRAGMENT)) return '';
		$item_path = rtrim(urldecode(parse_url($url,PHP_URL_PATH)),'/');
		$current_path = rtrim(urldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)),'/');
		return ($item_path == $current_path) ? $return : '';
	}

}

if (!function_exists('_relative')) {

	function _relative($url) {
		$rel = parse_url($url,PHP_URL_PATH);
		$rel .= (($q = parse_url($url,PHP_URL_QUERY)) ? "?{$q}" : "");
		$rel .= (($f = parse_url($url,PHP_URL_FRAGMENT)) ? "#{$f}" : "");
		return $rel;
	}

}
