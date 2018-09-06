$Ready(function(){

	var $this = document.getElementById('lab');

	// ---

	$this.querySelectorAll('section.lab-panel').forEach(function($item){
		var id = $item.id;
		var preset = localStorage.getItem(id);
		if (preset === null) return;
		if (preset == 0) {
			document.body.classList.remove(id);
		} else {
			document.body.classList.add(id);
		}
	});

	// ---

	lab_panelToggle = function(e,id) {
		if (!id && typeof e === 'string') {
			id = e.toString();
		} else if (!id) {
			id = e.parentNode.parentNode.id;
		}
		if (document.body.classList.contains(id)) {
			document.body.classList.remove(id);
			localStorage.setItem(id, 0);
		} else {
			document.body.classList.add(id);
			localStorage.setItem(id, 1);
		}
	}

});
