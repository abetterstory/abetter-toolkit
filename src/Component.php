<?php

namespace ABetter\Toolkit;

class Component {

	// --- Private

	protected $vars;
	protected $scope;

	// --- Public

	// --- Constructor

	public function __construct(array $defined_vars = []) {
		global $__vars;
		$this->vars = (array) $__vars;
		$this->scope = (object) $defined_vars;
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
