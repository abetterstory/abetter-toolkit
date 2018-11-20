@php
$label = $label ?? "Menu";
$style = $style ?? "";
$brand = $brand ?? get_bloginfo('name');
$menu = $menu ?? [
	_lipsum('word'),
	_lipsum('word'),
	_lipsum('word'),
	_lipsum('word'),
	_lipsum('word')
]
@endphp
<block class="mockup--menu {{ $style }}" background border small>
	@style('mockup--menu.scss')
	<label>{{ $label }}</label>
	<row>
		<column left><p><strong>{{ $brand }}</strong></p></column>
		<column grow center>
			<row>
				@foreach ($menu AS $item)
				<column><p lipsum bold>{{ $item }}</p></column>
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
