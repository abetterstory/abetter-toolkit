<?php

namespace ABetter\Toolkit;

use Closure;

class AfterMiddleware {

	public function handle($request, Closure $next) {

		$response = $next($request);

		if ($base = env('APP_BASE')) {
			$content = $this->filterRootBase($response->getContent(),$base);
			$response->setContent($content);
		}

		return $response;

	}

	// ---

	public function filterRootBase($content,$base) {
		$base = rtrim($base,'/');
		if (preg_match_all('/<link[^>]+href=\"(\/[^\"]+)\"/',$content,$links)) $content = $this->replaceRootBase($content,$links[1],$base);
		if (preg_match_all('/<javascript[^>]+src=\"(\/[^\"]+)\"/',$content,$scripts)) $content = $this->replaceRootBase($content,$scripts[1],$base);
		if (preg_match_all('/<img[^>]+src=\"(\/[^\"]+)\"/',$content,$images)) $content = $this->replaceRootBase($content,$images[1],$base);
		return $content;
	}

	public function replaceRootBase($content,$urls,$base) {
		foreach ($urls AS $url) {
			$content = str_replace($url,$base.$url,$content);
		}
		return $content;
	}

}
