<?php


use \Faker\Factory as Faker;


// Lipsum

if (!function_exists('_lipsum')) {

	function _lipsum($opt=NULL,$opt2=NULL) {

		$faker = Faker::create();

		if (is_numeric($opt)) {
			$input = (int) $opt;
			$opt = ['min' => $input - ($input / 10), 'max' => $input + ($input / 10)];
		} else if (is_string($opt)) {
			$input = $opt;
			$opts = explode(':',$input);
			$opt = ['type' => $opts[0]];
			if (preg_match('/\:(real)/',$input)) $opt['format'] = 'real';
			if (preg_match('/\:(p|a|li|span|h1|h2|h3|h4|h5|h6)/',$input,$match)) $opt['tag'] = $match[1];
			if (preg_match('/\.(lead)/',$input,$match)) $opt['class'] = $match[1];
			if (preg_match('/\:([0-9]+)/',$input,$match)) $opt['repeat'] = $match[1];
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
			'class' => NULL,
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
		if (!empty($opt['class'])) $opt['attr'] .= " class=\"{$opt['class']}\"";

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

}


// Pixsum

if (!function_exists('_pixsum')) {

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
			if ((is_string($input) && preg_match('/\:(img)/',$input)) || (is_string($opt2) && preg_match('/(img)\:/',$opt2))) {
				$opt['tag'] = 'img';
				$opt['imgservice'] = (is_string($opt2)) ? str_replace('img:','',$opt2) : NULL;
			}
		}

		if (is_string($opt2)) {
			$opt['text'] = $opt2;
		} else if (is_array($opt2)) {
			$opt = array_replace($opt,$opt2);
		}

		// ---

		$opt = array_replace([
			'type' => 'placeholder',
			'cache' => TRUE,
			'width' => 1024,
			'height' => 512,
			'background' => '555555',
			'color' => '666666',
			'format' => NULL,
			'x' => NULL,
			'icon' => NULL,
			'tag' => NULL,
			'imgservice' => NULL,
			'category' => NULL,
			'filter' => NULL,
			'grayscale' => NULL,
			'blur' => NULL,
			'text' => NULL,
			'index' => NULL,
			'random' => NULL,
			'remote' => NULL,
			'debug' => NULL,
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

		if ($opt['random']) $opt['index'] = rand(1,(is_numeric($opt['random']))?(int)$opt['random']:25);
		if ($opt['index'] !== FALSE) $opt['index'] = "?index=".(($opt['index']) ? $opt['index'] : $_pixsum_i);

		if ($opt['debug']) clock(['before' => $opt]);

		// ---

		switch ($opt['type']) {
			case 'icon' : {
				$opt['icon'] = (empty($opt['icon'])) ? 'fa-image' : $opt['icon'];
				$opt['return'] = "https://imgplaceholder.com/{$opt['x']}/{$opt['background']}/{$opt['color']}/{$opt['icon']}";
				$opt['remote'] = 'imgplaceholder.com';
				break;
			}
			case 'photo' : {
				$opt['category'] = (empty($opt['category'])) ? 'any' : $opt['category'];
				$opt['grayscale'] = (empty($opt['grayscale'])) ? '' : '/grayscale';
				$opt['return'] = "https://placeimg.com/{$opt['width']}/{$opt['height']}/{$opt['category']}".$opt['grayscale'];
				$opt['remote'] = 'placeimg.com';
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
				$opt['remote'] = 'via.placeholder.com';
			}
		}

		$opt['return'] .= $opt['index'];

		// ---

		if ($opt['remote'] && $opt['cache']) $opt['return'] = _imageCache($opt['return']);

		// ---

		if ($opt['tag'] && $opt['imgservice']) $opt['return'] = "/image/{$opt['imgservice']}".$opt['return'];
		if ($opt['tag']) $opt['return'] = "<img src=\"{$opt['return']}\" />";

		// ---

		if ($opt['debug']) clock(['after' => $opt]);

		return (string) $opt['return'];

	}

}


// Logosum

if (!function_exists('_logosum')) {

	function _logosum($opt=NULL,$opt2=NULL) {

		if (is_string($opt)) {
			$input = $opt;
			$opts = explode(':',$input);
			$opt = ['text' => $opts[0]];
			if (isset($opts[1])) $opt['color'] = $opts[1];
			if (isset($opts[2])) $opt['background'] = $opts[2];
		}

		// ---

		$opt = array_replace([
			'text' => "Logotype",
			'color' => NULL,
			'border' => NULL,
			'background' => NULL,
			'width' => 400,
			'height' => 180,
			'font' => 'monospace',
			'transform' => 'uppercase',
			'weight' => 'bold',
			'size' => 80,
			'spacing' => 20,
			'line' => 20,
			'pad' => 80,
			'return' => "",
		],(array)$opt);

		// ---

		if (empty($opt['color'])) $opt['color'] = '#333';
		if (empty($opt['border'])) $opt['border'] = $opt['color'];
		if (empty($opt['background'])) $opt['background'] = 'transparent';

		$opt['pad'] = $opt['size'];

		if (strlen($opt['text']) > 4) $opt['width'] = strlen($opt['text']) * $opt['pad'] + $opt['pad'];

		$opt['base'] = 1;
		$opt['ratio'] = round($opt['width'] / $opt['height'] * $opt['base'],2);

		// ---

		$template = '<svg data-aspect="[ratio]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 [width] [height]">
		<defs>
			<style>
				rect { fill: [background]; stroke: [border]; stroke-width: [line]px; }
			    text { fill: [color]; font-family: [font]; font-size: [size]px; line-height: 1; font-weight: [weight]; letter-spacing: [spacing]px; text-transform: [transform]; }
			</style>
		</defs>
		<rect width="100%" height="100%" />
		<text x="50%" y="50%" text-anchor="middle" dy=".3em">[text]</text>
		</svg>';

		// ---

		$opt['return'] = str_replace([
			'[width]',
			'[height]',
			'[base]',
			'[ratio]',
			'[background]',
			'[border]',
			'[line]',
			'[color]',
			'[font]',
			'[size]',
			'[weight]',
			'[spacing]',
			'[transform]',
			'[text]',
		],[
			$opt['width'],
			$opt['height'],
			$opt['base'],
			$opt['ratio'],
			$opt['background'],
			$opt['border'],
			$opt['line'],
			$opt['color'],
			$opt['font'],
			$opt['size'],
			$opt['weight'],
			$opt['spacing'],
			$opt['transform'],
			$opt['text'],
		],$template);

		// ---

		return (string) $opt['return'];

	}

}
