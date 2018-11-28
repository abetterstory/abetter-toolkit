@php
$opt = [
	'label' => $label ?? "Content",
	'style' => $style ?? "",
	'size' => $size ?? "",
	'icon' => $icon ?? "",
	'background' => $background ?? FALSE,
	'border' => $border ?? TRUE,
	'image' => $image ?? FALSE,
	'lipsum' => $lipsum ?? TRUE,
	'headline' => $headline ?? _lipsum('headline'),
	'body' => $body ?? _lipsum('body'),
];
$opt['body'] = ($opt['lipsum']) ? $opt['body'] : str_replace(' lipsum>','>',$opt['body']);
@endphp
<block class="mockup--content {{$opt['style']}}" {{$opt['size']}} {{$opt['background']?'background':''}} {{$opt['border']?'border':''}} {{$opt['image']?'image':''}}>
	@style('mockup--content.scss')
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
