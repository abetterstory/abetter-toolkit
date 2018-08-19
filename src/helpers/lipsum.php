<?php


use \Faker\Factory as Faker;


// Lipsum

function _lipsum($opt=NULL,$opt2=NULL) {

	$faker = Faker::create();

	if (is_string($opt)) {
		$input = $opt;
		$opts = explode(':',$input);
		$opt = ['type' => $opts[0]];
		if (preg_match('/\:(real)/',$input)) $opt['format'] = 'real';
		if (preg_match('/\:(p|a|li|span|h1|h2|h3|h4|h5|h6)/',$input,$match)) $opt['tag'] = $match[1];
		if (preg_match('/\:([0-9]+)/',$input,$match)) $opt['repeat'] = $match[1];
	} else if (is_numeric($opt)) {
		$input = (int) $opt;
		$opt = ['min' => $input - ($input / 10), 'max' => $input + ($input / 10)];
	}

	if (is_string($opt2)) {
		$opt['prefix'] = $opt2;
	} else if (is_array($opt2)) {
		$opt = array_replace($opt,$opt2);
	}

	// ---

	$opt = array_replace([
		'type' => 'normal',
		'format' => 'lipsum',
		'repeat' => NULL,
		'min' => NULL,
		'max' => NULL,
		'tag' => NULL,
		'attr' => NULL,
		'prefix' => NULL,
		'return' => "",
	],(array)$opt);

	// ---

	if ($opt['type'] == 'body') {
		$opt['repeat'] = (!empty($opt['repeat'])) ? $opt['repeat'] : 5;
		$opt['tag'] = (!empty($opt['tag'])) ? $opt['tag'] : 'p';
		$opt['min'] = (!empty($opt['min'])) ? $opt['min'] : 300;
		$opt['max'] = (!empty($opt['max'])) ? $opt['max'] : 600;
	}

	if (empty($opt['repeat'])) $opt['repeat'] = 1;
	if (empty($opt['attr'])) $opt['attr'] = 'lipsum';

	// ---

	$i = 0; while ($i < $opt['repeat']) { $i++;

		$line = ($opt['prefix']) ? "{$opt['prefix']} " : "";

		switch ($opt['type']) {

			case 'word' : {
				$line .= ucfirst($faker->word());
				$opt['return'] .= ($opt['tag']) ? "<{$opt['tag']} {$opt['attr']}>$line</{$opt['tag']}>" : $line;
				break;
			}

			case 'name' : {
				$line .= $faker->name();
				$opt['return'] .= ($opt['tag']) ? "<{$opt['tag']} {$opt['attr']}>$line</{$opt['tag']}>" : $line;
				break;
			}

			case 'label' : case 'link' : {
				$length = _lipsumRandom(20,25,$opt);
				$fake = _lipsumClean(($opt['format'] == 'lipsum') ? $faker->text($length) : $faker->realText($length), TRUE);
				$line .= ($opt['prefix']) ? lcfirst($fake) : $fake;
				$opt['return'] .= ($opt['tag']) ? "<{$opt['tag']} {$opt['attr']}>$line</{$opt['tag']}>" : $line;
				break;
			}

			case 'headline' : {
				$length = _lipsumRandom(75,100,$opt);
				$fake = _lipsumClean(($opt['format'] == 'lipsum') ? $faker->text($length) : $faker->realText($length), TRUE);
				$line .= ($opt['prefix']) ? lcfirst($fake) : $fake;
				$opt['return'] .= ($opt['tag']) ? "<{$opt['tag']} {$opt['attr']}>$line</{$opt['tag']}>" : $line;
				break;
			}

			case 'tiny' : case 'line' : {
				$length = _lipsumRandom(20,25,$opt);
				$fake = _lipsumClean(($opt['format'] == 'lipsum') ? $faker->text($length) : $faker->realText($length));
				$line .= ($opt['prefix']) ? lcfirst($fake) : $fake;
				$opt['return'] .= ($opt['tag']) ? "<{$opt['tag']} {$opt['attr']}>$line</{$opt['tag']}>" : $line;
				break;
			}

			case 'short' : {
				$length = _lipsumRandom(30,40,$opt);
				$fake = _lipsumClean(($opt['format'] == 'lipsum') ? $faker->text($length) : $faker->realText($length));
				$line .= ($opt['prefix']) ? lcfirst($fake) : $fake;
				$opt['return'] .= ($opt['tag']) ? "<{$opt['tag']} {$opt['attr']}>$line</{$opt['tag']}>" : $line;
				break;
			}

			case 'lead' : case 'medium' : {
				$length = _lipsumRandom(250,300,$opt);
				$fake = _lipsumClean(($opt['format'] == 'lipsum') ? $faker->text($length) : $faker->realText($length));
				$line .= ($opt['prefix']) ? lcfirst($fake) : $fake;
				$opt['return'] .= ($opt['tag']) ? "<{$opt['tag']} {$opt['attr']}>$line</{$opt['tag']}>" : $line;
				break;
			}

			case 'long' : {
				$length = _lipsumRandom(500,600,$opt);
				$fake = _lipsumClean(($opt['format'] == 'lipsum') ? $faker->text($length) : $faker->realText($length));
				$line .= ($opt['prefix']) ? lcfirst($fake) : $fake;
				$opt['return'] .= ($opt['tag']) ? "<{$opt['tag']} {$opt['attr']}>$line</{$opt['tag']}>" : $line;
				break;
			}

			case 'extra' : {
				$length = _lipsumRandom(800,900,$opt);
				$fake = _lipsumClean(($opt['format'] == 'lipsum') ? $faker->text($length) : $faker->realText($length));
				$line .= ($opt['prefix']) ? lcfirst($fake) : $fake;
				$opt['return'] .= ($opt['tag']) ? "<{$opt['tag']} {$opt['attr']}>$line</{$opt['tag']}>" : $line;
				break;
			}

			case 'normal' : default : {
				$length = _lipsumRandom(300,400,$opt);
				$fake = _lipsumClean(($opt['format'] == 'lipsum') ? $faker->text($length) : $faker->realText($length));
				$line .= ($opt['prefix']) ? lcfirst($fake) : $fake;
				$opt['return'] .= ($opt['tag']) ? "<{$opt['tag']} {$opt['attr']}>$line</{$opt['tag']}>" : $line;
			}

		}

	}

	// ---

	return (string) $opt['return'];

}

