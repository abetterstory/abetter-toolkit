<?php _debug();

global $post;

$Dev = new Component();

if ($Dev->active = _is_developer() && _not_production() && isset($post->ID)) {

	$Dev->script = (isset($script)) ? "<script>{$script}</script>" : '<link rel="stylesheet" href="/build/css/Development.min.css" />';
	$Dev->style = (isset($style)) ? "<style>{$style}</style>" : '<script src="/build/js/Development.min.js"></script>';

	$ancestors = array_merge((array)$post->ID,_ancestors(),(array)_id('start'));

	$system = _pages(['category_name' => 'system']);
	$hidden = _pages(['category_name' => 'hidden']);
	$content = _pages(['exclude' => _ids(array_merge($system,$hidden))]);

	if ($content) {
		$Dev->content = new Index(_pages(['id' => _ids($content)]));
		$Dev->content->label = "Table of Contents";
	}

	if ($hidden) {
		$Dev->hidden = new Index(_pages(['id' => _ids($hidden)]));
		$Dev->hidden->label = "Hidden Pages";
	}

	if ($system) {
		$Dev->system = new Index(_pages(['id' => _ids($system)]));
		$Dev->system->label = "System Pages";
	}

	$Dev->edit = new StdClass();
	$Dev->edit->url = "/wp-admin/post.php?action=edit&post=";
	$Dev->edit->post = "/wp-admin/post.php?action=edit&post={$post->ID}";
	$Dev->edit->label = '<i class="fa fa-pencil"></i>';

	$Dev->admin = new StdClass();
	$Dev->admin->label = "Administration";
	$Dev->admin->add = "Add new";
	$Dev->admin->goto = "Go to";
	$Dev->admin->page = "Page";
	$Dev->admin->pages = "Pages";
	$Dev->admin->post = "Post";
	$Dev->admin->posts = "Posts";
	$Dev->admin->dashboard = "Dash";

	// ---

	$Dev->brief = new StdClass();
	$Dev->brief->label = "UX Brief";
	$Dev->brief->link = "/wp-admin/edit.php?post_type=dev_brief";
	$Dev->brief->items = array();
	$Dev->brief->post = ($p = get_posts([
		'post_type' => 'brief',
		'meta_query' => [[
			'key' => 'dev_set_content',
			'value' => '"'.$post->ID.'"', 'compare' => 'LIKE'
		]]
	])) ? reset($p) : NULL;

	$fields = array(
		'dev_set_stakeholder' => "Stakeholder",
		'dev_set_audience' => "Audience",
		'dev_brief_message' => "Message",
		'dev_brief_proof' => "Proof",
		'dev_brief_voice' => "Voice",
		'dev_brief_action' => "Action",
		'dev_brief_notes' => "Notes",
		'dev_brief_outline' => "Outline"
	);

	foreach ($fields AS $field => $label) {
		if (!empty($Dev->brief->items[$field])) continue;
		if ($content = _field($field,$Dev->brief->post)) {
			$item = new StdClass();
			$item->id = $Dev->brief->post->ID;
			$item->label = $label;
			$item->link = "/wp-admin/post.php?action=edit&post={$item->id}";
			$item->content = "";
			if (is_array($content)) {
				foreach ($content AS $p) $item->content .= '<a class="rel" href="/wp-admin/post.php?action=edit&post='._id($p).'" target="_blank">'._title($p).'</a>';
			} else {
				$item->content = $content;
			}
			$Dev->brief->items[$field] = $item;
		}
	}

	// ---

	$Dev->mockup = new StdClass();
	$Dev->mockup->title = _title();
	$Dev->mockup->list = "/wp-admin/edit.php?post_type=dev_mockup";
	$Dev->mockup->add = "/wp-admin/post-new.php?post_type=dev_mockup";
	$Dev->mockup->breadcrumbs = array();
	$Dev->mockup->items = array();
	$Dev->mockup->post = ($p = get_posts([
		'post_type' => 'dev_mockup',
		'meta_query' => [[
			'key' => 'dev_set_content',
			'value' => '"'.$post->ID.'"', 'compare' => 'LIKE'
		]]
	])) ? reset($p) : NULL;
	$Dev->mockup->edit = (isset($Dev->mockup->post->ID)) ? "/wp-admin/post.php?action=edit&post={$Dev->mockup->post->ID}" : NULL;
	$Dev->mockup->blocks = ($f = _field('dev_mockup_blocks',$Dev->mockup->post)) ? $f : array();
	$Dev->mockup->markup = ($f = _field('dev_mockup_markup',$Dev->mockup->post)) ? _mockup($f) : "";

	$Dev->mockup->path = array_reverse(array_merge((array)$post->ID,_ancestors()));

	foreach ($Dev->mockup->path AS $path) {
		$Dev->mockup->breadcrumbs[] = _title($path);
	}

	foreach ($Dev->mockup->blocks AS $row => $block) {
		$styles = $block['dev_mockup_block_style'];
		foreach ($styles AS &$style) $style = ($t = get_term($style, 'dev_mockup')) ? $t->slug : "";
		$item = new StdClass();
		$item->style = implode($styles,' ');
		$item->label = $block['dev_mockup_block_name'];
		$item->content = _mockup($block['dev_mockup_block_content']);
		$Dev->mockup->items[$row] = $item;
	}

	// ---

	$Dev->group = new StdClass();
	$Dev->group->label = "Group";
	$Dev->group->items = array();
	$Dev->group->selected = NULL;
	$Dev->group->terms = get_terms('dev_group',['hide_empty'=>FALSE]);

	foreach ($Dev->group->terms AS $term) {
		$item = new StdClass();
		$item->id = _term_id($term);
		$item->label = _term_name($term);
		$item->selected = (!empty($_COOKIE['_dev-group']) && $_COOKIE['_dev-group'] == $item->id) ? TRUE : FALSE;
		if ($item->selected) $Dev->group->selected = $item->id;
		$Dev->group->items[$item->id] = $item;
	}

	// ---

	$Dev->requirements = new StdClass();
	$Dev->requirements->label = "Requirements";
	$Dev->requirements->list = "/wp-admin/edit.php?post_type=dev_requirement";
	$Dev->requirements->add = "/wp-admin/post-new.php?post_type=dev_requirement";
	$Dev->requirements->items = array();
	$Dev->requirements->selected = NULL;
	$Dev->requirements->posts = _pages(['post_type' => 'dev_requirement']);
	$Dev->requirements->current = ($p = get_posts([
		'post_type' => 'dev_requirement',
		'meta_query' => [[
			'key' => 'dev_set_content',
			'value' => '"'.$post->ID.'"', 'compare' => 'LIKE'
		]]
	])) ? $p : array();

	foreach ($Dev->requirements->posts AS $requirement) {
		$item = new StdClass();
		$item->id = _id($requirement);
		$item->order = _order($requirement);
		$item->parent = _parent($requirement);
		$item->link = "/wp-admin/post.php?action=edit&post={$item->id}";
		$item->label = _title($requirement);
		$item->type = _field('dev_requirement_type',$requirement);
		$item->who = _field_title('dev_requirement_who',$requirement);
		$item->who_id = _id(_field('dev_requirement_who',$requirement));
		$item->is_stakeholder = (($f = _field('dev_requirement_who',$requirement)) && $f->post_type == 'dev_stakeholder') ? TRUE : FALSE;
		$item->feel = _field('dev_requirement_feel',$requirement);
		$item->what = _field('dev_requirement_what',$requirement);
		$item->why = _field('dev_requirement_why',$requirement);
		$item->how = _field('dev_requirement_how',$requirement);
		$item->when = _field('dev_requirement_when',$requirement);
		$item->status = _field('dev_requirement_status',$requirement);
		$item->group = _term_ids(_field('dev_set_group',$requirement));
		$item->stakeholder = _ids(_field('dev_set_stakeholder',$requirement));
		$item->audience = _ids(_field('dev_set_audience',$requirement));
		$item->content = "";
		if ($item->what.$item->why) {
			$item->content .= "As <who>".(($item->is_stakeholder)?"{$item->who} Stakeholder":$item->who)."</who>";
			$item->content .= " I <feel>".(($item->feel)?lcfirst($item->feel):'&lt;feel&gt;')."</feel>";
			$item->content .= " <what>".(($item->what)?lcfirst($item->what):'&lt;what&gt;')."</what>";
			$item->content .= ($item->why) ? ", so <why>".(($item->why)?lcfirst($item->why):'&lt;why&gt;').".</why>" : "<why>...</why>";
		}
		$item->hide = FALSE;
		$item->selected = (!empty($_COOKIE['_dev-requirement']) && $_COOKIE['_dev-requirement'] == $item->id) ? TRUE : FALSE;
		if ($item->selected) $Dev->requirements->selected = $item->id;
		$Dev->requirements->items[$item->id] = $item;
	}

	$Dev->requirements->items_sorted = $Dev->requirements->items;
	usort($Dev->requirements->items_sorted, function($a,$b){ return ($a->when - $b->when); });
	$Dev->requirements->items_sorted = array_reverse($Dev->requirements->items_sorted);

	// ---

	$Dev->stakeholders = new StdClass();
	$Dev->stakeholders->label = "Stakeholder";
	$Dev->stakeholders->list = "/wp-admin/edit.php?post_type=dev_stakeholder";
	$Dev->stakeholders->add = "/wp-admin/post-new.php?post_type=dev_stakeholder";
	$Dev->stakeholders->items = array();
	$Dev->stakeholders->selected = NULL;
	$Dev->stakeholders->posts = _pages(['post_type' => 'dev_stakeholder']);

	foreach ($Dev->stakeholders->posts AS $stakeholder) {
		$item = new StdClass();
		$item->id = _id($stakeholder);
		$item->order = _order($stakeholder);
		$item->parent = _parent($stakeholder);
		$item->link = "/wp-admin/post.php?action=edit&post={$item->id}";
		$item->label = _title($stakeholder);
		$item->name = _field('dev_stakeholder_name',$stakeholder);
		$item->title = _field('dev_stakeholder_title',$stakeholder);
		$item->location = _field('dev_stakeholder_location',$stakeholder);
		$item->contact = _field('dev_stakeholder_contact',$stakeholder);
		$item->description = _field('dev_stakeholder_description',$stakeholder);
		$item->goals = _field('dev_stakeholder_goals',$stakeholder);
		$item->expectations = _field('dev_stakeholder_expectations',$stakeholder);
		$item->group = _term_ids(_field('dev_set_group',$stakeholder));
		$item->audience = _ids(_field('dev_set_audience',$stakeholder));
		$item->relations = _ids(_field('dev_set_content',$stakeholder));
		$item->hide = FALSE;
		$item->selected = (!empty($_COOKIE['_dev-stakeholder']) && $_COOKIE['_dev-stakeholder'] == $item->id) ? TRUE : FALSE;
		if ($item->selected) $Dev->stakeholders->selected = $item->id;
		$Dev->stakeholders->items[$item->id] = $item;
	}

	// ---

	$Dev->audience = new StdClass();
	$Dev->audience->label = "Audience";
	$Dev->audience->list = "/wp-admin/edit.php?post_type=dev_audience";
	$Dev->audience->add = "/wp-admin/post-new.php?post_type=dev_audience";
	$Dev->audience->items = array();
	$Dev->audience->selected = NULL;
	$Dev->audience->posts = _pages(['post_type' => 'dev_audience']);

	foreach ($Dev->audience->posts AS $audience) {
		$item = new StdClass();
		$item->id = _id($audience);
		$item->order = _order($audience);
		$item->parent = _parent($audience);
		$item->link = "/wp-admin/post.php?action=edit&post={$item->id}";
		$item->label = _title($audience);
		$item->alias = _field('dev_audience_alias',$audience);
		$item->description = _field('dev_audience_description',$audience);
		$item->needs = _field('dev_audience_needs',$audience);
		$item->wants = _field('dev_audience_wants',$audience);
		$item->barriers = _field('dev_audience_barriers',$audience);
		$item->context = _field('dev_audience_context',$audience);
		$item->group = _term_ids(_field('dev_set_group',$audience));
		$item->stakeholder = _ids(_field('dev_set_stakeholder',$audience));
		$item->relations = _ids(_field('dev_set_content',$audience));
		$item->hide = FALSE;
		$item->selected = (!empty($_COOKIE['_dev-audience']) && $_COOKIE['_dev-audience'] == $item->id) ? TRUE : FALSE;
		if ($item->selected) $Dev->audience->selected = $item->id;
		$Dev->audience->items[$item->id] = $item;
	}

	// ---

	if ($group = $Dev->group->selected) {
		foreach ($Dev->stakeholders->items AS $item) {
			$item->hide = TRUE;
			if (in_array($group,$item->group)) $item->hide = FALSE;
		}
		foreach ($Dev->audience->items AS $item) {
			$item->hide = TRUE;
			if (in_array($group,$item->group)) $item->hide = FALSE;
		}
		foreach ($Dev->requirements->items AS $item) {
			$item->hide = TRUE;
			if (in_array($group,$item->group)) $item->hide = FALSE;
		}
	}

	if ($stakeholder = $Dev->stakeholders->selected) {
		foreach ($Dev->stakeholders->items AS $item) {
			$item->hide = TRUE;
			if ($item->id == $stakeholder) $item->hide = FALSE;
		}
		foreach ($Dev->audience->items AS $item) {
			$item->hide = TRUE;
			if (in_array($stakeholder,$item->stakeholder)) $item->hide = FALSE;
		}
		foreach ($Dev->requirements->items AS $item) {
			$item->hide = TRUE;
			if ($stakeholder == $item->who_id) $item->hide = FALSE;
			if (in_array($stakeholder,$item->audience)) $item->hide = FALSE;
			if (in_array($stakeholder,$item->stakeholder)) $item->hide = FALSE;
		}
	}

	if ($audience = $Dev->audience->selected) {
		foreach ($Dev->audience->items AS $item) {
			$item->hide = TRUE;
			if ($item->id == $audience) $item->hide = FALSE;
		}
		foreach ($Dev->stakeholders->items AS $item) {
			$item->hide = TRUE;
			if (in_array($audience,$item->audience)) $item->hide = FALSE;
		}
		foreach ($Dev->requirements->items AS $item) {
			$item->hide = TRUE;
			if ($audience == $item->who_id) $item->hide = FALSE;
			if (in_array($audience,$item->audience)) $item->hide = FALSE;
			if (in_array($audience,$item->stakeholder)) $item->hide = FALSE;
		}
	}

}

