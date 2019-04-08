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
		$prefix = ($type == 'html') ? "<!-- " : "# ";
		$suffix = ($type == 'html') ? " -->" : "";
		if ($message) echo "{$prefix}{$message}{$suffix}"; return;
	 	$trace = debug_backtrace(NULL,1);
	 	$file = preg_replace('/.*\/abetter\/(.*)\.(.*)$/',"$1",$trace[0]['file']);
	 	echo "{$prefix}include:{$file}{$suffix}";
	}

}
