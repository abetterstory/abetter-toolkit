@php
$label = $label ?? "Cover";
$style = $style ?? "center";
$headline = $headline ?? _lipsum('headline');
$body = $body ?? _lipsum('lead');
$size = (!preg_match('/small|medium|xlarge|large/',$style)) ? 'xlarge' : '';
@endphp
<block class="mockup--cover {{ $style }}" background border image {{ $size }}>
	@style('mockup--cover.scss')
	<label>{{ $label }}</label>
	<row>
		<column>
			<h2 lipsum>{{ $headline }}</h2>
			<p lipsum>{{ $body }}</p>
		</column>
	</row>
</block>
