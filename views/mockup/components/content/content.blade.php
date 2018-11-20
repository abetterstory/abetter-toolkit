@php
$style = $style ?? "";
$label = $label ?? "Content";
$headline = $headline ??  _lipsum('headline');
$body = $body ?? _lipsum('body');
@endphp
<block class="mockup--content {{ $style }}" border>
	@style('mockup--content.scss')
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
