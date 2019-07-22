<?php

if (!function_exists('_dictionary')) {

	function _dictionary($string,$lang=NULL,$default=NULL) {
		/* FIX: allow multi underscore slugs */
		if (preg_match('/^(\w+)_(\w+)$/',$string)) return _dictionarySlug($string,$lang,$default);
		return _dictionaryString($string,$lang,$default);
	}

	function _dictionaryString($string,$lang=NULL,$default=NULL) {
		if (!preg_match_all('/\{\{([^\}]+)\}\}/',$string,$match)) return $string;
		foreach ($match[1] AS $i => $slug) $string = str_replace($match[0][$i],_dictionarySlug($slug,$lang,$default),$string);
		return $string;
	}

	function _dictionarySlug($slug,$lang=NULL,$default=NULL,$fallback=NULL) {
		if (!$dict = _dictionaryObject($slug,$lang,$default)) return ($default !== NULL) ? $default : $slug;
		$return = _dictionaryContent($dict,$lang,$default);
		$return = ($return) ? $return : (($default !== NULL) ? $default : $slug);
		$return = str_replace('\{','{',$return);
		$return = str_replace('\}','}',$return);
		return $return;
	}

	function _dictionaryObject($dict,$lang=NULL,$default=NULL) {
		if (!_wp_loaded()) return $dict;
		$dict = (is_object($dict)) ? $dict : get_page_by_path(preg_replace('/^dictionary_/',"",$dict),OBJECT,'dictionary');
		if ($dict && function_exists('icl_object_id')) {
			$lang = (!empty($lang)) ? $lang : ICL_LANGUAGE_CODE;
			if ($trid = $GLOBALS['wpdb']->get_var('SELECT trid FROM wp_icl_translations WHERE (element_id = "'.$dict->ID.'" AND element_type = "post_dictionary")')) {
				if ($translation = $GLOBALS['wpdb']->get_var('SELECT element_id FROM wp_icl_translations WHERE (trid = "'.$trid.'" AND language_code = "'.$lang.'" AND element_type = "post_dictionary")')) {
					$dict = get_page($translation);
				}
			}
		}
		return $dict;
	}

	function _dictionaryContent($dict,$lang=NULL,$default=NULL) {
		$dict = _dictionaryObject($dict,$lang,$default);
		$content = (!empty($dict->post_content)) ? $dict->post_content : NULL;
		return ($content) ? $content : $default;
	}

}
