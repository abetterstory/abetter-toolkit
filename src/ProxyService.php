<?php

namespace ABetter\Toolkit;

use ABetter\Toolkit\Service as BaseService;

class ProxyService extends BaseService {

	public function handle() {
		$opt = (isset($this->args[0][0])) ? $this->args[0][0] : [];
		$file = $opt['file'] ?? NULL;
		$file = trim($file,'/');
		$storage = storage_path('cache').'/proxy';
		$opt = array_replace([
			'source' => 'https://'.$file,
			'target' => $storage.'/'.preg_replace('/\/|\?|\=/','_',$file),
			'content' => NULL,
		],(array)$opt);
		if (!is_file($opt['target'])) {
			if (!$opt['content'] = @file_get_contents($opt['source'])) return abort(404);
			if (!is_dir($storage)) \File::makeDirectory($storage,0777,TRUE);
			$opt['content'] = str_replace([
				'https://www.google-analytics.com/analytics.js'
			],[
				'/proxy/www.google-analytics.com/analytics.js'
			],$opt['content']);
			@file_put_contents($opt['target'],$opt['content']);
			@chmod($opt['target'],0755);
		}
		$this->file = $opt['target'];
		$this->expire = '1 month';
		$this->handled = TRUE;
	}

	public function response() {
		return _echoFile($this->file,$this->expire);
	}

}
