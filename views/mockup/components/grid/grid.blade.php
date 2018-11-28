@php
$opt = [
	'label' => $label ?? "",
	'style' => $style ?? "",
	'size' => $size ?? "medium",
	'icon' => $icon ?? "",
	'background' => $background ?? TRUE,
	'border' => $border ?? TRUE,
	'image' => $image ?? FALSE,
	'lipsum' => $lipsum ?? TRUE,
	'headline' => $headline ?? "",
	'body' => $body ?? "",
	'items' => $items ?? [[""],[""],[""],[""]],
];
$opt['headline'] = $opt['headline'] ?: (in_array($opt['size'],['large','xlarge'])) ? _lipsum(50) : _lipsum(40);
$opt['body'] = $opt['body'] ?: (in_array($opt['size'],['large','xlarge'])) ? _lipsum(250) : _lipsum(150);
foreach ($opt['items'] AS &$i) $i = array_merge($opt,(is_array($i)) ? $i : ['label' => $i]);
@endphp
<block class="mockup--grid {{ $opt['style'] }}" {{$opt['size']}}>
	@style('mockup--grid.scss')
	<row>
		@foreach ($opt['items'] AS $item)
		<column class="{{$item['style']}}" {{$item['size']}} {{$item['background']?'background':''}} {{$item['border']?'border':''}} {{$item['image']?'image':''}}>
			@if($item['label'])<label>{{ $item['label'] }}</label>@endif
			<article>
				@if($item['icon'])<icon class="{{ $item['icon'] }}"></icon>@endif
				@if($item['headline'])<h4 {{$item['lipsum']?'lipsum':''}}>{{ $item['headline'] }}</h4>@endif
				@if($item['body'])<p {{$item['lipsum']?'lipsum':''}}>{!! $item['body'] !!}</p>@endif
			</article>
		</column>
		@endforeach
	</row>
</block>
