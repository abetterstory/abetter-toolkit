/* lazy.js v1.0.1 */

(function(){

	window.Lazy = {};

	self = window.Lazy;

	self.$images = document.querySelectorAll('img[data-lazy]');

	self.onscrollcallbacks = [];
	self.onscrolldelay = null;

	self.onscroll = function(func,call) {
		self.onscrollcallbacks.push(func);
		if (call && typeof func === 'function') func.call();
	}

	self.onscrollcall = function() {
		var cb = self.onscrollcallbacks; if (!cb) return;
		for (var i = 0; i < cb.length; i++) {
			if (typeof cb[i] === 'function') cb[i].call();
		}
	}

	self.windowHeight = function() { return window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight; }
	self.scrollTop = function() { return document.documentElement.scrollTop; }
	self.elementHeight = function($e) { return $e.offsetHeight; }
	self.elementTop = function($e) { var t = 0; while ($e && !isNaN($e.offsetTop)) { t += $e.offsetTop - $e.scrollTop; $e = $e.offsetParent; } return t; }

	self.isvisible = function($e,m) {
		m = (m) ? m : 50;
		var wh = self.windowHeight();
		var st = self.scrollTop();
		var eh = self.elementHeight($e);
		var et = self.elementTop($e);
		var em = et + (eh / 2);
		var eb = et + eh;
		var sm = st + (wh / 2);
		var sb = st + wh;
		var v = false;
		return ((st + m) >= et || sm >= em || (sb + m) >= eb) ? true : false;
	}

	self.parseBreakpoints = function($e) {
		var o = {};
		var src = ($e.dataset.src || '').trim();
		var srcs = ($e.dataset.srcset || '').trim().split(',');
		if (src) o[0] = src;
		srcs.forEach(function(val){
			var s = val.trim().split(' ');
			var w = parseInt(s[1].match(/\d+/));
			var u = s[0];
			o[w] = u;
		});
		//console.log('parseBreakpoints',o);
	}

	// ---

	self.onscroll(function(){
		;[].forEach.call(document.querySelectorAll('img[data-lazy]'),function($e){
			if (!self.isvisible($e)) return;
			if (!$e.breakpoints) $e.breakpoints = self.parseBreakpoints($e);

			//console.log('visible',$e.breakpoints);

		});
	},true);

	// ---

	window.addEventListener('scroll',function(){
		if (!self.onscrollcallbacks || !self.onscrollcallbacks.length) return;
		if (self.onscrolldelay) window.clearTimeout(self.onscrolldelay);
		self.onscrolldelay = window.setTimeout(function(){
			self.onscrollcall();
		}, 100);
	});

	window.addEventListener('resize',function(){
		if (!self.onscrollcallbacks || !self.onscrollcallbacks.length) return;
		if (self.onscrolldelay) window.clearTimeout(self.onscrolldelay);
		self.onscrolldelay = window.setTimeout(function(){
			self.onscrollcall();
		}, 100);
	});

	// ---

	//console.log('lazy.js v1.0.0',self);

	//

	/*


	var responsiveDefaults = { "small": 300, "medium": 500, "large": 700, "xlarge": 900 };
	var responsivePrefix = '--';
	var responsiveDelay = 100;
	var responsiveTimer = null;

	// ---

	var responsiveInit = function(e) {
		e.breakpoints = e.dataset.responsive ? JSON.parse(e.dataset.responsive.replace(/\'/g,'"')) : responsiveDefaults;
		e.breakprefix = e.dataset.responsivePrefix ? e.dataset.responsivePrefix : responsivePrefix;
		if (e.dataset.responsivePrefix) delete e.dataset.responsivePrefix;
		e.dataset.responsive = 'true';
		responsiveElement(e);
	}

	var responsiveResize = function() {
		if (!window.responsiveElements) return;
		for (var e, i = 0; e = window.responsiveElements[i]; i++) {
			responsiveElement(e);
		}
	}

	var responsiveElement = function(e) {
		if (!e.breakpoints) return;
		Object.keys(e.breakpoints).forEach(function(breakpoint) {
	        if (e.offsetWidth >= e.breakpoints[breakpoint]) {
				e.classList.add(e.breakprefix+breakpoint);
			} else {
				e.classList.remove(e.breakprefix+breakpoint);
			}
		});
	}

	// ---

	window.responsiveElements = document.querySelectorAll('[data-responsive]');

	for (var e, i = 0; e = window.responsiveElements[i]; i++) responsiveInit(e);

	window.addEventListener('resize',function(event){
		clearTimeout(responsiveTimer);
		responsiveTimer = setTimeout(function(){
			responsiveResize();
		}, responsiveDelay);
	});

	*/

	/*
	self.elementPos = function($el) {
		var x = 0, y = 0;
		while ($el && !isNaN($el.offsetLeft) && !isNaN($el.offsetTop)) {
			x += $el.offsetLeft - $el.scrollLeft;
			y += $el.offsetTop - $el.scrollTop;
			$el = $el.offsetParent;
		}
		return { top: y, left: x }
	}
	*/

}());
