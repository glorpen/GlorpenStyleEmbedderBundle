<?php
namespace Glorpen\StyleEmbedderBundle\Css;

use Symfony\Component\CssSelector\CssSelector;

class RuleBag {
	
	protected $declarations, $selectors;
	
	public function __construct(array $selectors){
		$this->declarations = new DeclarationBag();
		$this->selectors = $selectors;
	}
	
	public function add(\CssRulesetDeclarationToken $declaration){
		$this->declarations->add($declaration);
	}
	
	public function getDeclarations(){
		return $this->declarations;
	}
	
	static private function findMatch($regex, &$rule){
		
		$retRule = $rule;
		$count = 0;
		
		preg_match_all($regex, $rule, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		foreach($matches as $match){
			list($str, $offset) = $match[1];
			$len = strlen($str);
			$spaces = str_repeat(' ', $len);
			$retRule = substr_replace($retRule, $spaces, $offset, $len);
			$count += 1;
		}
		
		$rule = $retRule;
		
		return $count;
	}
	
	const TYPE_A = 100;
	const TYPE_B = 10;
	const TYPE_C = 1;
	
	static public function getRuleSpecifity($rule){
		//https://github.com/keeganstreet/specificity/blob/master/specificity.js
		
		//prepare
		
		// Remove the negation psuedo-class (:not) but leave its argument because specificity is calculated on its argument
		$regex = '/:not\(([^\)]*)\)/';
		$rule = preg_replace($regex, ' $1 ', $rule);
		
		$attributeRegex = '/(\[[^\]]+\])/';
		$idRegex = '/(#[^\s\+>~\.\[:]+)/';
		$classRegex = '/(\.[^\s\+>~\.\[:]+)/';
		$pseudoElementRegex = '/(::[^\s\+>~\.\[:]+|:first-line|:first-letter|:before|:after)/';
		$pseudoClassRegex = '/(:[^\s\+>~\.\[:]+)/';
		$elementRegex = '/([^\s\+>~\.\[:]+)/';
		
		$count = 0;
		//a b c
		$count += self::findMatch($attributeRegex, $rule) * self::TYPE_B;
		$count += self::findMatch($idRegex, $rule) * self::TYPE_A;
		$count += self::findMatch($classRegex, $rule) * self::TYPE_B;
		$count += self::findMatch($pseudoElementRegex, $rule) * self::TYPE_C;
		$count += self::findMatch($pseudoClassRegex, $rule) * self::TYPE_B;
		
		// Remove universal selector and separator characters
		$rule = preg_replace('/[\*\s\+>~]/', ' ', $rule);

		$count += self::findMatch($elementRegex, $rule) * self::TYPE_C;
		
		return $count;
	}

	public function getSelectors(){
		return $this->selectors;
	}
	
	public function getNormalizedSelectors(){
		$ret = array();
		foreach($this->getSelectors() as $selector){
			$ret[] = array(
				CssSelector::toXPath('div.item  > h4 > a'),
				self::getRuleSpecifity($selector)
			);
		}
		return $ret;
	}

}
