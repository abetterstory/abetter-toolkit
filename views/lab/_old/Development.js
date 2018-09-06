/* Development.JS */

$('filter select').change(function(){
	var s = $(this);
	var v = s.val();
	var k = s.attr('name');
	if (!v) {
		document.cookie = k+'=;';
		s.removeClass('selected');
	} else {
		document.cookie = k+'='+v;
		s.addClass('selected');
	}
	location.reload();
});

$('block').each(function() {
	var t, b = $(this);
	if (t = $(this).attr('title')) b.append('<label>' + t + '</label>');
	if (!b.find('row').length) b.wrapInner('<row></row>');
	if (!b.find('row column').length) b.find('row').wrapInner('<column></column>');
});

$('column').each(function() {
	var t, b = $(this);
	if (t = $(this).attr('title')) b.append('<label>' + t + '</label>');
});

// ---

if (localStorage.getItem('_dev-mock')) $('body').addClass('dev-mock');
if (localStorage.getItem('_dev-body')) $('body').addClass('dev-body');
//if (localStorage.getItem('_dev-modal')) $('body').addClass('dev-' + localStorage.getItem('_dev-modal'));
if (localStorage.getItem('_dev-stakeholder')) $('body').addClass('dev-stakeholder');
if (localStorage.getItem('_dev-audience')) $('body').addClass('dev-audience');
if (localStorage.getItem('_dev-requirement')) $('body').addClass('dev-requirement');
if (localStorage.getItem('_dev-left')) $('body').addClass('dev-left');
if (localStorage.getItem('_dev-right')) $('body').addClass('dev-right');

// ---

var ls; if (ls = localStorage.getItem('_dev-left-scroll')) setTimeout(function() {
	$('#development .left').scrollTop(ls);
}, 0);

var lr; if (lr = localStorage.getItem('_dev-right-scroll')) setTimeout(function() {
	$('#development .right').scrollTop(lr);
}, 0);

$('#development .left').scroll(function(e) {
	localStorage.setItem('_dev-left-scroll', e.target.scrollTop);
});

$('#development .right').scroll(function(e) {
	localStorage.setItem('_dev-right-scroll', e.target.scrollTop);
});

// ---

$('#development li.has-children > a.tree').click(function(e) {
	var $li = $(e.target).parent();
	$li.toggleClass('closed');
	if ($li.hasClass('closed')) {
		localStorage.setItem('_dev-closed-' + $li.attr('id'), 1);
	} else {
		localStorage.removeItem('_dev-closed-' + $li.attr('id'));
	}
});

$('#development li.has-children').each(function() {
	var $li = $(this);
	if (localStorage.getItem('_dev-closed-' + $li.attr('id'))) $li.addClass('closed');
});

$('#development li.all-children > a.tree').click(function(e) {
	var closed = $('#development li.has-children.closed').length;
	$('#development li.has-children').each(function() {
		var $li = $(this);
		if (!closed) {
			$li.addClass('closed');
			localStorage.setItem('_dev-closed-' + $li.attr('id'), 1);
		} else {
			$li.removeClass('closed');
			localStorage.removeItem('_dev-closed-' + $li.attr('id'));
		}
	});
});

$('#development li.toggle-parent').click(function(e) {
	var $li = $(this);
	var $ul = $li.parent();
	$ul.toggleClass('closed');
	if ($ul.hasClass('closed')) {
		localStorage.setItem('_dev-closed-' + $li.attr('id'), 1);
	} else {
		localStorage.removeItem('_dev-closed-' + $li.attr('id'));
	}
});

$('#development li.toggle-parent').each(function() {
	var $li = $(this);
	var $ul = $li.parent();
	if (localStorage.getItem('_dev-closed-' + $li.attr('id'))) $ul.addClass('closed');
});
