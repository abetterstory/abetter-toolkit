<?php

function _echoFile($file,$expire='1 month') {
	if (!is_file($file)) return _echoExit();
	_headersFile($file,$expire);
	echo file_get_contents($file);
	exit(0);
}

function _echoJson($json,$expire='1 month') {
	if (!$json) return _echoExit();
	$json = (is_string($json)) ? $json : json_encode($json);
	_headersContent($json,'json',$expire);
	echo $json;
	exit(0);
}

function _echoXml($xml,$expire='1 month') {
	if (!$xml) return _echoExit();
	_headersContent($xml,'xml',$expire);
	echo $xml;
	exit(0);
}

function _echoLocation($location) {
	@header("Location:{$location}");
	exit(0);
}

function _echoExit($header='HTTP/1.0 404 Not Found') {
	@header($header);
	echo $header;
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

function _headersContent($content,$ext,$expire='1 month',$cors=FALSE) {
	if (!$content) return FALSE;
	$type = _contentType($ext);
	$created = time();
	$expire = _expireTime($expire);
	$etag = md5($content);
	_headers($type,$created,$expire,$etag,$cors);
}

function _contentType($ext,$reverse=FALSE) {
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
	if ($reverse) $types = array_flip($types);
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
