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
