<?php

/**
 * This file is part of the GlorpenStyleEmbedderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license GPLv3
 */

namespace Glorpen\StyleEmbedderBundle\Css;

use Symfony\Component\CssSelector\CssSelector;

/**
 * Holds multiple rules with same declarations.
 * @author Arkadiusz Dzięgiel
 */
class RuleBag {
	
	protected $declarations, $rules;
	
	public function __construct(array $selectors){
		$this->declarations = new DeclarationBag();
		$this->rules = array();
		
		foreach($selectors as $selector){
			$this->rules[] = new Rule($selector, $this->declarations);
		}
	}
	
	/**
	 * Adds declaration
	 * @param \CssRulesetDeclarationToken $declaration
	 */
	public function add(\CssRulesetDeclarationToken $declaration){
		$this->declarations->addToken($declaration);
	}
	
	/**
	 * Returns declarations
	 * @return \Glorpen\StyleEmbedderBundle\Css\DeclarationBag
	 */
	public function getDeclarations(){
		return $this->declarations;
	}
	
	/**
	 * Returns rules
	 * @return array of \Glorpen\StyleEmbedderBundle\Css\Rule
	 */
	public function getRules(){
		return $this->rules;
	}

}
