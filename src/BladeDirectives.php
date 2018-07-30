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

	public static function style($name,$vars,$link=FALSE) {
		if (in_array($name,self::$styles)) return "<!--style:{$name}-->";
		$link = (env('APP_ENV') == 'sandbox') ? TRUE : $link;
		$source = self::getSource($name,$vars);
		$scss = new Compiler();
		$scss->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
		$css = $scss->compile($source);
		if ($link) {
			$path = '/_dev/components/'.pathinfo($name,PATHINFO_FILENAME).'.css';
			$file = public_path().$path;
			if (!is_dir(dirname($file))) mkdir(dirname($file),0777,TRUE);
			file_put_contents($file,$css);
			$style = "<link href=\"{$path}\" rel=\"stylesheet\" type=\"text/css\">";
		} else {
			$style = "<style>{$css}</style>";
		}
		self::$styles[] = $name;
		return $style;
	}

	// Script

	public static function script($name,$vars,$link=FALSE) {
		if (in_array($name,self::$scripts)) return "<!--script:{$name}-->";
		$link = (env('APP_ENV') == 'sandbox') ? TRUE : $link;
		$source = self::getSource($name,$vars);
		$js = JSMin::minify($source);
		if ($link) {
			$path = '/_dev/components/'.$name;
			$file = public_path().$path;
			if (!is_dir(dirname($file))) mkdir(dirname($file),0777,TRUE);
			file_put_contents($file,$js);
			$script = "<script src=\"{$path}\" type=\"text/javascript\"></script>";
		} else {
			$script = "<script>{$js}</script>";
		}
		self::$scripts[] = $name;
		return $script;
	}

	// Helpers

	public static function parseExpression($parse) {
		$id = trim(strtok($parse,','));
		$vars = trim(str_replace($id,'',$parse),',');
		$end = trim(preg_match('/, ?(end|true|1)$/i',$parse));
		if ($end) $vars = trim(substr($vars,0,strrpos($vars,',')));
		$exp = array();
		$exp[0] = trim($id,'\'');
		$exp[1] = ($vars) ? $vars : '[]';
		$exp[2] = ($end) ? TRUE : FALSE;
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
