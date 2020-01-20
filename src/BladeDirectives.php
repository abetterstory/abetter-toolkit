<?php

namespace ABetter\Toolkit;

use Leafo\ScssPhp\Compiler;
use Patchwork\JSqueeze;
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
		$render = (isset($vars['render'])) ? (boolean) $vars['render'] : TRUE;
		if (isset($vars['link'])) $link = (boolean) $vars['link'];
		if (empty($css = $vars['style'] ?? "")) {
			$source = self::getSource($name,$vars);
			$source = self::parseStyleIncludes($source,$vars);
			$paths = [dirname(self::getSourceFile($name,$vars)),resource_path('styles'),resource_path('css')];
			$scss = new Compiler();
			$scss->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
			$scss->setImportPaths($paths);
			$css = $scss->compile($source);
		}
		if ($link) {
			$attr = "";
			$name = str_replace('~','',$name);
			$path = '/styles/components/'.pathinfo($name,PATHINFO_FILENAME).'.css';
			$file = public_path().$path;
			if (!is_dir(dirname($file))) mkdir(dirname($file),0777,TRUE);
			@file_put_contents($file,$css);
			@chmod($file,0755);
			$style = ($render) ? "<link href=\"{$path}\" rel=\"stylesheet\" type=\"text/css\" {$attr}>" : "";
		} else {
			$style = "<style>{$css}</style>";
		}
		self::$styles[] = $name;
		return $style;
	}

	// Script

	public static function script($name,$vars=[],$link=FALSE) {
		if (in_array($name,self::$scripts)) return "<!--script:{$name}-->";
		$link = (env('APP_ENV') == 'sandbox') ? TRUE : $link;
		$render = (isset($vars['render'])) ? (boolean) $vars['render'] : TRUE;
		if (isset($vars['link'])) $link = (boolean) $vars['link'];
		if (empty($js = $vars['script'] ?? "")) {
			$source = self::getSource($name,$vars);
			$source = self::parseScriptIncludes($source,$vars);
			$JSqueeze = new JSqueeze();
			$js = $JSqueeze->squeeze($source,TRUE,TRUE,FALSE);
			//$js = JSMin::minify($source);
		}
		if ($link) {
			$attr = "";
			if (!empty($vars['defer'])) $attr .= " defer";
			if (!empty($vars['async'])) $attr .= " async";
			$name = str_replace('~','',$name);
			$path = '/scripts/components/'.$name;
			$file = public_path().$path;
			if (!is_dir(dirname($file))) mkdir(dirname($file),0777,TRUE);
			@file_put_contents($file,$js);
			@chmod($file,0755);
			$script = ($render) ? "<script src=\"{$path}\" type=\"text/javascript\" {$attr}></script>" : "";
		} else {
			$script = "<script>{$js}</script>";
		}
		self::$scripts[] = $name;
		return $script;
	}

	// Embedd

	public static function svg($file,$vars=[]) {
		$svg = "<!--svg:{$file}-->";
		$file = resource_path().$file;
		if (is_file($file)) $svg = @file_get_contents($file);
		return $svg;
	}

	// Helpers

	public static function parseExpression($parse) {
		$id = trim(strtok($parse,','));
		$vars = trim(str_replace($id,'',$parse),',');
		$vars = preg_replace('/(\'|") ?(=&gt;|=) ?(\'|")/',"$1 => $3",$vars);
		$end = trim(preg_match('/, ?(end|true|1)$/i',$parse));
		if ($end) $vars = trim(substr($vars,0,strrpos($vars,',')));
		$exp = array();
		$exp[0] = trim($id,'\'');
		$exp[1] = ($vars) ? $vars : '[]';
		$exp[2] = ($end) ? TRUE : FALSE;
		return $exp;
	}

	public static function canViewLab($can=TRUE) {
		$env = (boolean) env('APP_LAB');
		$can = (in_array(env('APP_ENV'),['stage','production'])) ? FALSE : $can;
		$can = (function_exists('get_current_user_id') && !get_current_user_id()) ? $env : $can;
		$can = (function_exists('current_user_can') && !current_user_can('developer')) ? FALSE : $can;
		return $can;
	}

	// ---

	protected static function getSourceFile($name,$vars=[]) {
		if (!$vars) return base_path().'/'.$name;
		$view = (isset($vars['view']->path)) ? $vars['view']->path : '';
		$path = ((!preg_match('/^\//',$name) && $view) ? dirname($view) : resource_path('views')) . '/';
		$path = $path.trim($name,'/');
		if (!is_file($path) && preg_match('/\.(js|scss|css)/',$path) && preg_match('/~/',$path)) {
			$dir = (preg_match('/\.js/',$path)) ? 'scripts' : 'styles';
			if (!is_file($find = preg_replace('/^(.*)\/~\/?(.*)/',base_path().'/resources/'.$dir.'/$2',$path))) {
				$find = preg_replace('/^(.*)\/~\/?(.*)/',base_path('vendor').'/abetter/toolkit/'.$dir.'/$2',$path);
			}
			$path = (is_file($find)) ? $find : $path;
		} else if (!is_file($path) && preg_match('/(\/|\$)(styles|scripts)\//',$path)) {
			if (!is_file($find = preg_replace('/^(.*)(\/|\$)(styles|scripts)\/(.*)/',base_path().'/resources/$3/$4',$path))) {
				$find = preg_replace('/^(.*)(\/|\$)(styles|scripts)\/(.*)/',base_path('vendor').'/abetter/toolkit/$3/$4',$path);
			}
			$path = (is_file($find)) ? $find : $path;
		} else if (!is_file($path) && preg_match('/(\/|\$)mockup\//',$path)) {
			$vendor = preg_replace('/^(.*)(\/|\$)mockup\/(.*)/',base_path('vendor').'/abetter/toolkit/views/mockup/$3',$path);
			$path = (is_file($vendor)) ? $vendor : $path;
		}
		return $path;
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
		if (is_file($file.'Component.php')) return $file.'Component.php';
		if (is_file($file.'Model.php')) return $file.'Model.php';
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
		$parent = _array_except($parent, array('__env', '__data', '__path', 'obLevel', 'app', 'errors', 'view', 'template'));
		if (!empty($parent)) foreach ($parent AS $key => $val) $vars[$key] = $val;
		if ($merge) $vars = array_merge($vars,$merge);
		return $vars;
	}

}
