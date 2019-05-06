<?php

namespace ABetter\Toolkit;

use ABetter\Toolkit\Service as BaseService;

class ProxyService extends BaseService {

	public function handle() {

		$opt = (isset($this->args[0][0])) ? $this->args[0][0] : [];

		$opt['base'] = ($base = env('APP_BASE')) ? rtrim($base,'/') : '';
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
		$content = str_replace([
			'https://www.google-analytics.com/analytics.js'
		],[
			$opt['base'].'/proxy/www.google-analytics.com/analytics.js'
		],$content);
		return $content;
	}

	// ---

	public function response() {
		return _echoFile($this->file,$this->expire);
	}

}
