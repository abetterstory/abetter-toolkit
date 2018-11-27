@php

$label = "Requirements";
$post = \ABetter\Wordpress\Post::$post;
$url = "/wp/wp-admin/post.php?post={$post->ID}&action=edit";
$link = "Add Requirement";

$requirements = ($f = _wp_field('dev_mockup_requirements',$post)) ? (array) $f : [];

foreach ($requirements AS &$req) {
	$req = reset($req);
	$req->edit = "/wp/wp-admin/post.php?post={$req->ID}&action=edit";
	$req->who = ($f = _wp_field('dev_requirement_who',$req)) ? $f : NULL;
	$req->who_label = $req->who->post_title ?? "";
	$req->who_link = (!empty($req->who->ID)) ? "/wp/wp-admin/post.php?post={$req->who->ID}&action=edit" : "";
	$req->importance = ($f = _wp_field('dev_requirement_importance',$req)) ? $f : "";
	$req->what = ($f = _wp_field('dev_requirement_what',$req)) ? $f : "";
	$req->why = ($f = _wp_field('dev_requirement_why',$req)) ? $f : "";
	$req->how = ($f = _wp_field('dev_requirement_how',$req)) ? $f : "";
	$req->when = ($f = _wp_field('dev_requirement_when',$req)) ? $f : 0;
	$req->type = ($f = _wp_field('dev_requirement_type',$req)) ? $f : "";
	$req->status = ($f = _wp_field('dev_requirement_status',$req)) ? $f : "";
	// ---
	$req->importance = strtolower($req->importance);
	$req->what = (!preg_match('/^I /',$req->what)) ? lcfirst($req->what) : $req->what;
	$req->why = (!preg_match('/^I /',$req->why)) ? lcfirst($req->why) : $req->why;
}
unset($req);

@endphp
<section class="inspector--section inspector--requirements">
	<label><a href="{{$url}}" target="_blank">{{ $label }}</a></label>
	@foreach($requirements AS $req)
	<article>
		<hgroup>
			<p><span>As </span><who>{{$req->who_label}}</who><span> I </span><importance>{{$req->importance}} </importance><what>{{$req->what}}</what>@if($req->why)<span>, so </span><why>{{$req->why}}</why><span><dot>.</dot></span>@else<dot>…</dot>@endif</p>
		</hgroup>
		<a href="{{$req->edit}}" target="_blank"><i class="fa fa-pen"></i></a>
	</article>
	@endforeach
	@if(empty($requirements))
	<nav><a href="{{$url}}" target="_blank"><i class="fa fa-plus"></i>{{ $link }}</a></nav>
	@endif
</section>
