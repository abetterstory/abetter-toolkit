@php
$opt = [
	'label' => $label ?? "Intro",
	'style' => $style ?? "",
	'size' => $size ?? "",
	'icon' => $icon ?? "",
	'background' => $background ?? FALSE,
	'border' => $border ?? FALSE,
	'image' => $image ?? FALSE,
	'lipsum' => $lipsum ?? TRUE,
	'headline' => $headline ?? _lipsum('headline'),
	'body' => $body ?? _lipsum('large'),
];
@endphp
<block class="mockup--intro {{$opt['style']}}" {{$opt['size']}} {{$opt['background']?'background':''}} {{$opt['border']?'border':''}} {{$opt['image']?'image':''}}>
	@style('mockup--intro.scss')
	<label>{{ $opt['label'] }}</label>
	<row>
		<column>
			<article>
				<h2 {{$opt['lipsum']?'lipsum':''}}>{{ $opt['headline'] }}</h2>
				<p {{$opt['lipsum']?'lipsum':''}}>{!! $opt['body'] !!}</p>
			</article>
		</column>
	</row>
</block>
