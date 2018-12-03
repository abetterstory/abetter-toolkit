<?php

namespace ABetter\Toolkit;

use Illuminate\Support\Facades\Route;

class Service {

	public $route = "";
	public $service = "";
	public $method = "";
	public $slug = "";
	public $type = "";
	public $args = [];
	public $data = [];
	public $file = NULL;
	public $expire = '1 hour';
	public $storage = 'service';
	public $response = NULL;
	public $handled = NULL;
	public $debug = NULL;
	public $log = [];

	// ---

	public function __construct() {
		$this->boot(func_get_args());
	}

	public function __toString() {
		return (string) $this->response();
	}

	public function boot() {
		$this->args = func_get_args();
		$this->route = Route::getFacadeRoot()->current();
		$this->service = _slugify(strtok($this->route->uri(),'{'));
		$this->method = trim($this->route->parameters['path'] ?? '', '/');
		$this->type = trim($this->route->parameters['type'] ?? '', '.');
		$this->slug = _slugify("{$this->service}-{$this->method}");
		$this->storage = storage_path($this->storage);
		if (!is_dir($this->storage)) \File::makeDirectory($this->storage,0777,TRUE);
		$this->data = [
			'service' => $this->service,
			'method' => $this->method,
			'type' => $this->type
		];
		if (isset($_GET['debug'])) {
			$this->debug = TRUE;
			$this->data['debug'] = $this->debug;
		}
		$this->handle();
	}

	// ---

	public function handle() {

    }

	// ---

	public function debug() {

	}

	// ---

	public function log($key,$value=NULL) {
		if (!$value) $this->log[] = $key; else $this->log[$key] = $value;
	}

	public function response() {
		if ($this->debug) {
			$this->debug();
			$this->data['log'] = $this->log;
		}
		return _echoJson($this->data,$this->expire);
    }

	public function echo() {
		echo $this->response();
    }

	// ---

	public function locked($name=NULL) {
		return (is_file($this->storage.'/'.($name ?? $this->slug).'.lock')) ? TRUE : FALSE;
	}

	public function lock($name=NULL) {
		@file_put_contents($this->storage.'/'.($name ?? $this->slug).'.lock',date('Y-m-d H:i:s'));
	}

	public function unlock($name=NULL) {
		@unlink($this->storage.'/'.($name ?? $this->slug).'.lock');
	}

}
