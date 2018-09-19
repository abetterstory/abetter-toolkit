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
			case 'browsersync' : return $this->handleBrowsersync($this->args[0],$this->args[1]);
		}
		return $this->echo(['error' => "Service {$this->service} not found"]);
    }

	// ---

	public function handleImage($style,$file,$opt=NULL) {
		$file = '/'.trim($file,'/');
		if (!is_file(public_path().$file) && preg_match('/^\/uploads\//',$file)) {
			$file = str_replace('/uploads/','/wp/wp-content/uploads/',$file);
		}
		$opt = array_replace([
			'style' => $style,
			'source' => public_path().$file,
			'target' => preg_replace('/\.([^\.]+)$/',".{$style}.$1",storage_path('cache').$file),
			'is_source' => NULL,
			'is_target' => NULL,
		],(array)$opt);
		if (is_file($opt['target'])) return _echoFile($opt['target'],'1 month');
		if (!is_file($opt['source'])) return abort(404);
		_imageMagick($opt['source'],$opt['target'],$opt['style']);
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
