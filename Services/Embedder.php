<?php

namespace Glorpen\StyleEmbedderBundle\Services;

use Glorpen\StyleEmbedderBundle\Css\Stylesheet;

use Glorpen\StyleEmbedderBundle\Css\RuleBag;

class Embedder {
	public function embed($styles, $html){
		$s = $this->getStylesheet($styles);
		return $s->apply($html);
	}
	
	public function getStylesheet($data){
		$parser = new \CssMin();
		
		$stylesheet = new Stylesheet();
		$currentSelector = null;
		
		$tokens = $parser->parse($data);
		foreach($tokens as $token){
			if($token instanceof \CssRulesetStartToken){
				$currentSelector = new RuleBag($token->Selectors);
			} else
			if($token instanceof \CssRulesetEndToken){
				$stylesheet->add($currentSelector);
			} else
			if($token instanceof \CssRulesetDeclarationToken){
				$currentSelector->add($token);
			}
		}
		
		return $stylesheet;
	}
}
