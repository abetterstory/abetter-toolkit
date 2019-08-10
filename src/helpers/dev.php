<?php

if (!function_exists('_log')) {

	function _log($message,$data=NULL) {
		if (!defined('WP_DEBUG') || strtolower(WP_DEBUG) != 'true') return;
		if (in_array(env('APP_ENV'),['stage','production'])) return;
		openlog("php", LOG_PID | LOG_PERROR, LOG_LOCAL0);
		$log = (is_string($message)) ? $message : var_export($message,TRUE);
		if ($data) $log .= ": ".var_export($data,TRUE);
		syslog(LOG_INFO,$log);
	}

}

if (!function_exists('_debug')) {

	function _debug($message="",$type='html') {
		if (!defined('WP_DEBUG') || strtolower(WP_DEBUG) != 'true') return;
		if (in_array(env('APP_ENV'),['stage','production'])) return;
		$prefix = ""; $suffix = "";
		if ($type == 'txt') $prefix = "# ";
		if ($type == 'html') { $prefix = "<!-- "; $suffix = " -->"; };
		if ($message) { echo "{$prefix}{$message}{$suffix}"; return; }
	 	$trace = debug_backtrace(NULL,1);
	 	$file = preg_replace('/.*\/abetter\/(.*)\.(.*)$/',"$1",$trace[0]['file']);
	 	echo "{$prefix}include:{$file}{$suffix}";
	}

}

// ---

if (!function_exists('_is_dev')) {

	function _is_dev() {
		return (in_array(strtolower(env('APP_ENV')),['stage','production'])) ? FALSE : TRUE;
	}

}

if (!function_exists('_is_live')) {

	function _is_live() {
		return (in_array(strtolower(env('APP_ENV')),['stage','production'])) ? TRUE : FALSE;
	}

}

if (!function_exists('_is_stage')) {

	function _is_stage() {
		return (strtolower(env('APP_ENV')) == 'stage') ? TRUE : FALSE;
	}

}

if (!function_exists('_is_production')) {

	function _is_production() {
		return (strtolower(env('APP_ENV')) == 'production') ? TRUE : FALSE;
	}

}
