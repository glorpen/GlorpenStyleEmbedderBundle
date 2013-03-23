<?php
namespace Glorpen\StyleEmbedderBundle\Css;

class DeclarationBag {
	
	protected $declarations;
	
	//TODO: rozbijanie border na border-left right itp.., background też
	
	public function __construct(array $declarations = array()){
		$this->declarations = $declarations;
	}
	
	public function add(\CssRulesetDeclarationToken $declaration){
		$this->declarations[] = $declaration;
	}
	
	public function merge(DeclarationBag $bag){
		//TODO
		return $this;
	}
	
}
