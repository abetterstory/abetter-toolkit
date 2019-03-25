<?php

namespace ABetter\Toolkit;

use ABetter\Toolkit\Service as BaseService;

class ImageService extends BaseService {

	public function handle() {
		$opt = (isset($this->args[0][0])) ? $this->args[0][0] : [];
		$style = $opt['style'] ?? NULL;
		$file = $opt['file'] ?? NULL;
		$file = '/'.trim($file,'/');
		if (preg_match('/https?\:\/\//',$file)) {
			$querystring = ($q = http_build_query($this->query)) ? "?{$q}" : NULL;
			$file = _imageCache($file,NULL,$querystring);
		}
		$opt = array_replace([
			'style' => $style,
			'source' => _imageFileSearch($file),
			'target' => preg_replace('/\.([^\.]+)$/',".{$style}.$1",storage_path('cache').$file),
		],(array)$opt);
		if (!is_file($opt['target'])) {
			if (!is_file($opt['source'])) return abort(404);
			_imageMagick($opt['source'],$opt['target'],$opt['style']);
		}
		$this->file = $opt['target'];
		$this->expire = '1 year';
		$this->handled = TRUE;
	}

	public function response() {
		return _echoFile($this->file,$this->expire);
	}

}
