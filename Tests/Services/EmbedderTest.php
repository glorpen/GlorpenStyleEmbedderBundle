<?php

namespace Glorpen\StyleEmbedderBundle\Tests\Service;

use Symfony\Component\CssSelector\CssSelector;

use Glorpen\StyleEmbedderBundle\Css\RuleBag;

use Glorpen\StyleEmbedderBundle\Services\Embedder;

class EmbedderTest extends \PHPUnit_Framework_TestCase {
	
	protected $embedder;
	
	protected function setUp() {
		$this->embedder = new Embedder();
	}

	
	public function testSimpleRuleExtracting(){
		$data = <<<EOF
	 .intro, #someId2, table , a 
				b d	, a
				>
				b+c[d~=e]{ 
		-moz: asd;
		background-image: url("asd{}//");
}
EOF;
		var_dump($this->embedder->tokenize($data));
	}
	
	public function testRulesSpecifity(){
		$data = array(
			// http://css-tricks.com/specifics-on-css-specificity/
			array('ul#nav li.active a', 113),
			array('body.ie7 .col_3 h2 ~ h2', 23),
			array('#footer *:not(nav) li', 102),
			array('ul > li ul li ol li:first-letter', 7),
				
			// http://reference.sitepoint.com/css/specificity
			array('body#home div#warning p.message', 213),
			array('* body#home>div#warning p.message', 213),
			array('#home #warning p.message', 211),
			array('#warning p.message', 111),
			array('#warning p', 101),
			array('p.message', 11),
			array('p', 1)
		);
		
		foreach($data as $d){
			$ret = RuleBag::getRuleSpecifity($d[0]);
			$this->assertEquals($d[1], $ret, 'Selector "'.$d[0].'" has specifity '.$d[1]);
		}
		
	}
}