// ---

?>
<? if ($Dev->active) : ?>
<aside id="development">

	<?=$Dev->style?>

	<div class="toggle body bottom">
		<a href="javascript:void(0)" onclick="$('body').addClass('dev-body');localStorage.setItem('_dev-body',1);"><i class="open fa fa-angle-up"></i></a>
	</div>

	<div class="toggle side left">
		<a href="javascript:void(0)" onclick="$('body').addClass('dev-left');localStorage.setItem('_dev-left',1);"><i class="open fa fa-angle-right"></i></a>
	</div>

	<div class="toggle side right">
		<a href="javascript:void(0)" onclick="$('body').addClass('dev-right');localStorage.setItem('_dev-right',1);"><i class="open fa fa-angle-left"></i></a>
	</div>

	<nav class="panel side left navigation">

		<header>
			<a class="close" href="javascript:void(0)" onclick="$('body').removeClass('dev-left');localStorage.removeItem('_dev-left');">×</a>
		</header>

		<? if (!empty($Dev->content->items)) : ?>
		<ul class="content">
			<li class="header all-children"><?=$Dev->content->label?><a class="tree"></a></li>
			<? foreach ($Dev->content->items AS $item) : ?>
			<li id="item-<?=$item->id?>" class="<?=$item->current?> <?=($item->items)?'has-children':''?>">
				<a class="link" href="<?=$item->url?>"><?=$item->title?></a>
				<a class="edit" href="<?=$Dev->edit->url.$item->id?>" target="_blank"><?=$Dev->edit->label?></a>
				<a class="tree"></a>
				<? if ($item->items) : ?>
				<ul>
					<? foreach ($item->items AS $subitem1) : ?>
					<li id="item-<?=$subitem1->id?>" class="<?=$subitem1->current?> <?=($subitem1->items)?'has-children':''?>">
						<a class="link" href="<?=$subitem1->url?>"><?=$subitem1->title?></a>
						<a class="edit" href="<?=$Dev->edit->url.$subitem1->id?>" target="_blank"><?=$Dev->edit->label?></a>
						<a class="tree"></a>
						<? if ($subitem1->items) : ?>
						<ul>
							<? foreach ($subitem1->items AS $subitem2) : ?>
							<li id="item-<?=$subitem2->id?>" class="<?=$subitem2->current?> <?=($subitem2->items)?'has-children':''?>">
								<a class="link" href="<?=$subitem2->url?>"><?=$subitem2->title?></a>
								<a class="edit" href="<?=$Dev->edit->url.$subitem2->id?>" target="_blank"><?=$Dev->edit->label?></a>
								<a class="tree"></a>
								<? if ($subitem2->items) : ?>
								<ul>
									<? foreach ($subitem2->items AS $subitem3) : ?>
									<li id="item-<?=$subitem3->id?>" class="<?=$subitem3->current?> <?=($subitem3->items)?'has-children':''?>">
										<a class="link" href="<?=$subitem3->url?>"><?=$subitem3->title?></a>
										<a class="edit" href="<?=$Dev->edit->url.$subitem3->id?>" target="_blank"><?=$Dev->edit->label?></a>
										<a class="tree"></a>
										<? if ($subitem3->items) : ?>
										<ul>
											<? foreach ($subitem3->items AS $subitem4) : ?>
											<li id="item-<?=$subitem4->id?>" class="<?=$subitem4->current?> <?=($subitem4->items)?'has-children':''?>">
												<a class="link" href="<?=$subitem4->url?>"><?=$subitem4->title?></a>
												<a class="edit" href="<?=$Dev->edit->url.$subitem4->id?>" target="_blank"><?=$Dev->edit->label?></a>
												<a class="tree"></a>
												<? if ($subitem4->items) : ?>
												<ul>
													<? foreach ($subitem4->items AS $subitem5) : ?>
													<li id="item-<?=$subitem5->id?>" class="<?=$subitem5->current?> <?=($subitem5->items)?'has-children':''?>">
														<a class="link" href="<?=$subitem5->url?>"><?=$subitem5->title?></a>
														<a class="edit" href="<?=$Dev->edit->url.$subitem5->id?>" target="_blank"><?=$Dev->edit->label?></a>
														<a class="tree"></a>
													</li>
													<? endforeach ?>
												</ul>
												<? endif ?>
											</li>
											<? endforeach ?>
										</ul>
										<? endif ?>
									</li>
									<? endforeach ?>
								</ul>
								<? endif ?>
							</li>
							<? endforeach ?>
						</ul>
						<? endif ?>
					</li>
					<? endforeach ?>
				</ul>
				<? endif ?>
			</li>
			<? endforeach ?>
		</ul>
		<? endif ?>

		<? if (!empty($Dev->hidden->items)) : ?>
		<ul class="hidden">
			<li class="header toggle-parent" id="nav-hidden"><?=$Dev->hidden->label?></li>
			<? foreach ($Dev->hidden->items AS $item) : ?>
			<li class="<?=$item->current?> <?=($item->items)?'has-children':''?>">
				<a class="link" href="<?=$item->url?>"><?=$item->title?></a>
				<a class="edit" href="<?=$Dev->edit->url.$item->id?>" target="_blank"><?=$Dev->edit->label?></a>
				<a class="tree"></a>
				<? if ($item->items) : ?>
				<ul>
					<? foreach ($item->items AS $subitem) : ?>
					<li class="<?=$subitem->current?> <?=($subitem->items)?'has-children':''?>">
						<a class="link" href="<?=$subitem->url?>"><?=$subitem->title?></a>
						<a class="edit" href="<?=$Dev->edit->url.$item->id?>" target="_blank"><?=$Dev->edit->label?></a>
						<a class="tree"></a>
					</li>
					<? endforeach ?>
				</ul>
				<? endif ?>
			</li>
			<? endforeach ?>
		</ul>
		<? endif ?>

		<? if (!empty($Dev->system->items)) : ?>
		<ul class="system">
			<li class="header toggle-parent" id="nav-system"><?=$Dev->system->label?></li>
			<? foreach ($Dev->system->items AS $item) : ?>
			<li class="<?=$item->current?> <?=($item->items)?'has-children':''?>">
				<a class="tree"></a>
				<a class="link" href="<?=$item->url?>"><?=$item->title?></a>
				<a class="edit" href="<?=$Dev->edit->url.$item->id?>" target="_blank"><?=$Dev->edit->label?></a>
				<? if ($item->items) : ?>
				<ul>
					<? foreach ($item->items AS $subitem) : ?>
					<li class="<?=$subitem->current?> <?=($subitem->items)?'has-children':''?>">
						<a class="tree"></a>
						<a class="link" href="<?=$subitem->url?>"><?=$subitem->title?></a>
						<a class="edit" href="<?=$Dev->edit->url.$item->id?>" target="_blank"><?=$Dev->edit->label?></a>
					</li>
					<? endforeach ?>
				</ul>
				<? endif ?>
			</li>
			<? endforeach ?>
		</ul>
		<? endif ?>

		<ul class="admin">
			<li class="header toggle-parent" id="nav-admin"><?=$Dev->admin->label?></li>
			<li>
				<span class="link"><?=$Dev->admin->add?> </span>
				<a class="link" href="/wp-admin/post-new.php?post_type=page" target="_blank"><?=$Dev->admin->page?></a>
				<span class="link"> / </span>
				<a class="link" href="/wp-admin/post-new.php?post_type=post" target="_blank"><?=$Dev->admin->post?></a>
			</li>
			<li>
				<span class="link"><?=$Dev->admin->goto?> </span>
				<a class="link" href="/wp-admin/" target="_blank"><?=$Dev->admin->dashboard?></a>
				<span class="link"> / </span>
				<a class="link" href="/wp-admin/edit.php?post_type=page" target="_blank"><?=$Dev->admin->pages?></a>
				<span class="link"> / </span>
				<a class="link" href="/wp-admin/edit.php?post_type=post" target="_blank"><?=$Dev->admin->posts?></a>
			</li>
		</ul>

	</nav>

	<section class="panel body mockup mockup-grid">

		<header>

			<!--<a class="print" href="javascript:void(0)" onclick="$('body').toggleClass('dev-print');window.print();"><i class="fa fa-print"></i></a>-->

			<? if ($Dev->mockup->edit) : ?>
			<a class="mock" href="javascript:void(0)" onclick="$('body').toggleClass('dev-mock');if($('body').hasClass('dev-mock')){localStorage.setItem('_dev-mock',1);}else{localStorage.removeItem('_dev-mock');};"><i class="fa fa-eye"></i></a>
			<a class="edit" href="<?=$Dev->mockup->edit?>" target="_blank"><i class="fa fa-pencil"></i></a>
			<? else : ?>
			<a class="edit" href="<?=$Dev->mockup->list?>" target="_blank"><i class="fa fa-pencil"></i></a>
			<? endif ?>

			<a class="close" href="javascript:void(0)" onclick="$('body').removeClass('dev-body');localStorage.removeItem('_dev-body');">×</a>

			<a class="stakeholder" href="javascript:void(0)" onclick="$('body').toggleClass('dev-stakeholder');if($('body').hasClass('dev-stakeholder')){localStorage.setItem('_dev-stakeholder',1);}else{localStorage.removeItem('_dev-stakeholder');};"><i class="fa fa-user-md"></i></a>

			<a class="audience" href="javascript:void(0)" onclick="$('body').toggleClass('dev-audience');if($('body').hasClass('dev-audience')){localStorage.setItem('_dev-audience',1);}else{localStorage.removeItem('_dev-audience');};"><i class="fa fa-users"></i></a>

			<a class="requirement" href="javascript:void(0)" onclick="$('body').toggleClass('dev-requirement');if($('body').hasClass('dev-requirement')){localStorage.setItem('_dev-requirement',1);}else{localStorage.removeItem('_dev-requirement');};"><i class="fa fa-file-text-o"></i></a>

		</header>

		<filter>
			<row>
				<column class="column-4">
					<label>
						<select name="_dev-group" class="<?=($Dev->group->selected)?'selected':''?>">
							<option value="">Group</option>
							<option value="">---</option>
							<? foreach ($Dev->group->items AS $id => $item) : ?>
							<option value="<?=$id?>" <?=($item->selected)?'selected="selected"':''?>><?=$item->label?></option>
							<? endforeach ?>
						</select>
					</label>
				</column>
				<column class="column-4">
					<label>
						<select name="_dev-stakeholder" class="<?=($Dev->stakeholders->selected)?'selected':''?>">
							<option value="">Stakeholder</option>
							<option value="">---</option>
							<? foreach ($Dev->stakeholders->items AS $id => $item) : ?>
							<option value="<?=$id?>" <?=($item->selected)?'selected="selected"':''?>>
								<? if ($item->parent) : ?>
								<?=$Dev->stakeholders->items[$item->parent]->label?>&nbsp;/&nbsp;
								<? endif ?>
								<?=$item->label?>
							</option>
							<? endforeach ?>
						</select>
					</label>
				</column>
				<column class="column-4">
					<label>
						<select name="_dev-audience" class="<?=($Dev->audience->selected)?'selected':''?>">
							<option value="">Audience</option>
							<option value="">---</option>
							<? foreach ($Dev->audience->items AS $id => $item) : ?>
							<option value="<?=$id?>" <?=($item->selected)?'selected="selected"':''?>><?=$item->label?></option>
							<? endforeach ?>
						</select>
					</label>
				</column>
			</row>
		</filter>

		<modal class="stakeholder">
			<div class="mockup-header">
				<a class="list" href="<?=$Dev->stakeholders->list?>" target="_blank">Stakeholders</a>
				<a class="add" href="<?=$Dev->stakeholders->add?>" target="_blank">+</a>
			</div>
			<items>
				<? foreach ($Dev->stakeholders->items AS $item) : ?>
				<? if ($item->hide) continue; ?>
				<item>
					<small legacy>Stakeholder</small>
					<h2 class="group">
						<a class="red" href="<?=$item->link?>" target="_blank">
							<? if ($item->parent) : ?>
							<span parent><?=$Dev->stakeholders->items[$item->parent]->label?></span>
							<? endif ?>
							<?=$item->label?>
						</a>
					</h2>
					<hr />
					<h4 class="group name">
						<span name><?=$item->name?></span>
						<? if ($item->title) : ?><span title><?=$item->title?></span><? endif ?>
						<? if ($item->location) : ?><span location><?=$item->location?></span><? endif ?>
					</h4>
					<? if ($item->contact) : ?>
					<p class="group contact">
						<span contact><?=$item->contact?></span>
					</p>
					<? endif ?>
					<? if ($item->description) : ?>
					<hr space />
					<p class="group description">
						<span description><?=$item->description?></span>
					</p>
					<? endif ?>
					<div class="row expanded">
						<div class="column column-6">
							<hr />
							<p class="group goals">
								<span subtitle>Goals</span>
								<span content><?=$item->goals?></span>
							</p>
						</div>
						<div class="column column-6">
							<hr />
							<p class="group expectations">
								<span subtitle>Expectations</span>
								<span content><?=$item->expectations?></span>
							</p>
						</div>
					</div>
				</item>
				<? endforeach ?>
			</items>
		</modal>

		<modal class="audience">
			<div class="mockup-header">
				<a class="list" href="<?=$Dev->audience->list?>" target="_blank">Audience</a>
				<a class="add" href="<?=$Dev->audience->add?>" target="_blank">+</a>
			</div>
			<items>
				<? foreach ($Dev->audience->items AS $item) : ?>
				<? if ($item->hide) continue; ?>
				<item>
					<small legacy>Audience</small>
					<h2 class="group">
						<? if ($item->parent) : ?>
						<span parent><?=$Dev->audience->items[$item->parent]->label?></span>
						<? endif ?>
						<a class="red" href="<?=$item->link?>" target="_blank"><?=$item->label?></a>
					</h2>
					<? if ($item->alias) : ?>
					<hr />
					<h4 class="group name">
						<span label>A.k.a</span>
						<span alias><?=$item->alias?></span>
					</h4>
					<? endif ?>
					<? if ($item->description) : ?>
					<hr space />
					<p class="group description">
						<span description><?=$item->description?></span>
					</p>
					<? endif ?>
					<div class="row expanded">
						<div class="column column-6">
							<hr />
							<p class="group needs">
								<span subtitle>Needs</span>
								<span content><?=$item->needs?></span>
							</p>
						</div>
						<div class="column column-6">
							<hr />
							<p class="group wants">
								<span subtitle>Wants</span>
								<span content><?=$item->wants?></span>
							</p>
						</div>
					</div>
					<div class="row expanded">
						<div class="column column-6">
							<hr />
							<p class="group barriers">
								<span subtitle>Barriers</span>
								<span content><?=$item->barriers?></span>
							</p>
						</div>
						<div class="column column-6">
							<hr />
							<p class="group context">
								<span subtitle>Context</span>
								<span content><?=$item->context?></span>
							</p>
						</div>
					</div>
				</item>
				<? endforeach ?>
			</items>
		</modal>

		<modal class="requirement">
			<div class="mockup-header">
				<a class="list" href="<?=$Dev->requirements->list?>" target="_blank">Requirements</a>
				<a class="add" href="<?=$Dev->requirements->add?>" target="_blank">+</a>
			</div>
			<items>
				<? foreach ($Dev->requirements->items_sorted AS $item) : ?>
				<? if ($item->hide) continue; ?>
				<item>
					<small legacy><?=($item->type)?"{$item->type}&nbsp;":''?>Requirement</small>
					<small status><?=$item->status?></small>
					<small priority><?=$item->when?></small>
					<h2 class="group">
						<? if ($item->parent) : ?>
						<span parent><?=$Dev->requirements->items[$item->parent]->label?></span>
						<? endif ?>
						<a class="red" href="<?=$item->link?>" target="_blank"><?=$item->label?></a>
						<ul>
							<? foreach ($item->group AS $group) : ?>
							<li><?=$Dev->group->items[$group]->label?></li>
							<? endforeach ?>
						</ul>
					</h2>
					<? if ($item->content) : ?>
					<hr space />
					<p class="group story">
						<span story><?=$item->content?></span>
					</p>
					<? endif ?>
					<row class="row">

						<? if ($item->how) : ?>
						<column class="column <?=($item->audience||$item->stakeholder)?'tiny-6':''?>">
							<hr />
							<p class="group how">
								<span subtitle>Specifications</span>
								<span content><?=$item->how?></span>
							</p>
						</column>
						<? endif ?>

						<? if ($item->audience) : ?>
						<column class="column <?=($item->how)?'':''?>">
							<hr />
							<p class="group audience">
								<span subtitle>Audience</span>
								<? foreach ($item->audience AS $audience) : ?>
								<span item><?=$Dev->audience->items[$audience]->label?></span>
								<? endforeach ?>
							</p>
						</column>
						<? endif ?>

						<? if ($item->stakeholder) : ?>
						<column class="column <?=($item->how)?'':''?>">
							<hr />
							<p class="group stakeholder">
								<span subtitle>Stakeholder</span>
								<? foreach ($item->stakeholder AS $stakeholder) : ?>
								<span item><?=$Dev->stakeholders->items[$stakeholder]->label?></span>
								<? endforeach ?>
							</p>
						</column>
						<? endif ?>

						<? if (!$item->how && !$item->stakeholder && !$item->audience) : ?>
						<column class="column">
							<hr />
						</column>
						<? endif ?>

					</row>
				</item>
				<? endforeach ?>
			</items>
		</modal>

		<mockup>

			<div class="mockup-title">
				<? foreach ($Dev->mockup->breadcrumbs AS $item) : ?>
				<span><?=$item?></span>
				<? endforeach ?>
			</div>

			<blocks>

				<? if ($Dev->mockup->items) : ?>
				<? foreach ($Dev->mockup->items AS $item) : ?>
				<block class="<?=$item->style?>">
					<? if ($item->label) : ?><label><?=$item->label?></label><? endif ?>
					<row><?=$item->content?></row>
				</block>
				<? endforeach ?>
				<? endif ?>

				<?=$Dev->mockup->markup?>

			</blocks>
		</mockup>

	</section>

	<nav class="panel side right inspector">

		<header>
			<!--a class="edit" href="<?=$Dev->edit->post?>" target="_blank"><i class="fa fa-pencil"></i></a-->
			<a class="close" href="javascript:void(0)" onclick="$('body').removeClass('dev-right');localStorage.removeItem('_dev-right');">×</a>
		</header>

		<ul class="brief">
			<li>
				<a class="list" href="<?=$Dev->brief->link?>" target="_blank">
					<h5><?=$Dev->brief->label?></h5>
				</a>
			</li>
			<? if ($Dev->brief->items) : ?>
			<? foreach ($Dev->brief->items AS $item) : ?>
			<li>
				<a class="post" href="<?=$item->link?>" target="_blank">
					<h6><?=$item->label?></h6>
					<p><?=$item->content?></p>
				</a>
			</li>
			<? endforeach ?>
			<? endif ?>
		</ul>

		<hr />

		<? if (FALSE) : ?>
		<ul class="requirements">
			<li>
				<a class="list" href="<?=$Dev->requirements->list?>" target="_blank">
					<h5><?=$Dev->requirements->label?></h5>
				</a>
			</li>
			<? if ($Dev->requirements->items) : ?>
			<? foreach ($Dev->requirements->items AS $item) : ?>
			<li>
				<a class="post" href="<?=$item->link?>" target="_blank">
					<h6><?=$item->label?></h6>
					<p><?=$item->content?></p>
				</a>
			</li>
			<? endforeach ?>
			<? endif ?>
		</ul>
		<? endif ?>

	</nav>

	<?=$Dev->script?>

</aside>
<? endif ?>
