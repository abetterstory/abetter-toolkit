<section id="lab-inspector" class="lab-panel lab-inspector">

	@style('lab-inspector.scss')
	@style('lab-inspector-print.scss')
	@script('lab-inspector.js')

	<header>
		<a class="close" href="javascript:void(0)" onclick="lab_panelToggle(this)">×</a>
	</header>

	@component('lab.inspector.briefs',TRUE)
	@component('lab.inspector.requirements',TRUE)
	@component('lab.inspector.notes',TRUE)
	@component('lab.inspector.design',TRUE)

</section>
