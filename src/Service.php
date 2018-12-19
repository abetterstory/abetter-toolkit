<?php

namespace ABetter\Toolkit;

use Illuminate\Support\Facades\Route;

class Service {

	public $route = "";
	public $service = "";
	public $method = "";
	public $origin = "";
	public $slug = "";
	public $type = "";
	public $args = [];
	public $data = [];
	public $file = NULL;
	public $expire = '1 hour';
	public $lockexpire = '2 minutes';
	public $storage = 'service';
	public $response = NULL;
	public $handled = NULL;
	public $debug = NULL;
	public $log = [];
	public $logfile = TRUE;

	public $aws = [];
	public $invalidated = [];

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
		$this->origin = $_GET['origin'] ?? 'direct';
		$this->service = _slugify(strtok($this->route->uri(),'{'));
		$this->method = trim($this->args[0]['method'] ?? $this->route->parameters['path'] ?? '', '/');
		$this->type = trim($this->args[0]['type'] ?? $this->route->parameters['type'] ?? '', '.');
		$this->slug = _slugify("{$this->service}-{$this->method}");
		$this->storage = storage_path($this->storage);
		if (!is_dir($this->storage)) \File::makeDirectory($this->storage,0777,TRUE);
		$this->data = [
			'requested' => date('Y-m-d H:i:s'),
			'origin' => $this->origin,
			'service' => $this->service,
			'method' => $this->method,
			'type' => $this->type
		];
		if ($this->locked()) {
			$this->data['locked'] = TRUE;
		}
		if (isset($_GET['debug'])) {
			$this->debug = TRUE;
			$this->data['debug'] = $this->debug;
		}
		$this->handle();
		$this->output();
		$this->logfile();
	}

	// ---

	public function handle() {

    }

	// ---

	public function output() {

	}

	// ---

	public function debug() {

	}

	// ---

	public function logfile() {
		if (!$this->logfile) return;
		$file = $this->storage.'/'.($name ?? $this->slug).'.log';
		$log = json_encode($this->data);
		@file_put_contents($file, $log.PHP_EOL, FILE_APPEND | LOCK_EX);
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
		$file = $this->storage.'/'.($name ?? $this->slug).'.lock';
		if (!$lock = (is_file($file)) ? file_get_contents($file) : NULL) return FALSE;
		if (strtotime($lock) > time()) return TRUE;
		$this->unlock($name);
		return FALSE;
	}

	public function lock($name=NULL) {
		$file = $this->storage.'/'.($name ?? $this->slug).'.lock';
		$expire = (is_string($this->lockexpire)) ? strtotime('+'.$this->lockexpire) : time()+(int)$this->lockexpire;
		@file_put_contents($file,date('Y-m-d H:i:s',$expire));
	}

	public function unlock($name=NULL) {
		$file = $this->storage.'/'.($name ?? $this->slug).'.lock';
		@unlink($file);
	}

	// ---

	public function invalidate() {
		$this->aws['id'] = getenv('AWS_ID');
		$this->aws['key'] = getenv('AWS_KEY');
		$this->aws['distribution'] = ($d = getenv('AWS_DISTRIBUTION')) ? explode(',',$d) : [];
		$this->aws['paths'] = ['/*'];
		$this->aws['results'] = [];
		if (!$this->aws['id'] || !$this->aws['key'] || !$this->aws['distribution']) return;
		$client = new \Aws\CloudFront\CloudFrontClient(array(
		    'version' => 'latest',
			'region' => 'us-east-1',
		    'credentials' => array(
		        'key' => $this->aws['id'],
		        'secret' => $this->aws['key']
		    )
		));
		foreach ($this->aws['distribution'] as $distribution) {
			$result = $client->createInvalidation(array(
			    'DistributionId' => $distribution,
			    'InvalidationBatch' => array(
			        'CallerReference' => $distribution.date('U'),
			        'Paths' => array(
			            'Items' => $this->aws['paths'],
			            'Quantity' => count($this->aws['paths'])
			        )
			    )
			));
			$this->invalidated($distribution,$result['Invalidation']['Id']);
		}
	}

	public function invalidated($distribution,$value=TRUE) {
		$this->invalidated[$distribution] = $value;
	}

}
