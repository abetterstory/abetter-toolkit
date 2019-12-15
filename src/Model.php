<?php

namespace ABetter\Toolkit;

use Illuminate\Database\Eloquent\Model;

class Model extends Model {

	// --- Public

	// --- Boot

	public static function boot() {
		parent::boot();
		$this->init();
		$this->build();
	}

	// --- Init

	public function init() {
		//
	}

	// --- Build

	public function build() {
		//
	}

}
