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
	public $argx = [];
	public $query = [];
	public $data = [];
	public $file = NULL;
	public $expire = '1 hour';
	public $lockexpire = '2 minutes';
	public $md5expire = '2 hours';
	public $storage = 'service';
	public $response = NULL;
	public $handled = NULL;
	public $debug = NULL;
	public $log = [];
	public $logfile = FALSE;

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
		$this->query = $_GET ?? [];
		$this->route = Route::getFacadeRoot()->current();
		$this->origin = $_GET['origin'] ?? 'direct';
		$this->service = _slugify(strtok($this->route->uri(),'{'));
		$this->method = trim($this->args[0]['method'] ?? $this->route->parameters['path'] ?? '', '/');
		$this->type = trim($this->args[0]['type'] ?? $this->route->parameters['type'] ?? '', '.');
		$this->slug = _slugify("{$this->service}-{$this->method}");
		$this->storage = storage_path($this->storage);
		if (!is_dir($this->storage)) \File::makeDirectory($this->storage,0777,TRUE);
		if ($this->service == 'service') {
			$this->argx = explode('/',$this->method);
			$this->service = $this->argx[0] ?? '';
			$this->method = $this->argx[1] ?? '';
		}
		$this->data = [
			'requested' => date(\DateTime::ISO8601),
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
		@file_put_contents($file,date(\DateTime::ISO8601,$expire));
	}

	public function unlock($name=NULL) {
		$file = $this->storage.'/'.($name ?? $this->slug).'.lock';
		@unlink($file);
	}

	// ---

	public function md5changed($string) {
		if (isset($this->data['changed'])) return $this->data['changed'];
		$md5 = md5($string);
		$md5_last = $this->md5stored();
		$md5_changed = ($md5 === $md5_last) ? FALSE : TRUE;
		$this->data['changed'] = $md5_changed;
		if (!$md5_changed) return FALSE;
		$this->md5change($md5);
		return TRUE;
	}

	public function md5stored($name=NULL) {
		$file = $this->storage.'/'.($name ?? $this->slug).'.md5';
		if (!$md5 = (is_file($file)) ? file_get_contents($file) : NULL) return NULL;
		list($time,$value) = explode('=',$md5);
		if (strtotime($time) > time()) return $value;
		return NULL;
	}

	public function md5change($value="",$name=NULL) {
		$file = $this->storage.'/'.($name ?? $this->slug).'.md5';
		$expire = (is_string($this->md5expire)) ? strtotime('+'.$this->md5expire) : time()+(int)$this->md5expire;
		@file_put_contents($file,date(\DateTime::ISO8601,$expire)."=".$value);
	}

	public function md5unlock($name=NULL) {
		$file = $this->storage.'/'.($name ?? $this->slug).'.md5';
		@unlink($file);
	}

	// ---

	public function invalidate() {
		$this->aws['id'] = ($e = getenv('AWS_ID')) ? $e : getenv('AWS_ACCESS_KEY_ID');
		$this->aws['key'] = ($e = getenv('AWS_KEY')) ? $e : getenv('AWS_SECRET_ACCESS_KEY');
		$this->aws['distribution'] = ($e = getenv('AWS_DISTRIBUTION')) ? explode(',',$e) : [];
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
