@php
$opt = [
	'label' => $label ?? "List",
	'style' => $style ?? "",
	'size' => $size ?? "",
	'icon' => $icon ?? "",
	'background' => $background ?? FALSE,
	'border' => $border ?? TRUE,
	'image' => $image ?? FALSE,
	'lipsum' => $lipsum ?? TRUE,
	'headline' => $headline ?? _lipsum('headline'),
	'body' => $body ?? _lipsum('headline:li:10'),
];
@endphp
<block class="mockup--list {{$opt['style']}}" {{$opt['size']}} {{$opt['background']?'background':''}} {{$opt['border']?'border':''}} {{$opt['image']?'image':''}}>
	@style('mockup--list.scss')
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
