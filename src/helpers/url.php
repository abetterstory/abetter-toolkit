<?php

if (!function_exists('_is_current')) {

	function _is_current($url, $return='current') {
		if ($item_hash = parse_url($url,PHP_URL_FRAGMENT) || preg_match('/^\#/',$url)) return '';
		if (($host = parse_url($url,PHP_URL_HOST)) && ($host != parse_url($_SERVER['REQUEST_URI'],PHP_URL_HOST))) return '';
		$item_path = rtrim(urldecode(parse_url($url,PHP_URL_PATH)),'/');
		$current_path = rtrim(urldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)),'/');
		return ($item_path == $current_path) ? $return : '';
	}

}

if (!function_exists('_is_front')) {

	function _is_front($url, $return='front') {
		return ($url == '/') ? $return : '';
	}

}

if (!function_exists('_relative')) {

	function _relative($url) {
		$rel = parse_url($url,PHP_URL_PATH);
		$rel .= (($q = parse_url($url,PHP_URL_QUERY)) ? "?{$q}" : "");
		$rel .= (($f = parse_url($url,PHP_URL_FRAGMENT)) ? "#{$f}" : "");
		$wp = ($env = env('WP_UPLOADS')) ? $env : '/wp/wp-content/uploads/';
		if (preg_match('|'.$wp.'|',$rel)) $rel = str_replace($wp,'/uploads/',$rel);
		return $rel;
	}

}

if (!function_exists('_base')) {

	function _base($url) {
		if (($base = env('APP_BASE')) && preg_match('/^\/.*/')) {
			$url = rtrim($base,'/').$url;
		}
		return $url;
	}

}

if (!function_exists('_slugify')) {

	function _slugify($url,$sep='-',$base=FALSE) {
		$url = ($base) ? basename($url) : preg_replace('/(\/|\?|\=|\&|\#)/',$sep,$url);
		$ext = strtolower(pathinfo($url,PATHINFO_EXTENSION));
		$name = strtolower(pathinfo($url,PATHINFO_FILENAME));
		$slug = Illuminate\Support\Str::slug($name,$sep);
		if (!$slug) $slug = "na{$sep}".Illuminate\Support\Str::random();
		if ($ext) $slug .= ".{$ext}";
		return $slug;
	}

}
