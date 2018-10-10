<?php

namespace ABetter\Toolkit;

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController {

	public $json = [];
	public $args = [];
	public $service = '';
	public $method = '';

	// ---

	public function __construct($args=NULL) {
	}

	// ---

	public function handle() {
		$this->args = func_get_args();
		$this->service = strtok(Route::getFacadeRoot()->current()->uri(),'/');
		$this->method = $this->args[0];
		switch ($this->service) {
			case 'image' : return $this->handleImage($this->args[0],$this->args[1]);
			case 'proxy' : return $this->handleProxy($this->args[0]);
			case 'browsersync' : return $this->handleBrowsersync($this->args[0],$this->args[1]);
		}
		return $this->echo(['error' => "Service {$this->service} not found"]);
    }

	// ---

	public function handleImage($style,$file,$opt=NULL) {
		$file = '/'.trim($file,'/');
		$opt = array_replace([
			'style' => $style,
			'source' => _imageFileSearch($file),
			'target' => preg_replace('/\.([^\.]+)$/',".{$style}.$1",storage_path('cache').$file),
		],(array)$opt);
		if (is_file($opt['target'])) return _echoFile($opt['target'],'1 month');
		if (!is_file($opt['source'])) return abort(404);
		_imageMagick($opt['source'],$opt['target'],$opt['style']);
		return _echoFile($opt['target'],'1 month');
	}

	// ---

	public function handleProxy($file,$opt=NULL) {
		$file = trim($file,'/');
		$storage = storage_path('cache').'/proxy';
		$opt = array_replace([
			'source' => 'https://'.$file,
			'target' => $storage.'/'.preg_replace('/\//','_',$file),
			'content' => NULL,
		],(array)$opt);
		if (is_file($opt['target'])) return _echoFile($opt['target'],'1 month');
		if (!$opt['content'] = @file_get_contents($opt['source'])) return abort(404);
		if (!is_dir($storage)) \File::makeDirectory($storage,0777,TRUE);
		// ---
		$opt['content'] = str_replace([
			'https://www.google-analytics.com/analytics.js'
		],[
			'/proxy/www.google-analytics.com/analytics.js'
		],$opt['content']);
		// ---
		@file_put_contents($opt['target'],$opt['content']);
		return _echoFile($opt['target'],'1 month');
	}

	// ---

	public function handleBrowsersync($event, $file) {
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		if ($ext == 'scss' || $ext == 'css') {
			BladeDirectives::style($file,[],TRUE);
			return $this->echo(['message' => "Updated style {$file}"]);
		} else if ($ext == 'js') {
			BladeDirectives::script($file,[],TRUE);
			return $this->echo(['message' => "Updated script {$file}"]);
		}
		return $this->echo(['error' => "Service {$this->service} for type {$ext} not available"]);
	}

	// ---

	public function echo($send = []) {
		$this->json['service'] = $this->service;
		$this->json['method'] = $this->method;
		if (isset($send['error'])) {
			$this->json['error'] = $send['error'];
		} else {
			$this->json['message'] = $send['message'];
		}
		return response()->json($this->json);
	}

}
