@php
$opt = [
	'label' => $label ?? "Footer",
	'style' => $style ?? "",
	'size' => $size ?? "medium",
	'icon' => $icon ?? "",
	'background' => $background ?? TRUE,
	'border' => $border ?? TRUE,
	'image' => $image ?? FALSE,
	'lipsum' => $lipsum ?? TRUE,
];
@endphp
<hr />
<block class="mockup--footer  {{$opt['style']}}" {{$opt['size']}} {{$opt['background']?'background':''}} {{$opt['border']?'border':''}} {{$opt['image']?'image':''}}>
	@style('mockup--footer.scss')
	<label>{{ $opt['label'] }}</label>
	<row>
		<column>
			<h4 {{$opt['label']?'lipsum':''}}>{{ _lipsum('word') }}</h4>
			<p {{$opt['label']?'lipsum':''}}>{!! _lipsum('word:li:5') !!}</p>
		</column>
		<column>
			<h4 {{$opt['label']?'lipsum':''}}>{{ _lipsum('word') }}</h4>
			<p {{$opt['label']?'lipsum':''}}>{!! _lipsum('word:li:5') !!}</p>
		</column>
		<column>
			<h4 {{$opt['label']?'lipsum':''}}>{{ _lipsum('word') }}</h4>
			<p {{$opt['label']?'lipsum':''}}>{!! _lipsum('word:li:5') !!}</p>
		</column>
		<column>
			<h4 {{$opt['label']?'lipsum':''}}>{{ _lipsum('word') }}</h4>
			<p {{$opt['label']?'lipsum':''}}>{!! _lipsum('word:li:5') !!}</p>
		</column>
	</row>
</block>
