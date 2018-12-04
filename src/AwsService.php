<?php

namespace ABetter\Toolkit;

use ABetter\Toolkit\Service as BaseService;

class AwsService extends BaseService {

	public function handle() {
		switch ($this->method)  {
			case 'invalidate' : case 'clearcache' : return $this->invalidate();
		}
	}

	// ---

	public function output() {
		if ($this->invalidated) {
			$this->data['invalidated'] = $this->invalidated;
		}
	}

}
