<?php

if (!function_exists('_dictionary')) {

	function _dictionary($string,$lang="") {
		/* FIX: allow multi underscore slugs */
		if (preg_match('/^(\w+)_(\w+)$/',$string)) return _dictionarySlug($string,$lang);
		return _dictionaryString($string,$lang);
	}

	function _dictionaryString($string,$lang="") {
		if (!preg_match_all('/\{\{([^\}]+)\}\}/',$string,$match)) return $string;
		foreach ($match[1] AS $i => $slug) $string = str_replace($match[0][$i],_dictionarySlug($slug,$lang),$string);
		return $string;
	}

	function _dictionarySlug($slug,$lang="",$default="",$fallback="") {
		if (!$dict = _dictionaryObject($slug,$lang)) return $slug;
		$return = _dictionaryContent($dict,$lang,$default);
		$return = ($return) ? $return : $default;
		$return = ($return) ? $return : $slug;
		$return = str_replace('\{','{',$return);
		$return = str_replace('\}','}',$return);
		return $return;
	}

	function _dictionaryObject($dict,$lang="") {
		return (is_object($dict)) ? $dict : get_page_by_path(preg_replace('/^dictionary_/',"",$dict),OBJECT,'dictionary');
	}

	function _dictionaryContent($dict,$lang="",$default="") {
		$dict = _dictionaryObject($dict);
		$content = (!empty($dict->post_content)) ? $dict->post_content : NULL;
		return ($content) ? $content : $default;
	}

}
