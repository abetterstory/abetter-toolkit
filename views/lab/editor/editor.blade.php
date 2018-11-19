<section id="lab-editor" class="lab-panel lab-editor lab-mockup lab-mockup-grid">

	@style('editor.scss')

	<header>
		<a class="close" href="javascript:void(0)" onclick="lab_panelToggle(this)">×</a>
	</header>

	<section id="mockup">

		@style('mockup.scss')

		@php

		$post = \ABetter\Wordpress\Controller::$handle->post ?? NULL;
		$options = ($f = get_field('mockup_options',$post)) ? (array) $f : [];
		$post = (in_array('inherit',$options)) ? get_post($post->post_parent) : NULL;
		$mockup = ($f = get_field('mockup_template',$post)) ? _render($f,get_defined_vars()) : "";

		if (!$mockup) {
			$view = \ABetter\Wordpress\Controller::$handle->view ?? NULL;
			$mockup = ($view && view()->exists('mockup.'.$view)) ? view('mockup.'.$view)->render() : "";
		}

		if (!$mockup) {
			$suggestions = \ABetter\Wordpress\Controller::$handle->suggestions ?? [];
			foreach ($suggestions AS $suggestion) {
				$mockup = (!$mockup && view()->exists('mockup.'.$suggestion)) ? view('mockup.'.$suggestion)->render() : $mockup;
			}
		}

		@endphp

		@mockup('header')

		{!! $mockup !!}

	</section>

	@script('editor.js')

</section>
