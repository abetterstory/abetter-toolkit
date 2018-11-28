@php

$post = \ABetter\Wordpress\Post::$post;
$url = "/wp/wp-admin/post.php?post={$post->ID}&action=edit";

@endphp
<section id="lab-editor" class="lab-panel lab-editor lab-mockup lab-mockup-grid">

	@style('lab-editor.scss')
	@style('lab-mockup.scss')
	@style('lab-mockup-print.scss')

	@script('lab-editor.js')

	<header>
		<a class="edit" href="{{$url}}" target="_blank"><i class="fa fa-pen"></i></a>
		<a class="close" href="javascript:void(0)" onclick="lab_panelToggle(this)">×</a>
	</header>

	<section id="mockup">

		@php

		$options = ($f = get_field('dev_mockup_options',$post)) ? (array) $f : [];
		$current = (in_array('inherit',$options) && $post->post_parent) ? get_post($post->post_parent) : $post;
		$mockup = ($f = get_field('dev_mockup_template',$current)) ? _render($f,get_defined_vars()) : "";

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

		@mockup('title')

		{!! $mockup !!}

	</section>

</section>
