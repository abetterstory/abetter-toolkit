<section class="component-demo">

	@inject('Demo')

	<h1>{{ $Demo->text }}</h1>
	<p>{{ $Demo->notset or '!$Demo->notset' }}</p>

	@style('demo.scss')
	@script('demo.js')

</section>
