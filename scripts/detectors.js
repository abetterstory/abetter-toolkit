/* detectors.js v1.0.0 */

(function(){

	var $doc = document;
	var $html = $doc.documentElement;
	var $htmlclass = $html.classList;
	var $nav = navigator;

	var ua = $nav.userAgent;

	var detect = function(test,is,not) { if (test()) $htmlclass.add(is); else if (not) $htmlclass.add(not); };

	detect((function(){ try { $doc.createEvent('TouchEvent'); return true; } catch(e) { return false; } }),'is-touch','no-touch');
	detect((function(){ return ua.match(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i); }),'is-mobile','is-desktop');

	detect((function(){ return ua.match(/Android/i); }),'is-android');
	detect((function(){ return ua.match(/iPhone|iPad|iPod/i); }),'is-ios');
	detect((function(){ return ua.match(/BlackBerry/i); }),'is-blackberry');
	detect((function(){ return ua.match(/Windows Phone|IEMobile|WPDesktop/i); }),'is-winphone');
	detect((function(){ return ua.match(/MSIE|IE |Trident/i); }),'is-msie');
	detect((function(){ return ua.match(/Edge/i); }),'is-edge');
	detect((function(){ return ua.match(/iP(ad|hone|od).+Version\/[\d\.]+.*Safari/i); }),'is-mobile-safari');

	detect((function(){ return (('standalone' in $nav) && $nav['standalone']) ? true : false; }),'is-standalone');
	detect((function(){ return ($doc.location.href.match(/(\?|\&)homescreen/) || $doc.cookie.match(/homescreen/)) ? true : false; }),'is-homescreen');

	if ($doc.location.href.match(/(\?|\&)homescreen/)) $doc.cookie = "homescreen=1; path=/;";

}());
