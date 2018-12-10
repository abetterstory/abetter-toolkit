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
			case 'image' : return new ImageService(['style' => $this->args[0], 'file' => $this->args[1]]);
			case 'cache' : return new ImageService(['style' => 'x', 'file' => '/cache/'.$this->args[0]]);
			case 'proxy' : return new ProxyService(['file' => $this->args[0]]);
			case 'browsersync' : return new BrowsersyncService(['event' => $this->args[0], 'file' => $this->args[1]]);
			case 'aws' : return new AwsService();
		}
		return $this->echo(['error' => "Service {$this->service} not found"]);
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
