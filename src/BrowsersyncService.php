<?php

namespace ABetter\Toolkit;

use ABetter\Toolkit\Service as BaseService;

class BrowsersyncService extends BaseService {

	public function handle() {
		$opt = (isset($this->args[0][0])) ? $this->args[0][0] : [];
		$file = $opt['file'] ?? NULL;
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		if ($ext == 'scss' || $ext == 'css') {
			BladeDirectives::style($file,[],TRUE);
			$this->data(['message' => "Updated style {$file}"]);
		} else if ($ext == 'js') {
			BladeDirectives::script($file,[],TRUE);
			$this->data(['message' => "Updated script {$file}"]);
		} else {
			$this->data(['error' => "BrowsersyncService not available for type {$ext}"]);
		}
	}

	public function response() {
		return $this->echo($this->data);
	}

}
