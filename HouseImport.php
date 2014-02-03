<?php

class HouseImport{
	const ROOTTAG = 'legis-body';
	protected $structure = array(
					'section',
					'subsection',
					'paragraph',
					'subparagraph',
					'clause',
					'subclause',
					'item'
				);

	public $originalXML;
	public $md = '';
	protected $simplexml;

	public function __construct($xml){
		$this->originalXML = $xml;
		
		$this->simplexml = simplexml_load_string($xml);
	}

	public function convert(){
		$rootNode = $this->simplexml->xpath(self::ROOTTAG);
		$rootNode = $rootNode[0];

		if(!isset($rootNode)){
			throw new Exception("Unable to get simplexml root node.  Tag: " . self::ROOTTAG);
		}

		$markdown = $this->convertChildren($rootNode, 0);

		$this->md = $markdown;

		return $this->md;
	}

	protected function convertChildren($node, $index){

		$mdString = "";

		$nodes = $node->xpath($this->structure[$index]);
		$quoted = $node->xpath('quoted-block');

		if(count($nodes) == 0){
			$nodes = $node->xpath('quoted-block');
			
			if(count($nodes) == 0){
				return '';
			}else{
				$raw = strip_tags($nodes[0]->asXML());
				$raw = preg_replace('/^/m', "> ", $raw);
				return "\n\n" . $raw . "\n\n";
			}
		}

		foreach($nodes as $node){
			$nodeString = "";

			if($index == 0){
				$nodeString .= "\n";
			}

			$header = $node->header;
			if(isset($header[0]) && $index == 0){
				$header = '__' . $header[0] . '__';
			}
			$enum = $node->enum;
			$text = $node->text;

			$nodeString .= str_repeat(' ', $index * 2) . "* " . str_replace('.', '\.', $enum[0]) . " " . $header;
			
			if(isset($text[0])){

				$attributes = $text->attributes();
				
				if($index <= 2){
					$nodeString .= "\n";
					$nodeString .= str_repeat(' ', ($index + 1) * 2) . "*";
				}

				$textXML = $text->asXML();
				$textXML = preg_replace('/<quote>(\w+)<\/quote>/', '`$1`', $textXML);
				$textXML = preg_replace('/&#x\d+;/', '', $textXML);
				$textString = strip_tags($textXML);
				$nodeString .= " " . $textString . "\n";	
			}else{
				$nodeString .= "\n";
			}
			
			$childrenString = $this->convertChildren($node, $index + 1);

			$nodeString .= $childrenString;

			$mdString .= $nodeString;	
		}

		return $mdString;
	}

	protected function dd()
	{
		array_map(function($x) { var_dump($x); }, func_get_args()); die;
	}

}


