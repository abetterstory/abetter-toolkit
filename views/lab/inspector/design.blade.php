@php

$label = "Design";
$post = \ABetter\Wordpress\Post::$post;
$url = "/wp/wp-admin/post.php?post={$post->ID}&action=edit";
$link = "Add Design";

$designs = ($f = _wp_field('dev_mockup_design',$post)) ? (array) $f : [];
foreach ($designs AS &$design) {
	$design = ['preview' => _image($design['url'],'w1000'), 'url' => _image($design['url'],'x')];
}
unset($design);

@endphp
<section class="inspector--section inspector--design">
	<label><a href="{{$url}}" target="_blank">{{ $label }}</a></label>
	@foreach($designs AS $design)
	<article>
		<figure><a href="{{ $design['url'] }}" target="_blank"><img src="{{ $design['preview'] }}" /></a></figure>
	</article>
	@endforeach
	@if(empty($designs))
	<nav><a href="{{$url}}" target="_blank"><i class="fa fa-plus"></i>{{ $link }}</a></nav>
	@endif
</section>
