<?php
	/**
	* InStyle
	* Embedded CSS to Inline CSS Converter Class
	* @version 0.2
	* @updated 12/04/2009
	* 
	* @author David Lim
	* @email miliak@orst.edu
	* @link http://davidandjennilyn.com/instyle
	* @acknowledgements Simple HTML DOM
	* @description This class will extract the embedded CSS of a HTML file and apply the styles inline.
	* @requirements: PHP 4.3+
	*
	* InStyle is provided AS-IS.
	*/ 

	class InStyle {

		function convert($document) {
			// Debug mode
			// Debug mode will output selectors and styles that are detected in the embedded CSS
			$debug = false;
	

			// Extract the CSS
			preg_match('/<style[^>]+>(?<css>[^<]+)<\/style>/s', $document, $matches);

			// Strip out extra newlines and tabs from CSS
			$css = preg_replace("/[\n\r\t]+/s", "", $matches['css']);

			// Extract each CSS declaration
			preg_match_all('/([a-zA-Z0-9_ ,#\.]+){([^}]+)}/s', $css, $rules, PREG_SET_ORDER);

			// For each CSS declaration, make the selector and style declaration into an array
			// Array index 1 is the CSS selector
			// Array index 2 is the CSS rule(s)
			foreach ($rules as $rule) {
				// If the CSS selector is multiple, we should split them up
				if (strstr($rule['1'], ',')) {
					// Strip out spaces after a comma for consistency
					$rule['1'] = str_replace(', ', ',', $rule['1']);
					
					// Unset any previous combos
					unset($selectors);

					// Make each selector declaration its own
					// Create a separate array element in styles array for each declaration
					$selectors = explode(',', $rule['1']);
					foreach($selectors as $selector) {
						$styles[trim($selector)] .= trim($rule['2']);
						if ($debug) { echo trim($selector) . ' { ' . trim($rule['2']) . ' }<br/>'; }
					}				
				} else {
					$styles[trim($rule['1'])] .= trim($rule['2']);
					if ($debug) { echo trim($rule['1']) . ' { ' . trim($rule['2']) . ' }<br/>'; }
				}
			}

			// DEBUG: Show selector and declaration
			if ($debug) {
				echo '<pre>';
				foreach ($styles as $selector=>$styling) {
					echo $selector . ':<br>';
					echo $styling . '<br/><br/>';
				}
				echo '</pre><hr/>';
			}

			// Load Simple HTML DOM helper
			require_once('simple_html_dom.php');
			$html_dom = new simple_html_dom();

			// Load in the HTML without the head and style definitions
			$html_dom->load(preg_replace('/\<head\>(.+?)\<\/head>/s', '', $document));

			// For each style declaration, find the selector in the HTML and add the inline CSS
			if (!empty($styles)) {
				foreach ($styles as $selector=>$styling) {
					foreach ($html_dom->find($selector) as $element) {
						// Check to make sure the style doesn't already exist
						if (!stristr($element->style, $styling)) {
							// If there is any existing style, this will append to it
							$element->style .= $styling;
						}
					}
				}
				$inline_css_message = $html_dom->save();
				return $inline_css_message;
			}
			return false;
		}
	}

/* End of file inline_css.php */