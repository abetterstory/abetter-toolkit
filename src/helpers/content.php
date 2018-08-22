<?php

if (!function_exists('_excerpt')) {

	function _excerpt($str="",$max=150,$suffix="…",$breakspace=TRUE,$striphead=TRUE) {
		if ($striphead) $str = preg_replace('/<h[123456][^>]*?>.*?<\/h[123456]>/si',' ',$str);
		$str = preg_replace('/ +/'," ",trim(strip_tags($str)));
		if (mb_strlen($str) <= $max) return $str;
		$e = trim(mb_substr($str,0,$max-3),".");
		$e = ($breakspace && ($b = mb_substr($e,0,mb_strrpos($e," ")))) ? $b : $e;
		return $e.$suffix;
	}

}
