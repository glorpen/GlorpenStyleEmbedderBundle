<?php

/**
 * This file is part of the GlorpenStyleEmbedderBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license GPLv3
 */

namespace Glorpen\StyleEmbedderBundle\Services;

use Glorpen\StyleEmbedderBundle\Css\Stylesheet;

use Glorpen\StyleEmbedderBundle\Css\RuleBag;

/**
 * Embeds css into html elements
 * @author Arkadiusz Dzięgiel
 */
class Embedder {
	
	protected $twigRenderer;
	
	public function __construct(\Twig_Environment $env){
		$this->twigRenderer = $env;
	}
	
	/**
	 * Embeds $styles into $html
	 * @param string $styles
	 * @param string $html
	 * @return string html with embedded styles
	 */
	public function embed($styles, $html){
		$s = $this->getStylesheet($styles);
		return $s->apply($html);
	}
	
	/**
	 * Returns stylesheet for given css data
	 * @param string $data css styles
	 * @return \Glorpen\StyleEmbedderBundle\Css\Stylesheet
	 */
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
	
	/**
	 * Renders twig template
	 * @param string $template
	 * @param array $context vars for template
	 * @param string $styleBlock style block name
	 * @param string $htmlBlock html block name
	 * @return string
	 */
	public function render($template, array $context=array(), $styleBlock = 'style', $htmlBlock = 'html'){
		$tpl = $this->twigRenderer->loadTemplate($template);
		
		$styles = $tpl->renderBlock($styleBlock, $context);
		$html = $tpl->renderBlock($htmlBlock, $context);
		
		return $this->embed($styles, $html);
	}
}
