<?php

namespace ABetter\Toolkit;

class Component {

	// --- Private

	protected $vars;
	protected $scope;
	protected $slot;

	// --- Public

	// --- Constructor

	public function __construct(array $defined_vars = []) {
		global $__vars;
		$this->vars = (object) $__vars;
		$this->scope = (object) $defined_vars;
		$this->slot = trim($this->scope->slot ?? "");
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
