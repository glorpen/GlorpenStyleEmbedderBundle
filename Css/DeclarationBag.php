<?php

/**
 * This file is part of the GlorpenStyleEmbedderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license GPLv3
 */

namespace Glorpen\StyleEmbedderBundle\Css;

/**
 * @author Arkadiusz Dzięgiel
 */
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
