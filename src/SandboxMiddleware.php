<?php

namespace ABetter\Toolkit;

use Closure;

class SandboxMiddleware {

	public function handle($request, Closure $next) {

		if (env('APP_ENV') == 'sandbox') {

			$dir = app('path.storage').'/framework/views/';
			$files = glob($dir.'*');

			foreach($files AS $file) {
	            if (is_file($file)) @unlink($file);
	        }

			if (function_exists('clock')) clock('sandbox:view:clear');

		}

		return $next($request);

	}

}
