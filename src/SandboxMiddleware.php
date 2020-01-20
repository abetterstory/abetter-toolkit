<?php

namespace ABetter\Toolkit;

use Closure;

class SandboxMiddleware {

	public function handle($request, Closure $next) {

		if (env('APP_ENV') == 'sandbox' || isset($_GET['clearcache'])) {
			$this->deleteFiles(app('path.storage').'/framework/views/',FALSE);
		}

		if (isset($_GET['clearcache'])) {
			$this->deleteFiles(app('path.storage').'/cache/',FALSE);
		}

		if (!in_array(strtolower(env('APP_ENV')),['production','stage'])) {
			app()->register('ABetter\Toolkit\PhpConsoleServiceProvider');
		}

		return $next($request);

	}

	// ---

	public function deleteFiles($path,$rmdir=TRUE) {
		$i = new \DirectoryIterator($path);
        foreach ($i AS $f) {
			if ($f->isFile() && !preg_match('/^\./',$f->getFilename())) {
                @unlink($f->getRealPath());
            } else if (!$f->isDot() && $f->isDir()) {
                $this->deleteFiles($f->getRealPath());
            }
        }
        if ($rmdir) @rmdir($path);
	}

}
