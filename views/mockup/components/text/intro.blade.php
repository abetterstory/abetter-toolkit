@php
$style = $style ?? "";
$label = $label ?? "Text";
$headline = $headline ??  _lipsum('headline');
$body = $body ?? _lipsum('large');
@endphp
<block class="mockup--intro {{ $style }}">
	@style('mockup--intro.scss')
	<label>{{ $label }}</label>
	<row>
		<column>
			<article>
				<h2 lipsum>{{ $headline }}</h2>
				<p lipsum>{!! $body !!}</p>
			</article>
		</column>
	</row>
</block>
