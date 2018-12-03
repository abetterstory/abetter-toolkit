<?php

namespace ABetter\Toolkit;

use Illuminate\Support\Facades\Route;

class Service {

	public $route = "";
	public $service = "";
	public $method = "";
	public $type = "";
	public $args = [];
	public $data = [];
	public $file = NULL;
	public $response = NULL;
	public $expire = '1 month';
	public $handled = NULL;
	public $debug = NULL;

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
		$this->service = strtok($this->route->uri(),'{');
		$this->method = trim($this->route->parameters['path'] ?? '', '/');
		$this->type = trim($this->route->parameters['type'] ?? '', '.');
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

	public function response() {
		return _echoJson($this->data);
    }

	public function echo() {
		echo $this->response();
    }

}
