<?php

class Structure extends Element{

	public static $structure = array();
	protected $markdown;
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
		$this->text($text[0]);

		foreach($this->xml->children() as $child){
			$name = $child->getName();
			if(in_array($name, $this->scalars)){
				continue;
			}

			$structure = new Structure($child->getName());
			$structure->level($this->level + 1);
			$structure->simplexml($child);
			$structure->parseSelf();

			$ret = $structure->enum();
			if(!isset($ret)){
				continue;
			}

			array_push($this->children, $structure);
		}
	}

	public function toMarkdown(){
		$this->required(array('enum', 'level'));

		$this->markdown = str_repeat(' ', $this->level() * 2) . "* __" . $this->enum();
		if(isset($this->header)){
			$this->markdown .= " " . $this->header() . "__\n";
			$this->markdown .= str_repeat(' ', ($this->level() + 1) * 2);
			if(isset($this->text)){
				$this->markdown .= "* " . $this->text() . "\n";	
			}
		}else{
			$this->markdown .= "__ ";
			$this->markdown .= $this->text() . "\n";
		}
		
		if(isset($this->children)){
			foreach($this->children as $child){
				$this->markdown .= $child->toMarkdown();
			}
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
	
}
