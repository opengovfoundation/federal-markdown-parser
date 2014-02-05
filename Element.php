<?php

class Element{

	public $parent;
	public $xml;

	protected function __construct(){

	}

	public function simplexml($xml = null){
		return $this->accessor('xml', $xml);
	}

	protected function required($attributes){
		if(is_array($attributes)){
			foreach($attributes as $attribute){
				if(!isset($this->$attribute)){
					throw new Exception('Attribute ' . $attribute . ' isn\'t set for ' . print_r($this, true) . "\n\n Aborting.");
				}
			}
		}else{
			if(!isset($this->$attributes)){
				throw new Exception('Attribute ' . $attributes . ' isn\'t set for ' . print_r($this, true) . "\n\n Aborting.");
			}
		}
	}

	protected function accessor($attribute, $value = null){
		if(!isset($attribute)){
			throw new Exception('Attribute not set for accessor method.  Aborting');
		}

		if(!isset($value)){
			return $this->$attribute;
		}else{
			$this->$attribute = $value;
		}
	}

	protected function dd()
	{
		array_map(function($x) { var_dump($x); }, func_get_args()); die;
	}
}
