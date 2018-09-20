<?php

if (!function_exists('_image')) {

	function _image($file,$style='x',$ext=NULL) {
		if (($wp = env('WP_UPLOADS')) && preg_match('|'.$wp.'|',$file)) $file = str_replace($wp,'/uploads/',$file);
		$url = '/image/'.$style._relative($file);
		if ($ext === TRUE) return $url;
		$ext = ($ext) ? $ext : 'jpg';
		$url = dirname($url).'/'.pathinfo($url,PATHINFO_FILENAME).'.'.$ext;
		return $url;
	}

}

if (!function_exists('_imageMagick')) {

	function _imageMagick($source,$target,$style,$format=NULL) {
		if (!is_file($source)) return FALSE;
		File::makeDirectory(dirname($target),0777,TRUE,TRUE);
		$ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
		$style = _imageStyle($style);
		// ---
		$imagick = new Imagick($source);
		_imagickResize($imagick,$style);
		_imagickFilter($imagick,$style);
		if ($ext == 'png' || $ext == 'gif') {
			$imagick->setImageCompression(Imagick::COMPRESSION_UNDEFINED);
			$imagick->setImageCompressionQuality(0);
		} else if ($ext == 'jpg' || $ext == 'jpeg') {
			$imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
			$imagick->setImageCompressionQuality(70);
		}
		$imagick->stripImage();
		$imagick->writeImage($target);
		$imagick->clear();
		// ---
		return (is_file($target)) ? TRUE : FALSE;
	}

}

if (!function_exists('_imagickResize')) {

	function _imagickResize($imagick,$style) {
		$source_w = $imagick->getImageWidth();
		$source_h = $imagick->getImageHeight();
		$target_w = floor($style['w']);
		$target_h = floor($style['h']);
		// ---
		if (empty($target_w) && empty($target_h)) {
			$target_w = $source_w;
			$target_h = $source_h;
		}
		// ---
		if ($target_w > $target_h) {
			$resize_w = $target_w;
			$resize_h = floor($source_h * ($target_w / $source_w));
		} else {
			$resize_w = floor($source_w * ($target_h / $source_h));
			$resize_h = $target_h;
		}
		$imagick->resizeImage($resize_w, $resize_h, Imagick::FILTER_LANCZOS, 0.9);
		// ---
		if ($target_w == 0 || $target_h == 0) return $imagick;
		// ---
		$crop_w = $target_w;
		$crop_h = $target_h;
		$crop_x = 0;
		$crop_y = 0;
		$crop_align = $style['align'];
		// ---
		if ($resize_w > $crop_w) {
			$crop_x = floor(($resize_w - $crop_w) / 2);
			if (preg_match('/left/',$crop_align)) $crop_x = 0;
			if (preg_match('/right/',$crop_align)) $crop_x = floor($resize_w - $crop_w);
			$imagick->cropImage($crop_w, $target_h, $crop_x, 0);
		}
		if ($resize_h > $crop_h) {
			$crop_y = floor(($resize_h - $crop_h) / 2);
			if (preg_match('/top/',$crop_align)) $crop_y = 0;
			if (preg_match('/bottom/',$crop_align)) $crop_y = floor($resize_h - $crop_h);
			$imagick->cropImage($target_w, $crop_h, 0, $crop_y);
		}
		// ---
		return $imagick;
	}

}

if (!function_exists('_imagickFilter')) {

	function _imagickFilter($imagick,$style) {
		if ($style['filter'] == 'grayscale') {
			$imagick->transformimagecolorspace(Imagick::COLORSPACE_GRAY);
		}
		if ($style['filter'] == 'blur') {
			$val = ($style['value']) ? (int) $style['value'] : 10;
			$imagick->blurImage($val,$val);
		}
		if ($style['filter'] == 'lighter') {
			$val = ($style['value']) ? (int) $style['value'] : 25;
			$imagick->brightnessContrastImage($val, 0);
		}
		if ($style['filter'] == 'darker') {
			$val = ($style['value']) ? (int) $style['value'] : 25;
			$imagick->brightnessContrastImage(0 - $val, 0);
		}
		return $imagick;
	}

}

