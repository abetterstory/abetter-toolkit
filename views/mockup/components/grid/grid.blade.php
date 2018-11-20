@php
$items = $items ?? [["","","",""]];
$style = $style ?? "";
$label = $label ?? "";
$icon = $icon ?? "";
$background = (($background ?? TRUE) !== FALSE) ? 'background' : '';
$border = (($border ?? TRUE) !== FALSE) ? 'border' : '';
$image = (($image ?? FALSE) !== FALSE) ? 'image' : '';
$lipsum = (($lipsum ?? TRUE) !== FALSE) ? 'lipsum' : '';
$headline = $headline ?? _lipsum(50);
$body = $body ?? _lipsum(200);
foreach ($items AS &$item) {
	$item = (is_array($item)) ? $item : ['label' => $item];
	$item['style'] = $item['style'] ?? "";
	$item['label'] = $item['label'] ?? $label;
	$item['icon'] = $item['icon'] ?? $icon;
	$item['background'] = (($item['background'] ?? TRUE) !== FALSE) ? $background : '';
	$item['border'] = (($item['border'] ?? TRUE) !== FALSE) ? $border : '';
	$item['image'] = (($item['image'] ?? FALSE) !== FALSE) ? $image : '';
	$item['lipsum'] = (($item['lipsum'] ?? TRUE) !== FALSE) ? $lipsum : '';
	$item['headline'] = $item['headline'] ?? $headline;
	$item['body'] = $item['body'] ?? $body;
}
unset($item);
@endphp
<block class="mockup--grid {{ $style }}">
	@style('mockup--grid.scss')
	<row>
		@foreach ($items AS $item)
		<column class="{{$item['style']}}" {{$item['background']}} {{$item['border']}} {{$item['image']}}>
			@if($item['label'])<label>{{ $item['label'] }}</label>@endif
			<article>
				@if($item['icon'])<icon class="{{ $item['icon'] }}"></icon>@endif
				@if($item['headline'])<h4 {{$item['lipsum']}}>{{ $item['headline'] }}</h4>@endif
				@if($item['body'])<p {{$item['lipsum']}}>{!! $item['body'] !!}</p>@endif
			</article>
		</column>
		@endforeach
	</row>
</block>
