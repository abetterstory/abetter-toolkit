@php
$style = $style ?? "";
$label = $label ?? "List";
$headline = $headline ??  _lipsum('headline');
$body = $body ?? _lipsum('headline:li:10');
@endphp
<block class="mockup--list {{ $style }}" border>
	@style('mockup--list.scss')
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