if (!function_exists('_imageStyle')) {

	function _imageStyle($string,$style=array()) {
		$style['args'] = explode('-',$string);
		$style['dimension'] = (isset($style['args'][0])) ? $style['args'][0] : NULL;
		$style['align'] = (isset($style['args'][1])) ? $style['args'][1] : NULL;
		$style['filter'] = (isset($style['args'][2])) ? $style['args'][2] : NULL;
		$style['value'] = (isset($style['args'][3])) ? $style['args'][3] : NULL;
		$style['background'] = NULL; //'#000';
		// ---
		$style['type'] = '';
		$style['w'] = 0;
		$style['h'] = 0;
		if (preg_match('/x/',$style['dimension'])) {
			$style['type'] = 'x';
			list($style['w'],$style['h']) = explode('x',$style['dimension']);
			$style['w'] = (int) $style['w'];
			$style['h'] = (int) $style['h'];
		} elseif (preg_match('/^w/',$style['dimension'])) {
			$style['type'] = 'w';
			$style['w'] = (int) ltrim($style['dimension'],'w');
		} elseif (preg_match('/^h/',$style['dimension'])) {
			$style['type'] = 'h';
			$style['h'] = (int) ltrim($style['dimension'],'h');
		} elseif (is_numeric($style['dimension'])) {
			$style['type'] = 's';
			$style['w'] = (int) $style['dimension'];
			$style['h'] = (int) $style['dimension'];
		}
		// ---
		$aligns = array(
			'c' => 'center', 'm' => 'center', 'middle' => 'center', 'center' => 'center',
			't' => 'top', 'top' => 'top',
			'b' => 'bottom', 'bottom' => 'bottom',
			'l' => 'left', 'left' => 'left',
			'r' => 'right', 'right' => 'right',
			'lt' => 'lefttop', 'tl' => 'lefttop', 'topleft' => 'lefttop', 'lefttop' => 'lefttop',
			'lb' => 'leftbottom', 'bl' => 'leftbottom', 'bottomleft' => 'leftbottom', 'leftbottom' => 'leftbottom',
			'rt' => 'righttop', 'tr' => 'righttop', 'topright' => 'righttop', 'righttop' => 'righttop',
			'rb' => 'rightbottom', 'br' => 'rightbottom', 'bottomright' => 'rightbottom', 'rightbottom' => 'rightbottom'
		);
		$style['align'] = (isset($aligns[$style['align']])) ? $aligns[$style['align']] : 'center';
		if ($style['align'] == 'box' && $style['filter']) $style['background'] = $style['filter'];
		// ---
		$filters = array(
			'g' => 'grayscale', 'gray' => 'grayscale', 'grayscale' => 'grayscale',
			'b' => 'blur', 'blur' => 'blur',
			'l' => 'lighter', 'light' => 'lighter', 'lighter' => 'lighter',
			'd' => 'darker', 'dark' => 'darker', 'darker' => 'darker'
		);
		$style['filter'] = (isset($filters[$style['filter']])) ? $filters[$style['filter']] : NULL;
		// ---
		return $style;
	}

}

// ---

function _echoFile($file,$expire='1 month') {
	if (!is_file($file)) return _echoExit();
	_headersFile($file,$expire);
	echo file_get_contents($file);
	exit(0);
}

function _headersFile($file,$expire='1 month',$cors=FALSE) {
	if (!is_file($file)) return FALSE;
	$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
	$type = _contentType($ext);
	$created = filemtime($file);
	$expire = _expireTime($expire);
	$etag = md5_file($file);
	_headers($type,$created,$expire,$etag,$cors);
}

function _contentType($ext) {
	$types = array(
		'txt' => 'text/plain',
		'html' => 'text/html',
		'xml' => 'text/xml',
		'json' => 'application/json',
		'pdf' => 'application/pdf',
		'js' => 'application/javascript',
		'css' => 'text/css',
		'svg' => 'image/svg+xml',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'png' => 'image/png',
		'gif' => 'image/gif',
		'ico' => 'image/x-icon',
		'ttf' => 'application/x-font-ttf',
		'eot' => 'application/vnd.ms-fontobject',
		'woff' => 'application/font-woff',
		'woff2' => 'application/font-woff2',
		'mp4' => 'video/mp4',
		'm4v' => 'video/mp4',
		'ogg' => 'video/ogg',
		'ogv' => 'video/ogg',
		'webm' => 'video/webm',
		'xls' => 'application/vnd.ms-excel',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
	);
	return (isset($types[$ext])) ? $types[$ext] : 'text/html';
}

function _expireTime($time) {
	if (is_numeric($time)) return $time;
	return strtotime($time, 0);
}

function _headers($type,$created,$expire,$etag=NULL,$cors=FALSE) {
	@header('Content-Type: '.$type.'; charset=utf-8');
	@header('Cache-Control: public, max-age='.$expire);
	@header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $expire));
	if ($created) @header('Last-Modified: '.gmdate('D, d M Y H:i:s \G\M\T', $created));
	if ($etag) @header('Etag: '.$etag);
	// SVG: @header('Vary: Accept-Encoding'); // Add AddOutputFilterByType DEFLATE image/svg+xml in htaccess
	@header_remove('X-Powered-By');
	if ($cors) {
		@header('Access-Control-Allow-Origin: *');
		@header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
		@header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, X-Requested-With');
	}
	return TRUE;
}

function _imageFileSearch($file) {
	$return = NULL;
	$ext = pathinfo($file,PATHINFO_EXTENSION);
	$name = pathinfo($file,PATHINFO_FILENAME);
	if ($conv = pathinfo($name,PATHINFO_EXTENSION)) {
		$ext = $conv;
		$name = pathinfo($name,PATHINFO_FILENAME);
	}
	$dir = dirname(public_path().$file);
	if (!is_dir($dir)) {
		// Try WP_UPLOADS
		if (preg_match('/^\/uploads\//',$file)) {
			$dir = dirname(public_path().str_replace('/uploads/',env('WP_UPLOADS'),$file));
		}
		if (!is_dir($dir)) return FALSE;
	}
	foreach (scandir($dir) AS $f) {
		if ($return || in_array($f,['.','..'])) continue;
		if ($f == "{$name}.{$ext}") $return = $f;
		if (pathinfo($f,PATHINFO_FILENAME) == $name) $return = $f;
	}
	return ($return) ? realpath($dir.DIRECTORY_SEPARATOR.$return) : FALSE;
}
