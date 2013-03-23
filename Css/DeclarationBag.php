<?php
namespace Glorpen\StyleEmbedderBundle\Css;

class DeclarationBag {
	
	protected $declarations;
	
	public function __construct(array $declarations = array()){
		$this->declarations = $declarations;
	}
	
	public function addToken(\CssRulesetDeclarationToken $declaration){
		$this->declarations[$declaration->Property] = $declaration->Value;
	}
	
	public function merge(DeclarationBag $bag){
		foreach($bag->declarations as $property=>$value){
			$this->declarations[$property] = $value;
			//TODO: rozbijanie border na border-left right itp.., background też
		}
		return $this;
	}
	
	public function __toString(){
		$ret='';
		foreach($this->declarations as $k=>$v){
			$ret .= $k.':'.$v.';';
		}
		return $ret;
	}
	
}
