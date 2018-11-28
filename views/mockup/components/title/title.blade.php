@php
$post = \ABetter\Wordpress\Post::$post;
$parents = get_post_ancestors($post) ?? [];
@endphp
<block class="mockup--title {{ $style ?? '' }}">
	@style('mockup--title.scss')
	<row>
		<column>
			<h1>
				@foreach($parents AS $id)
				<span class="parent">{{ get_post($id)->post_title }}</span>
				@endforeach
				<span class="current">{{ $post->post_title }}</span>
			</h1>
		</column>
	</row>
</block>
