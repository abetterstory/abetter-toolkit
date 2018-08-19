<section class="component--demo">

	@inject('Demo')

	<div class="row uk-section">
		<div class="column uk-container">

			<h2>{{ $Demo->title }}</h2>

			<p class="lead">{{ $Demo->lead }}</p>

			<figure>
				<img src="{{ $Demo->image }}" />
			</figure>

			<article>
				{!! $Demo->body !!}
			</article>

		</div>
	</div>

	@style('demo.scss')
	@script('demo.js')

</section>
