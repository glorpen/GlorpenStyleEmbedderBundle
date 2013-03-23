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
 * Holds multiple delcarations
 * @author Arkadiusz Dzięgiel
 */
class DeclarationBag {
	
	protected $declarations;
	
	public function __construct(array $declarations = array()){
		$this->declarations = $declarations;
	}
	
	/**
	 * Adds declarations from token
	 * @param \CssRulesetDeclarationToken $declaration
	 */
	public function addToken(\CssRulesetDeclarationToken $declaration){
		$this->declarations[$declaration->Property] = $declaration->Value;
	}
	
	/**
	 * Merges properties from other bag
	 * @param DeclarationBag $bag
	 * @return \Glorpen\StyleEmbedderBundle\Css\DeclarationBag
	 */
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
