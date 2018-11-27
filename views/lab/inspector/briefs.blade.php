@php

$label = "Brief";
$post = \ABetter\Wordpress\Post::$post;
$url = "/wp/wp-admin/post.php?post={$post->ID}&action=edit";
$link = "Add Brief";

$briefs = ($f = _wp_field('dev_mockup_briefs',$post)) ? (array) $f : [];

foreach ($briefs AS &$brief) {
	$brief = reset($brief);
	$brief->edit = "/wp/wp-admin/post.php?post={$brief->ID}&action=edit";
	$brief->message = ($f = _wp_field('dev_brief_message',$brief)) ? $f : "";
	$brief->attention = ($f = _wp_field('dev_brief_attention',$brief)) ? $f : "";
	$brief->proof = ($f = _wp_field('dev_brief_proof',$brief)) ? $f : "";
	$brief->action = ($f = _wp_field('dev_brief_action',$brief)) ? $f : "";
	$brief->voice = ($f = _wp_field('dev_brief_voice',$brief)) ? $f : "";
}
unset($brief);

@endphp
<section class="inspector--section inspector--briefs">
	<label><a href="{{$url}}" target="_blank">{{ $label }}</a></label>
	@foreach($briefs AS $brief)
	<article>
		@if($brief->message)
		<hgroup><p><label>Message</label>{{ $brief->message }}</p></hgroup>
		@endif
		@if($brief->attention)
		<hgroup><p><label>Attention</label>{{ $brief->attention }}</p></hgroup>
		@endif
		@if($brief->proof)
		<hgroup><p><label>Proof</label>{{ $brief->proof }}</p></hgroup>
		@endif
		@if($brief->action)
		<hgroup><p><label>Action</label>{{ $brief->action }}</p></hgroup>
		@endif
		@if($brief->voice)
		<hgroup><p><label>Voice</label>{{ $brief->voice }}</p></hgroup>
		@endif
		<a href="{{$brief->edit}}" target="_blank"><i class="fa fa-pen"></i></a>
	</article>
	@endforeach
	@if(empty($briefs))
	<nav><a href="{{$url}}" target="_blank"><i class="fa fa-plus"></i>{{ $link }}</a></nav>
	@endif
</section>
