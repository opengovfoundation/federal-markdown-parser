<?php

class Structure extends Element{

	public static $structure = array();
	protected $markdown = "";
	public $enum, $header, $text, $level, $label, $children = array();

	protected $scalars = array(
	                 	  	'enum',
	                 	  	'header'
	                    );
	

	public function __construct($label){
		$this->label = $label;
		parent::__construct();

		$this->required('label');
	}

	public function parseSelf(){
		$this->required('xml', 'level');

		$header = $this->xml->header;
		$enum = $this->xml->enum;
		$text = $this->xml->text;

		$this->header($header[0]);
		$this->enum($enum[0]);
		$this->text(trim($text[0]));

		foreach($this->xml->children() as $child){
			$name = $child->getName();
			if(in_array($name, $this->scalars)){
				continue;
			}

			if($child->getName() == 'text'){
				$text = $child->asXML();
				$text = preg_replace('/<\/?quote>/', '"', $text);
				$text = strip_tags($text);
				$this->text(trim($text));
				continue;
			}

			$structure = new Structure($child->getName());
			$structure->parent = $this;
			$structure->level($this->level + 1);
			$structure->simplexml($child);
			$structure->parseSelf();

			// $ret = $structure->enum();
			// if(!isset($ret)){
			// 	$this->debugChildren($child, 0);
			// }

			array_push($this->children, $structure);
		}
	}

	public function toMarkdown(){
		$this->required(array('level', 'label'));

		if(isset($this->enum)){
			$this->markdown .= str_repeat(' ', $this->level() * 2) . "* __" . $this->enum();	
		}

		if(isset($this->header)){
			$this->markdown .= " " . $this->header() . "__\n";
			
			if(isset($this->text) && !preg_match('/^[\s\n]*$/s', $this->text)){
				$this->markdown .= str_repeat(' ', ($this->level() + 1) * 2);
				$this->markdown .= "* " . $this->text() . "\n";	
			}
		}else{
			if(isset($this->enum)){
				$this->markdown .= "__ ";
			}

			if(isset($this->text)){
				$this->markdown .= $this->text() . "\n";	
			}else{
				$this->markdown .= "\n";
			}
		}

		if($this->label() == 'quoted-block'){
			$this->markdown .= str_repeat(' ', ($this->level() + 1) * 2) . "* \"\n";
		}
		
		if(isset($this->children)){
			foreach($this->children as $child){
				$this->markdown .= $child->toMarkdown();
			}
		}
		
		if($this->label() == 'quoted-block'){
			$this->markdown .= str_repeat(' ', ($this->level() + 1) * 2) . "* \"\n";
		}

		return $this->markdown;
	}

	public function enum($enum = null){
		return $this->accessor('enum', $enum);
	}

	public function header($header = null){
		return $this->accessor('header', $header);
	}

	public function text($text = null){
		return $this->accessor('text', $text);
	}

	public function level($level = null){
		return $this->accessor('level', $level);
	}

	public function label($label = null){
		return $this->accessor('label', $label);
	}

	protected function debugChildren($node, $level){
		if($level == 0){
			echo "\n\n------ Children Trace ------\n\n";
		}
		echo "Node Name: " . $node->getName() . "\n";
		
		foreach($node->children() as $child){
			$this->debugChildren($child, $level + 1);
		}

		if($level == 0){
			echo "\n\n----------------------------\n\n";	
		}
		
	}
}
