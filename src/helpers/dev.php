<?php

if (!function_exists('_log')) {

	function _log($message,$data=NULL) {
		if (!env('APP_DEBUG')) return;
		//if (!defined('WP_DEBUG') || strtolower(WP_DEBUG) != 'true') return;
		if (in_array(strtolower(env('APP_ENV')),['stage','production'])) return;
		openlog("php", LOG_PID | LOG_PERROR, LOG_LOCAL0);
		$log = (is_string($message)) ? $message : var_export($message,TRUE);
		if ($data) $log .= ": ".var_export($data,TRUE);
		syslog(LOG_INFO,$log);
	}

}

if (!function_exists('_debug')) {

	function _debug($message="",$type='html') {
		if (!env('APP_DEBUG')) return;
		//if (!defined('WP_DEBUG') || strtolower(WP_DEBUG) != 'true') return;
		if (in_array(strtolower(env('APP_ENV')),['stage','production'])) return;
		$prefix = ""; $suffix = "";
		if ($type == 'txt') $prefix = "# ";
		if ($type == 'html') { $prefix = "<!-- "; $suffix = " -->"; };
		if ($message) { echo "{$prefix}{$message}{$suffix}"; return; }
	 	$trace = debug_backtrace(NULL,1);
	 	$file = preg_replace('/.*\/abetter\/(.*)\.(.*)$/',"$1",$trace[0]['file']);
	 	echo "{$prefix}include:{$file}{$suffix}";
	}

}

if (!function_exists('_console')) {

	function _console($var="",$tags=null) {
		if (!env('APP_DEBUG')) return;
		if (in_array(strtolower(env('APP_ENV')),['stage','production'])) return;
		if (!class_exists('\PhpConsole\Connector')) return;
		\PhpConsole\Connector::getInstance()->getDebugDispatcher()->dispatchDebug($var, $tags, 1);
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
