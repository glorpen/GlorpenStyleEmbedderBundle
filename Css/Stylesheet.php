<?php
namespace Glorpen\StyleEmbedderBundle\Css;

use Symfony\Component\CssSelector\CssSelector;

class Stylesheet {
	
	protected $rules, $linearized;
	
	public function __construct(){
		$this->rules = array();
	}
	
	public function add(RuleBag $rule){
		$this->rules[] = $rule;
	}
	
	public function linearize(){
		
		if($this->linearized !== null) return $this->linearized;
		
		$ret = array();
		
		foreach($this->rules as $rule){
			foreach($rule->getNormalizedSelectors() as $item){
				list($xpath, $specifity) = $item;
				if(!array_key_exists($xpath, $ret)){
					$ret[$xpath] = array(
						'specifity' => $specifity,
						'declarations' => new DeclarationBag()
					);
				}
				$ret[$xpath]['declarations']->merge($rule->getDeclarations());
			}
		}
		
		return $this->linearized = $ret;
	}
	
}