function _lipsumClean($string,$extra=FALSE) {
	$string = preg_replace('/[^A-Za-z0-9\.\, ]/', '', $string);
	if ($extra) {
		$clean = preg_replace('/\.|\,/', '', $string);
		$clean = ucfirst(strtolower($clean));
	} else {
		$clean = "";
		$lines = explode('. ',$string);
		foreach ($lines AS $line) $clean .= ucfirst(strtolower(trim($line))).'. ';
		$clean = preg_replace('/\.+/', '.', $clean);
	}
	return trim($clean);
}

function _lipsumRandom($min,$max,&$opt) {
	$min = (!empty($opt['min'])) ? $opt['min'] : $min;
	$max = (!empty($opt['max'])) ? $opt['max'] : $max;
	return rand($min,$max);
}


// Pixsum

function _pixsum($opt=NULL,$opt2=NULL) {

	if (is_string($opt)) {
		$input = $opt;
		$opts = explode(':',$input);
		$opt = ['type' => $opts[0]];
		if (isset($opts[1])) {
			if ($opt['type'] == 'photo') $opt['category'] = $opts[1];
			if ($opt['type'] == 'icon') $opt['icon'] = $opts[1];
		}
		if (isset($opts[2])) {
			if ($opt['type'] == 'photo') $opt['filter'] = $opts[2];
			if ($opt['type'] == 'icon') $opt['background'] = $opts[2];
		}
		if (isset($opts[3])) {
			if ($opt['type'] == 'icon') $opt['color'] = $opts[3];
		}
		if (preg_match('/\:(img)/',$input)) $opt['tag'] = 'img';
	}

	if (is_string($opt2)) {
		$opt['text'] = $opt2;
	} else if (is_array($opt2)) {
		$opt = array_replace($opt,$opt2);
	}

	// ---

	$opt = array_replace([
		'type' => 'placeholder',
		'width' => 1024,
		'height' => 512,
		'background' => '555555',
		'color' => '666666',
		'format' => NULL,
		'x' => NULL,
		'icon' => NULL,
		'tag' => NULL,
		'category' => NULL,
		'filter' => NULL,
		'grayscale' => NULL,
		'blur' => NULL,
		'text' => NULL,
		'index' => NULL,
		'return' => "",
	],(array)$opt);

	// ---

	global $_pixsum_i; $_pixsum_i = (isset($_pixsum_i)) ? $_pixsum_i : 0; $_pixsum_i++;

	if (!empty($opt['x'])) list($opt['width'],$opt['height']) = explode('x',$opt['x']);
	if (!empty($opt['icon'])) $opt['type'] = 'icon';
	if (!empty($opt['category'])) $opt['type'] = 'photo';

	if ($opt['format'] == 'jpg') $opt['type'] = 'placeholder';
	if ($opt['filter'] == 'grayscale') $opt['grayscale'] = TRUE;
	if ($opt['filter'] == 'blur') $opt['blur'] = TRUE;

	$opt['background'] = trim($opt['background'],'#');
	$opt['color'] = trim($opt['color'],'#');

	$opt['x'] = "{$opt['width']}x{$opt['height']}";

	$opt['index'] = "?index={$_pixsum_i}";

	// ---

	switch ($opt['type']) {
		case 'icon' : {
			$opt['icon'] = (empty($opt['icon'])) ? 'fa-image' : $opt['icon'];
			$opt['return'] = "https://imgplaceholder.com/{$opt['x']}/{$opt['background']}/{$opt['color']}/{$opt['icon']}";
			break;
		}
		case 'photo' : {
			$opt['category'] = (empty($opt['category'])) ? 'any' : $opt['category'];
			$opt['grayscale'] = (empty($opt['grayscale'])) ? '' : '/grayscale';
			$opt['return'] = "https://placeimg.com/{$opt['width']}/{$opt['height']}/{$opt['category']}".$opt['grayscale'];
			break;
		}
		case 'wireframe' : {
			$opt['index'] = "";
			$opt['return'] = "data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiIHZpZXdCb3g9IjAgMCAxMDAwIDEwMDAiPjxkZWZzPjxzdHlsZT4uY2xzLTF7ZmlsbDpub25lO3N0cm9rZTojOTk5O3N0cm9rZS13aWR0aDo1cHg7fTwvc3R5bGU+PC9kZWZzPjxsaW5lIGNsYXNzPSJjbHMtMSIgeDI9IjEwMDAiIHkyPSIxMDAwIi8+PGxpbmUgY2xhc3M9ImNscy0xIiB4MT0iMTAwMCIgeTI9IjEwMDAiLz48L3N2Zz4=";
			break;
		}
		default : {
			$opt['format'] = (empty($opt['format'])) ? 'jpg' : $opt['format'];
			$opt['return'] = "https://via.placeholder.com/{$opt['x']}/{$opt['background']}/{$opt['color']}.{$opt['format']}";
		}
	}

	$opt['return'] .= $opt['index'];

	// ---

	if ($opt['tag']) $opt['return'] = "<img src=\"{$opt['return']}\" />";

	// ---

	return (string) $opt['return'];

}
