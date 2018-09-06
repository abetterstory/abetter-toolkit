<li id="{{ $item->selector }}" class="{{ $item->status }} {{ $item->current }} {{ $item->front }} {{ ($item->items)?'has-children':'' }}">
	<a class="link label" href="{{ $item->url }}">{{ $item->label }}</a>
	<a class="action edit" href="{{ $item->edit }}" target="_blank"><i class="fa fa-pen"></i></a>
	<a class="action view" href="{{ $item->preview }}?view" target="_blank"><i class="fa fa-eye"></i></a>
	<a class="tree" onclick="labNav_itemToggle(this)"></a>
	@if ($item->items)
	<ul>
		@foreach ($item->items AS $subitem)
			@component('lab.navigator.item',['item' => $subitem],TRUE)
		@endforeach
	</ul>
	@endif
</li>
