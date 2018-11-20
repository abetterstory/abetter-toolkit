$Ready(function(){

	var $this = document.getElementById('lab-navigator');

	// ---

	var scroll; if (scroll = localStorage.getItem('lab-nav-scroll')) {
		setTimeout(function() {
			$this.scrollTop = scroll;
		}, 0);
	}

	$this.addEventListener('scroll',function(e) {
		localStorage.setItem('lab-nav-scroll', e.target.scrollTop);
	});

	// ---

	$this.querySelectorAll('li.has-children').forEach(function($item){
		var open; if (open = localStorage.getItem('lab-nav-item-'+$item.id)) {
			$item.classList.add('show-children');
		}
	});

	// ---

	labNav_itemToggle = function(e) {
		var $item = e.parentNode;
		var id = $item.id;
		if ($item.classList.contains('show-children')) {
			$item.classList.remove('show-children');
			localStorage.removeItem('lab-nav-item-'+id);
		} else {
			$item.classList.add('show-children');
			localStorage.setItem('lab-nav-item-'+id, 1);
		}
	}

	labNav_treeToggle = function(e) {
		var $ul = e.parentNode.parentNode;
		var $items = $ul.querySelectorAll('li.has-children');
		var open = $ul.querySelectorAll('li.show-children').length;
		if (open || $ul.classList.contains('show-all')) {
			$items.forEach(function($item){
				$item.classList.remove('show-children');
				localStorage.removeItem('lab-nav-item-'+$item.id);
			});
			$ul.classList.remove('show-all');
		} else {
			$items.forEach(function($item){
				$item.classList.add('show-children');
				localStorage.setItem('lab-nav-item-'+$item.id, 1);
			});
			$ul.classList.add('show-all');
		}
	}

});
