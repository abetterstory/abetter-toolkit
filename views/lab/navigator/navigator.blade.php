<?php

class Navigator extends ABetter\Toolkit\Component {
	public function build() {


		$this->web_label = "Wordpress Content";
		$this->web_admin_new = "Add new";
		$this->web_admin_goto = "Go to";
		$this->web_admin_dash = "Dash";
		$this->web_admin_page = "Page";
		$this->web_admin_pages = "Pages";
		$this->web_admin_post = "Post";
		$this->web_admin_posts = "Posts";
		$this->web_admin_language = "Language";

		$this->web_index = new \ABetter\Wordpress\Index();
		$this->web_items = $this->web_index->items;

		$this->web_languages = [];

		if (function_exists('icl_get_languages')) {
			global $sitepress;
			foreach (icl_get_languages() AS $lang) {
				$this->web_languages[strtoupper($lang['code'])] = ($lang['code'] != $sitepress->get_default_language()) ? '/'.$lang['code'].'/' : '/';
			}
		}

	}
}

$Navigator = new Navigator();

?>
<section id="lab-navigator" class="lab-panel lab-navigator lab-mockup-grid">

	@style('lab-navigator.scss')
	@style('lab-navigator-print.scss')

	<header>
		<a class="close" href="javascript:void(0)" onclick="lab_panelToggle(this)">×</a>
	</header>

	<ul class="wordpress">

		@if ($Navigator->web_items)

			<li class="header all-children">
				<span class="label">{{ $Navigator->web_label }}</span>
				<a class="tree" onclick="labNav_treeToggle(this)"></a>
			</li>

			@foreach ($Navigator->web_items as $item)
				@component('lab.navigator.item',['item' => $item],TRUE)
			@endforeach

			<li class="divider"></li>

			@if ($Navigator->web_languages)
			<li class="admin">
				<span class="label">{{ $Navigator->web_admin_language }}
				@foreach ($Navigator->web_languages as $label => $url)
					/ <a class="link" href="{{ $url }}">{{ $label }}</a>
				@endforeach
				</span>
			</li>
			@endif

			<li class="admin">
				<span class="label">{{ $Navigator->web_admin_new }}
					<a class="link" href="/wp/wp-admin/post-new.php?post_type=page" target="_blank">{{ $Navigator->web_admin_page }}</a>
					/
					<a class="link" href="/wp/wp-admin/post-new.php?post_type=post" target="_blank">{{ $Navigator->web_admin_post }}</a>
				 </span>
			</li>

			<li class="admin">
				<span class="label">{{ $Navigator->web_admin_goto }}
					<a class="link" href="/wp/wp-admin/" target="_blank">{{ $Navigator->web_admin_dash }}</a>
					/
					<a class="link" href="/wp/wp-admin/edit.php?post_type=page" target="_blank">{{ $Navigator->web_admin_pages }}</a>
					/
					<a class="link" href="/wp/wp-admin/edit.php?post_type=post" target="_blank">{{ $Navigator->web_admin_posts }}</a>
				</span>
			</li>

		@endif

	</ul>

	@script('lab-navigator.js')

</section>
