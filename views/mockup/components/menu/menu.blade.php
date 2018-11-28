@php
$opt = [
	'label' => $label ?? "Menu",
	'style' => $style ?? "",
	'size' => $size ?? "small",
	'icon' => $icon ?? "",
	'background' => $background ?? TRUE,
	'border' => $border ?? TRUE,
	'image' => $image ?? FALSE,
	'lipsum' => $lipsum ?? TRUE,
	'headline' => $headline ?? _lipsum(50),
	'body' => $body ?? _lipsum(200),
	'brand' => $brand ?? get_bloginfo('name'),
	'items' => $items ?? [_lipsum('word'),_lipsum('word'),_lipsum('word'),_lipsum('word'),_lipsum('word')],
];
@endphp
<block class="mockup--menu {{$opt['style']}}" {{$opt['size']}} {{$opt['background']?'background':''}} {{$opt['border']?'border':''}} {{$opt['image']?'image':''}}>
	@style('mockup--menu.scss')
	<label>{{ $opt['label'] }}</label>
	<row>
		<column left><p><strong>{{ $opt['brand'] }}</strong></p></column>
		<column grow center>
			<row>
				@foreach ($opt['items'] AS $item)
				<column><p {{$opt['lipsum']?'lipsum':''}} bold>{{ $item }}</p></column>
				@endforeach
			</row>
		</column>
		<column right><p>
			<i class="fa fa-globe"></i>
			&nbsp;
			<i class="fa fa-search"></i>
		</p></column>
	</row>
</block>
