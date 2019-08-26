<?php

namespace ABetter\Toolkit;

use ABetter\Toolkit\Service as BaseService;

class ProxyService extends BaseService {

	public function handle() {

		$opt = (isset($this->args[0][0])) ? $this->args[0][0] : [];

		$opt['base'] = ($base = env('APP_BASE') ?? env('APP_CANONICAL') ?? env('APP_URL')) ? rtrim(preg_replace('/https?\:\/\//','',$base),'/') : '';
		$opt['storage'] = storage_path('cache').'/proxy';
		$opt['file'] = $opt['file'] ?? NULL;
		$opt['file'] = trim($opt['file'],'/');
		$opt['files'] = [];
		$opt['sources'] = [];
		$opt['content'] = "";

		$opt['v'] = (!empty($_GET['id'])) ? $_GET['id'].'-' : "";

		if (!preg_match('/^.+\..+\//',$opt['file'])) {
			$opt['file'] = request()->getSchemeAndHttpHost().'/'.$opt['file'];
		}

		if ((preg_match('/(.*)\[([^\]]+)\](.*)/',$opt['file'],$m)) && ($ms = explode(',',$m[2]))) {
			foreach ($ms AS $f) $opt['files'][] = "{$m[1]}{$f}{$m[3]}";
		}

		if (!$opt['files']) $opt['files'] = [$opt['file']];

		foreach ($opt['files'] AS $f) {
			$opt['sources'][] = (preg_match('/^https?\:\/\//',$f)) ? $f : 'https://'.$f;
		}

		$opt['target'] = $opt['storage'].'/'.preg_replace('/\:|\/|\?|\=/','_',$opt['v'].$opt['file']);

		if (!is_file($opt['target'])) {
			foreach ($opt['sources'] AS $s) $opt['content'] .= @file_get_contents($s);
			if (!$opt['content']) return abort(404);
			if (!is_dir($opt['storage'])) \File::makeDirectory($opt['storage'],0777,TRUE);
			$opt['content'] = $this->filter($opt['content'],$opt);
			@file_put_contents($opt['target'],$opt['content']);
			@chmod($opt['target'],0755);
		}

		$this->file = $opt['target'];
		$this->expire = '1 year';
		$this->handled = TRUE;

	}

	// ---

	public function filter($content,$opt) {
		$base = $opt['base'].'/proxy/';
		$content = str_replace([
			'https://www.google-analytics.com','"www.google-analytics.com',"'www.google-analytics.com",
			'https://www.googleadservices.com','"www.googleadservices.com',"'www.googleadservices.com",
			'https://www.googletagmanager.com','"www.googletagmanager.com',"'www.googletagmanager.com",
			'https://connect.facebook.net','"connect.facebook.net',"'connect.facebook.net",
			//'https://www.facebook.com','"www.facebook.com',"'www.facebook.com",
			//'https://px.ads.linkedin.com','"px.ads.linkedin.com',"'px.ads.linkedin.com",
			//'https://tb.de17a.com','"tb.de17a.com',"'tb.de17a.com",
		],[
			'/proxy/www.google-analytics.com','"'.$base.'www.google-analytics.com',"'".$base.'www.google-analytics.com',
			'/proxy/www.googleadservices.com','"'.$base.'www.googleadservices.com',"'".$base.'www.googleadservices.com',
			'/proxy/www.googletagmanager.com','"'.$base.'www.googletagmanager.com',"'".$base.'www.googletagmanager.com',
			'/proxy/connect.facebook.net','"'.$base.'connect.facebook.net',"'".$base.'connect.facebook.net',
			//'/proxy/www.facebook.com','"'.$base.'www.facebook.com',"'".$base.'www.facebook.com',
			//'/proxy/px.ads.linkedin.com','"'.$base.'px.ads.linkedin.com',"'".$base.'px.ads.linkedin.com',
			//'/proxy/tb.de17a.com','"'.$base.'tb.de17a.com',"'".$base.'tb.de17a.com',
		],$content);
		return $content;
	}

	// ---

	public function response() {
		return _echoFile($this->file,$this->expire);
	}

}
