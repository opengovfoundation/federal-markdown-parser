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
		$nodeList = $node->xpath('section');
		$mdString = '';

		foreach($nodeList as $nodes){
			$section = new Structure('section');
			$section->level(0);

			$section->simplexml($nodes);
			$section->parseSelf();
			$mdString .= $section->toMarkdown();
		}

		return $mdString;
	}

	protected function dd()
	{
		array_map(function($x) { var_dump($x); }, func_get_args()); die;
	}

	protected function count_beg_chars($string, $char){
		$i = 0;
		echo "Starts with |" . $string{$i} . "|\n";

		while($string{$i} == $char){
			echo "|" .$string{$i} . "| ?= |" . $char . "|\n";
			$i++;
		}

		return $i;
	}

}


