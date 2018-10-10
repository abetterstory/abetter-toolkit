/* tracker.js v1.0.0 */

(function(){

	var env = document.documentElement.getAttribute('env');

	window.trackonscrollcallbacks = [];
	window.trackonscrolldelay = null;

	window.trackdebug = (env != 'production') ? true : false;
	window.trackindex = 0; try { window.trackindex = window.localStorage.getItem('trackerindex'); } catch (e) { window.trackindex = -1; };

	window.track = function(action, category, label, debug) {
		if (!category) category = 'event';
		if (!label) label = window.location.pathname;
		if ('ga' in window) ga('send', 'event', category, action, label);
		if (debug || window.trackdebug) console.log('track',action+':'+category+':'+label);
	}

	window.log = function(action, category, log, debug) {
		if (debug || window.trackdebug) console.log('log',log);
	}

	window.onerror = function(message, file, line) {
	  	setTimeout(function() {
			var category = 'javascript';
			var action = 'error';
			var label = message + ':' + file + ':' + line + '@' + window.location.pathname+':'+navigator.userAgent;
			window.track(action,category,label);
			window.log('debug',category,{label:label,message:message,file:file,line:line});
	  	}, 100);
		return true;
	}

	// ---

	;[].forEach.call(document.querySelectorAll('[data-track]'),function($track){
		$track.addEventListener('click',function(e){
			var params = ($track.dataset.track || '').split(':');
			var category = (params[0]) ? params[0] : 'link';
			var label = (params[1]) ? params[1] : $track.getAttribute('href') || window.location.pathname;
			var action = (params[2]) ? params[2] : 'click';
			window.track(action,category,label);
		});
	});

	// ---

	window.trackonscroll = function(func,call) {
		window.trackonscrollcallbacks.push(func);
		if (call && typeof func === 'function') func.call();
	}

	window.trackonscrollcall = function() {
		var cb = window.trackonscrollcallbacks;
		for (var i = 0; i < cb.length; i++) {
			if (typeof cb[i] === 'function') cb[i].call();
		}
	}

	window.trackvisible = function($el,m) {
		m = (m) ? m : 50;
		var eh = $el.offsetHeight;
		var et = $el.offsetTop;
		var em = et + (eh / 2);
		var eb = et + eh;
		var wh = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
		var st = document.documentElement.scrollTop;
		var sm = st + (wh / 2);
		var sb = st + wh;
		var v = false;
		return ((st + m) >= et || sm >= em || (sb + m) >= eb) ? true : false;
	}

	window.addEventListener('scroll',function(){
		if (!window.trackonscrollcallbacks.length) return;
		if (window.trackonscrolldelay) window.clearTimeout(window.trackonscrolldelay);
		window.trackonscrolldelay = window.setTimeout(function(){
			window.trackonscrollcall();
		}, 100);
	});

	window.trackonscroll(function(){
		;[].forEach.call(document.querySelectorAll('[data-track-view]'),function($track){
			if (!window.trackvisible($track)) return;
			var params = ($track.dataset.trackView || '').split(':');
			var category = (params[0]) ? params[0] : $track.getAttribute('id');
			var label = (params[1]) ? params[1] : $track.getAttribute('href') || window.location.pathname;
			var action = (params[2]) ? params[2] : 'view';
			window.track(action,category,label);
			$track.removeAttribute('data-track-view');
		});
	},true);

}());
