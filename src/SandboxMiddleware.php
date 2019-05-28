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

		if (env('WP_LOGIN') && env('APP_ENV') != 'production') {
			$pass = (preg_match('/_logged_in_/',$_SERVER['HTTP_COOKIE']??'')) ? TRUE : FALSE;
			if (!$pass) {
				@header('Location:/wp/wp-login.php?redirect_to='.$_SERVER['REQUEST_URI']);
				exit;
			}
		}

		return $next($request);

	}

	// ---

	public function deleteFiles($path,$rmdir=TRUE) {
		$i = new \DirectoryIterator($path);
        foreach ($i AS $f) {
            if ($f->isFile()) {
                @unlink($f->getRealPath());
            } else if (!$f->isDot() && $f->isDir()) {
                $this->deleteFiles($f->getRealPath());
            }
        }
        if ($rmdir) @rmdir($path);
	}

}
