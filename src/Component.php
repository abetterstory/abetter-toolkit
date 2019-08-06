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
		if (empty($function)) return $return;
		$options = (empty($options) && is_array($return)) ? $return : $options;
		$model = '\\Components\\'.preg_replace('/component$/i','',$this->namespace).'Model';
		$data = NULL;
		if (method_exists($this,$function)) $data = $this->{$function}($options);
		if ($data === NULL && class_exists($model) && method_exists($model,$function) && ($Model = new $model())) $data = $Model->{$function}($options);
		if ($data === NULL && function_exists($this->namespace.'_'.$function)) $data = $this->namespace.'_'.$function($options);
		if ($data === NULL && env('APP_DEBUG')) echo "<!-- missing-data:{$this->namespace}_{$function} -->";
		return ($data === NULL) ? $return : $data;
	}

}
