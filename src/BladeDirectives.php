<?php

namespace ABetter\Toolkit;

use Leafo\ScssPhp\Compiler;
use JSMin\JSMin;

class BladeDirectives {

	protected static $styles = [];
	protected static $scripts = [];

	// Style

	public static function style($name,$vars) {
		if (in_array($name,self::$styles)) return "<!--style:{$name}-->";
		$source = self::getSource($name,$vars);
		$scss = new Compiler();
		$scss->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
		$css = $scss->compile($source);
		$style = "<style>{$css}</style>";
		self::$styles[] = $name;
		return $style;
	}

	// Script

	public static function script($name,$vars) {
		if (in_array($name,self::$scripts)) return "<!--script:{$name}-->";
		$source = self::getSource($name,$vars);
		$js = JSMin::minify($source);
		$script = "<script>{$js}</script>";
		self::$scripts[] = $name;
		return $script;
	}

	// Helpers

	public static function parseExpression($expressions) {
		$expressions = explode(',',$expressions);
		$expressions[0] = trim($expressions[0],'\'');
		$expressions[1] = (isset($expressions[1])) ? $expressions[1] : '[]';
		return $expressions;
	}

	// ---

	protected static function getSource($name,$vars) {
		$file = self::getSourceFile($name,$vars);
		return (is_file($file)) ? trim(file_get_contents($file)) : '';
	}

	protected static function getSourceFile($name,$vars) {
		$view = (isset($vars['view']->path)) ? $vars['view']->path : '';
		$path = ((!preg_match('/\//',$name) && $view) ? dirname($view) : resource_path('views')) . '/';
		return $path.trim($name,'/');
	}

	// Special

	public static function vars($parent) {
		$vars = get_defined_vars();
		$parent = array_except($parent, array('__env', '__data', '__path', 'obLevel', 'app', 'errors', 'view', 'template'));
		if (!empty($parent)) foreach ($parent AS $key => $val) $vars[$key] = $val;
		return $vars;
	}

}
