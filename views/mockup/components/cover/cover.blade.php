@php
$opt = [
	'label' => $label ?? "Cover",
	'style' => $style ?? "center",
	'size' => $size ?? "xlarge",
	'icon' => $icon ?? "",
	'background' => $background ?? TRUE,
	'border' => $border ?? TRUE,
	'image' => $image ?? TRUE,
	'lipsum' => $lipsum ?? TRUE,
	'headline' => $headline ?? _lipsum('headline'),
	'body' => $body ?? _lipsum('lead'),
];
@endphp
<block class="mockup--cover {{$opt['style']}}" {{$opt['size']}} {{$opt['background']?'background':''}} {{$opt['border']?'border':''}} {{$opt['image']?'image':''}}>
	@style('mockup--cover.scss')
	<label>{{ $opt['label'] }}</label>
	<row>
		<column>
			<h2 {{$opt['lipsum']?'lipsum':''}}>{{ $opt['headline'] }}</h2>
			<p {{$opt['lipsum']?'lipsum':''}}>{!! $opt['body'] !!}</p>
		</column>
	</row>
</block>
