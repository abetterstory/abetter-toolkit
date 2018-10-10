/* responsive.js v1.0.0 */

(function(){

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

}());
