<?php

if (!function_exists('_wp_content')) {

	function _wp_content($post,$lang=NULL,$return=NULL) {
		$return = $post->post_content ?? ""; // Current
		if ($lang === FALSE || empty($post->l10n->translations)) return $return; // No WPML
		if ($lang && ($id = $post->l10n->translations[$lang] ?? NULL) && ($req = get_post($id))) {
			$return = ($f = $req->post_content ?? "") ? $f : $return; // Lang
		}
		if (!$return && ($id = $post->l10n->translations[$post->l10n->default] ?? NULL) && ($def = get_post($id))) {
			$return = ($f = $def->post_content ?? "") ? $f : $return; // Default
		}
		return $return; // Fallback
	}

}

if (!function_exists('_wp_field')) {

	function _wp_field($key,$post,$lang=NULL,$return=NULL) {
		$return = ($f = get_field($key,$post)) ? $f : $return; // Current
		if ($lang === FALSE || empty($post->l10n->translations)) return $return; // No WPML
		if ($lang && ($id = $post->l10n->translations[$lang] ?? NULL) && ($req = get_post($id))) {
			$return = ($f = get_field($key,$req)) ? $f : $return; // Lang
		}
		if (!$return && ($id = $post->l10n->translations[$post->l10n->default] ?? NULL) && ($def = get_post($id))) {
			$return = ($f = get_field($key,$def)) ? $f : $return; // Default
		}
		return $return; // Fallback
	}

}

if (!function_exists('_wp_option')) {

	function _wp_option($key) {
		return ($var = $GLOBALS['wpdb']->get_var('SELECT option_value FROM wp_options WHERE option_name = "'.$key.'"')) ? $var : NULL;
	}

}
