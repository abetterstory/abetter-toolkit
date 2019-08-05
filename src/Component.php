<?php

namespace ABetter\Toolkit;

class Component {

	// --- Private

	protected $vars;
	protected $scope;
	protected $slot;
	protected $namespace;

	// --- Public

	// --- Constructor

	public function __construct(array $defined_vars = []) {
		global $__vars;
		$this->vars = (object) $__vars;
		$this->scope = (object) $defined_vars;
		$this->slot = trim($this->scope->slot ?? "");
		$this->namespace = get_called_class();
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

	// --- Get

	public function get($function,$return=NULL,$options=[]) {
		switch (gettype($return)) {
			case 'boolean' : return $this->getBoolean($function,$return,$options);
			case 'integer' : return $this->getInteger($function,$return,$options);
			case 'double' : return $this->getFloat($function,$return,$options);
			case 'string' : return $this->getString($function,$return,$options);
			case 'array' : return $this->getArray($function,$return,$options);
			case 'object' : return $this->getObject($function,$return,$options);
		}
		return $this->getFunction($function,$return,$options);
	}

	public function getBoolean($function,$return=NULL,$options=[]) {
		return (boolean) $this->getFunction($function,$return,$options);
	}

	public function getInteger($function,$return=NULL,$options=[]) {
		return (integer) $this->getFunction($function,$return,$options);
	}

	public function getFloat($function,$return=NULL,$options=[]) {
		return (float) $this->getFunction($function,$return,$options);
	}

	public function getString($function,$return=NULL,$options=[]) {
		return (string) $this->getFunction($function,$return,$options);
	}

	public function getArray($function,$return=NULL,$options=[]) {
		return (array) $this->getFunction($function,$return,$options);
	}

	public function getObject($function,$return=NULL,$options=[]) {
		return (object) $this->getFunction($function,$return,$options);
	}

	public function getDictionary($function,$return=NULL,$options=[]) {
		$slug = $function;
		$default = $return;
		$lang = $options['lang'] ?? NULL;
		$fallback = $options['fallback'] ?? NULL;
		return (string) _dictionarySlug($slug,$lang,$default,$fallback);
	}

	// ---

	public function getFunction($function,$return=NULL,$options=[]) {
		$options = (empty($options) && is_array($return)) ? $return : $options;
		if (empty($function)) return $return;
		if (method_exists($this,$function)) return $this->{$function}($options);
		if (function_exists($this->namespace.'_'.$function)) return $this->namespace.'_'.$function($options);
		if (env('APP_DEBUG')) echo "<!-- missing-data:{$this->namespace}_{$function} -->";
		return $return;
	}

}
