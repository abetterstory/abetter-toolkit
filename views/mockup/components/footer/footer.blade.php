@php
$label = $label ?? "Footer";
$style = $style ?? "";
@endphp
<hr space />
<hr />
<block class="mockup--footer {{ $style }}" background border medium>
	@style('mockup--footer.scss')
	<label>{{ $label }}</label>
	<row>
		<column>
			<h4 lipsum>{{ _lipsum('word') }}</h4>
			<p lipsum>{!! _lipsum('word:li:5') !!}</p>
		</column>
		<column>
			<h4 lipsum>{{ _lipsum('word') }}</h4>
			<p lipsum>{!! _lipsum('word:li:5') !!}</p>
		</column>
		<column>
			<h4 lipsum>{{ _lipsum('word') }}</h4>
			<p lipsum>{!! _lipsum('word:li:5') !!}</p>
		</column>
		<column>
			<h4 lipsum>{{ _lipsum('word') }}</h4>
			<p lipsum>{!! _lipsum('word:li:5') !!}</p>
		</column>
	</row>
</block>
