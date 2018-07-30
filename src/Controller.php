<?php

namespace ABetter\Toolkit;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController {

	public $args = array();

	// ---

	public function __construct($args=NULL) {
	}

	// ---

	public function handle() {
		$this->args = func_get_args();
		clock($this->args );
		return "No template found in views/wordpress/";
    }

}
