<?php

namespace ABetter\Toolkit;

use Leafo\ScssPhp\Compiler;
use JSMin\JSMin;

class BladeDirectives {

	protected static $styles = [];
	protected static $scripts = [];

	protected static $style_include_path = '';
	protected static $script_include_path = '';

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

	public static function style($name,$vars=[],$link=FALSE) {
		if (in_array($name,self::$styles)) return "<!--style:{$name}-->";
		$link = (env('APP_ENV') == 'sandbox') ? TRUE : $link;
		$source = self::getSource($name,$vars);
		$source = self::parseStyleIncludes($source,$vars);
		$paths = [dirname(self::getSourceFile($name,$vars)),resource_path('styles'),resource_path('css')];
		$scss = new Compiler();
		$scss->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
		$scss->setImportPaths($paths);
		$css = $scss->compile($source);
		if ($link) {
			$path = '/styles/components/'.pathinfo($name,PATHINFO_FILENAME).'.css';
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
		$source = self::parseScriptIncludes($source,$vars);
		$js = JSMin::minify($source);
		if ($link) {
			$path = '/scripts/components/'.$name;
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

	protected static function getSourceFile($name,$vars=[]) {
		if (!$vars) return base_path().'/'.$name;
		$view = (isset($vars['view']->path)) ? $vars['view']->path : '';
		$path = ((!preg_match('/^\//',$name) && $view) ? dirname($view) : resource_path('views')) . '/';
		return $path.trim($name,'/');
	}

	protected static function getSource($name,$vars=[]) {
		$file = self::getSourceFile($name,$vars);
		return (is_file($file)) ? trim(file_get_contents($file)) : '';
	}

	protected static function getClassFile($name,$vars=[]) {
		$view = (isset($vars['view']->path)) ? $vars['view']->path : '';
		$path = ((!preg_match('/^\//',$name) && $view) ? dirname($view) : resource_path('views')) . '/';
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

	// ---

	protected static function parseStyleIncludes($source,$vars) {
		self::$style_include_path = (isset($vars['view']->path)) ? $vars['view']->path : '';
		$source = preg_replace_callback('/\@include([^\;]+);/',function($matches){
			$file = trim($matches[1],'\'\" ');
			if (preg_match('/^\~/',$file)) {
				$file = base_path().'/node_modules/'.trim($file,'~');
			} elseif (preg_match('/^\//',$file)) {
				$file = base_path().$file;
			} else {
				$file = ((!empty(self::$style_include_path)) ? dirname(self::$style_include_path) : base_path()).'/'.$file;
			}
			return (is_file($file)) ? file_get_contents($file) : "";
		},$source);
		return $source;
	}

	protected static function parseScriptIncludes($source,$vars) {
		self::$script_include_path = (isset($vars['view']->path)) ? $vars['view']->path : '';
		$source = preg_replace_callback('/\@include\(([^\)]+)\);?/',function($matches){
			$file = trim($matches[1],'\'\"');
			if (preg_match('/^\~/',$file)) {
				$file = base_path().'/node_modules/'.trim($file,'~');
			} elseif (preg_match('/^\//',$file)) {
				$file = base_path().$file;
			} else {
				$file = ((!empty(self::$script_include_path)) ? dirname(self::$script_include_path) : base_path()).'/'.$file;
			}
			return (is_file($file)) ? file_get_contents($file) : "";
        },$source);
		return $source;
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
