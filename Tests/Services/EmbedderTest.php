<?php

/**
 * This file is part of the GlorpenStyleEmbedderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license GPLv3
 */

namespace Glorpen\StyleEmbedderBundle\Tests\Service;

use Glorpen\StyleEmbedderBundle\Css\Rule;

use Symfony\Component\CssSelector\CssSelector;

use Glorpen\StyleEmbedderBundle\Css\RuleBag;

use Glorpen\StyleEmbedderBundle\Services\Embedder;

/**
 * @author Arkadiusz Dzięgiel
 */
class EmbedderTest extends \PHPUnit_Framework_TestCase {
	
	protected $embedder;
	
	protected function setUp() {
		
		$twig = <<<EOF
   {% block style %}
      .footer * {
         color: silver;
      }
	  .footer p {
		font-weight: bold;
		}
      .footer p > span {
		 font-weight: normal;
      }
      h1 {
         font-size: 20px;
      }
   {% endblock %}
   {% block html %}
   <html>
      <head></head>
      <body>
         <h1>Some Header</h1>
         <div class="footer">
            <p>Address: <span>Our address</span></p>
            <p>Tel.: <span>123-456-789</span></p>
         </div>
      </body>
   </html>
   {% endblock %}
EOF;
		
		$templates=array(
			'template.html.twig' => $twig
		);
		
		$env = new \Twig_Environment(new \Twig_Loader_Array($templates));
		$this->embedder = new Embedder($env);
	}

	
	public function testRuleApplying(){
		$data = <<<EOF
p, .class, #id, #id * { 
	color: red;
}
p.big {
	font-size:20px;
	font-weight: bold;
}
EOF;
		$stylesheet = $this->embedder->getStylesheet($data);
		
		$html = <<<EOF
<html>
		<head></head>
		<body>
			<p>some p</p>
			<div id="id">
				<span data-inside-div>some text</span>
				<p class="big class"></p>
			</div>
		</body>
</html>
EOF;
		
		$ret = $stylesheet->apply($html, false);
		
		$this->assertContains('id="id" style="color:red;"', $ret);
		$this->assertContains('<p style="color:red;">', $ret);
		$this->assertContains('data-inside-div style="color:red;"', $ret);
		$this->assertContains('class="big class" style="color:red;font-size:20px;font-weight:bold;"', $ret);
	}
	
	public function testApplyingWhenSameSpecifity(){
		$data = <<<EOF
p {
	color: red;
}
p {
	color: black;
}
EOF;
		$stylesheet = $this->embedder->getStylesheet($data);
	
		$html = <<<EOF
<html><body><p>some p</p></body></html>
EOF;
	
		$ret = $stylesheet->apply($html);
		$this->assertContains('style="color:black;"', $ret);
	}

	public function testApplyingWhenStyleExists(){
		$data = <<<EOF
p {
	color: red;
}
EOF;
		$stylesheet = $this->embedder->getStylesheet($data);
	
		$html = <<<EOF
<html><body><p style="color:black">some p</p></body></html>
EOF;
	
		$ret = $stylesheet->apply($html);
		$this->assertContains('style="color:red;color:black"', $ret);
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
			$rule = new Rule($d[0]);
			$ret = $rule->getSpecifity();
			$this->assertEquals($d[1], $ret, 'Selector "'.$d[0].'" has specifity '.$d[1]);
		}
		
	}
	
	public function testTwigRendering(){
		$ret = $this->embedder->render('template.html.twig');
		echo $ret;
		$this->assertEquals(4, substr_count($ret, 'color:red;'), 'Twig rendering');
	}
}
