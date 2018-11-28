<aside id="lab">

	<script>window.$Ready=(window.$Ready)?window.$Ready:function(fn){if(document.readyState!='loading')return fn.call();document.addEventListener("DOMContentLoaded",function(){fn.call()})}</script>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">

	@inject('Lab')

	@style('lab.scss')

	@component('lab.navigator',TRUE)
	@component('lab.editor',TRUE)
	@component('lab.inspector',TRUE)
	@component('lab.bar',TRUE)

	@style('lab-print.scss')

	@script('lab.js')

</aside>
