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
 * Represents whole css stylesheet, can apply rules to given html
 * @author Arkadiusz Dzięgiel
 */
class Stylesheet {
	
	protected $rules, $linearized;
	
	public function __construct(){
		$this->rules = array();
	}
	
	/**
	 * Adds rule bag
	 * @param RuleBag $rule
	 */
	public function add(RuleBag $rule){
		$this->rules[] = $rule;
	}
	
	/**
	 * Parses given html and applies rules from this stylesheet
	 * @param string $html
	 * @param string $removeAttrs whatever class and id attributes should be removed
	 * @return string html with embedded styles
	 */
	public function apply($html, $removeAttrs=true){
		
		$doc = new \DOMDocument();
		$doc->loadHTML($html);
		
		$xpath = new \DOMXPath($doc);
		
		$ret = array();
		
		foreach($this->rules as $ruleBag){
			foreach($ruleBag->getRules() as $rule){
				/* @var $rule Rule */
				$elements = $xpath->query($rule->getXPath());
				foreach($elements as $el){
					/* @var $el \DOMElement */
					$nodePath = $el->getNodePath();
					if(!array_key_exists($nodePath, $ret)){
						$ret[$nodePath] = array();
					}
					$ret[$nodePath][] = array('specifity'=>$rule->getSpecifity(), 'declarations'=>$rule->getDeclarations());
				}
			}
		}
		
		$compareSpecifity = function($a, $b){
			return $a['specifity'] == $b['specifity']?1:$a['specifity'] > $b['specifity'];
		};
		
		foreach($ret as $nodePath => $items){
			usort($items, $compareSpecifity);
			
			$style = new DeclarationBag();
			foreach($items as $item){
				$style->merge($item['declarations']);
			}
			
			$node = $xpath->query($nodePath)->item(0);
			//prepend if style attr exists
			if($node->hasAttribute("style")){
				$newStyle = ((string) $style).$node->getAttribute("style");
			} else {
				$newStyle = ((string) $style);
			}
			
			$node->setAttribute("style", $newStyle);
			
			if($removeAttrs){
				$node->removeAttribute("class");
				$node->removeAttribute("id");
			}
		}
		
		return $doc->saveHTML();
	}
	
}
