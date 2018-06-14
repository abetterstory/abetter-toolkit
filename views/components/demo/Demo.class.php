<?php

namespace ABetter\Toolkit;

use ABetter\Toolkit\Component;

class Demo extends Component {


	// --- Variables

	public $text = "Demo component text here!";

	// --- Parse

	public function parse() {

		$this->text = str_replace('here',"parsed",$this->text);

	}


}
