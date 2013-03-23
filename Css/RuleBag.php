<?php
namespace Glorpen\StyleEmbedderBundle\Css;

use Symfony\Component\CssSelector\CssSelector;

class RuleBag {
	
	protected $declarations, $rules;
	
	public function __construct(array $selectors){
		$this->declarations = new DeclarationBag();
		$this->rules = array();
		
		foreach($selectors as $selector){
			$this->rules[] = new Rule($selector, $this->declarations);
		}
	}
	
	public function add(\CssRulesetDeclarationToken $declaration){
		$this->declarations->addToken($declaration);
	}
	
	public function getDeclarations(){
		return $this->declarations;
	}
	
	public function getRules(){
		return $this->rules;
	}

}
