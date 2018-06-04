<?php

namespace ABetter\Toolkit;

use Leafo\ScssPhp\Compiler;
use JSMin\JSMin;

class BladeDirectives {

	protected static $styles = [];
	protected static $scripts = [];

	// inject

	public static function inject($name,$vars) {
		$file = self::getClassFile($name,$vars);
		$source = self::getClass($name,$vars);
		$namespace = (preg_match('/(?<=(namespace))\s+([^;]+);/',$source,$matches)) ? trim($matches[2]) : '';
		$class = (preg_match('/(?<=(class))(\s\w*)/',$source,$matches)) ? trim($matches[0]) : '';
		$service = (($namespace) ? '\\'.$namespace.'\\' : '') . $class;
		if (is_file($file)) require_once($file);
		return new $service($vars);
	}

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

	public static function parseExpression($exp) {
		$exp = explode(',',$exp);
		$exp[0] = trim($exp[0],'\'');
		$exp[1] = (isset($exp[1])) ? $exp[1] : '[]';
		$exp[2] = (isset($exp[2])) ? TRUE : FALSE;
		if (in_array(strtolower($exp[1]),['end','true','1'])) {
			$exp[1] = '[]'; $exp[2] = TRUE;
		}
		return $exp;
	}

	// ---

	protected static function getSourceFile($name,$vars) {
		$view = (isset($vars['view']->path)) ? $vars['view']->path : '';
		$path = ((!preg_match('/\//',$name) && $view) ? dirname($view) : resource_path('views')) . '/';
		return $path.trim($name,'/');
	}

	protected static function getSource($name,$vars) {
		$file = self::getSourceFile($name,$vars);
		return (is_file($file)) ? trim(file_get_contents($file)) : '';
	}

	protected static function getClassFile($name,$vars) {
		$view = (isset($vars['view']->path)) ? $vars['view']->path : '';
		$path = ((!preg_match('/\//',$name) && $view) ? dirname($view) : resource_path('views')) . '/';
		$file = $path.trim($name,'/');
		if (is_file($file)) return $file;
		if (is_file($file.'.php')) return $file.'.php';
		if (is_file($file.'.class.php')) return $file.'.class.php';
		return $file;
	}

	protected static function getClass($name,$vars) {
		$file = self::getClassFile($name,$vars);
		return (is_file($file)) ? trim(file_get_contents($file)) : '';
	}

	// Special

	public static function vars($parent,$merge=NULL) {
		$vars = get_defined_vars();
		$parent = array_except($parent, array('__env', '__data', '__path', 'obLevel', 'app', 'errors', 'view', 'template'));
		if (!empty($parent)) foreach ($parent AS $key => $val) $vars[$key] = $val;
		if ($merge) $vars = array_merge($vars,$merge);
		return $vars;
	}

}
