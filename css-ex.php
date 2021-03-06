<?php

/**
 * MIT License
 * 
 * Copyright (c) 2012 Mike Cluck
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software 
 * and associated documentation files (the "Software"), to deal in the Software without restriction, 
 * including without limitation the rights to use, copy, modify, merge, publish, distribute, 
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or 
 * substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING 
 * BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, 
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */


/**
 * CssEX
 * Ever get tired of having to write ever expanding CSS selectors? Especially when everything
 * else in software development uses the idea of scoping? This will allow you to write CSS in a more readable
 * fashion then translate it to valid CSS.
 * 
 * Example:
 * (Before)
 * #container {
 * 		color: #F00;
 * 		.subClass {
 * 			color: #00F;
 * 		}
 * 		> p {
 * 			color: #0F0;
 * 			:hover: {
 * 				color: #0FF;
 * 			}
 * 		}
 * }
 * 
 * (After)
 * #container { color: #F00; }
 * #container .subClass { color: #00F; }
 * #container > p { color: #0F0; }
 * #container > p:hover { color: #0FF; }
 */
class CssEX
{
	// The tree of CSS selectors => rules
	private $cssTree;
	
	// Variables inside the CSS code
	private $variables;
	
	// The current selector
	private $selector;
	
	// Regex pattern for detecting whitespace
	private static $whitespace = "/\\s/";
	
	/**
	 * Parses a special CSS file with defined scopes and generates valid CSS
	 * 
	 * @access public
	 * @param string 	Path to the CSS file
	 * @param bool		If true, empty CSS rules will be removed
	 */
	public function __construct($filePath, $removeEmpty = TRUE)
	{
		// If the file doesn't exist, warn the programmer that the file doesn't exist
		if (!file_exists($filePath)) {
			trigger_error("Cannot parse '".$filePath."'. File does not exist.", E_USER_WARNING);
			return;
		}
		
		// Initialize the tree of rule and the active selector
		$this->cssTree = array();
		$this->variables = array();
		$this->selector = "";
		
		$cssFile = @fopen($filePath, "r");
		
		while (!feof($cssFile)) {
			$char = fgetc($cssFile);
			
			// Ignore whitespace
			if (strlen($this->selector) == 0 && preg_match(self::$whitespace, $char))
				continue;
			else if ($this->selector == "/*") {
				// Ignore comments
				while ($this->selector !== "*/") {
					$this->selector = substr($this->selector, 1);
					$this->selector .= fgetc($cssFile);
				}
				
				$this->selector = "";
				$char = fgetc($file);
				
				if (preg_match(self::$whitespace, $char))
					continue;
			}
			
			// Once we find a {, begin a new CSS scope
			if ($char == "{") {
				$this->selector = trim($this->selector);
				$this->startScope($cssFile);
				$this->selector = "";
			} else if ($char == ";" && strrpos($this->selector, "$") !== FALSE) {
				$this->setVariable($this->selector);
				$this->selector = "";
			} else {
				$this->selector .= $char;
			}
		}
		
		// Clear out empty rules
		if ($removeEmpty) {
			$keys = array_keys($this->cssTree);
			
			foreach($keys as $key) {
				if (count($this->cssTree[$key]) == 0)
					unset($this->cssTree[$key]);
			}
		}
	}
	
	/**
	 * Gets the set of generated CSS rules
	 * 
	 * @access public
	 * @return array 	Set of CSS rules done in the following format:
	 * 		$selector => {
	 * 			$rule => $value,
	 * 			...
	 * 		}
	 */
	public function getRules()
	{
		return $this->cssTree;
	}
	
	/**
	 * Gets the pure CSS
	 * 
	 * @access public
	 * @param  bool		If true, whitespace will be added to make the CSS more readable
	 * @param  bool		If true, 4 spaces will be put used instead of tabs
	 * @return string 	The generated CSS
	 */
	public function getCSS($addWhiteSpace = TRUE, $spacesForTab = TRUE)
	{
		$css = "";
		$space = ($addWhiteSpace) ? " " : NULL;
		$lineBreak = ($addWhiteSpace) ? "\n" : NULL;
		$tab = ($addWhiteSpace) ? (($spacesForTab) ? "    " : "\t") : NULL;
		
		// Format the CSS
		foreach ($this->cssTree as $selector => $ruleSet) {
			$css .= $selector.$space."{";
			
			foreach ($ruleSet as $rule => $value) {
				$css .= $lineBreak.$tab.$rule.":".$space.$value.";";
			}
			
			
			$css .= $lineBreak."}".$lineBreak;
		}
		
		return $css;
	}
	
	/**
	 * Helper function for setting the value of a CSS variable
	 * 
	 * @access private
	 * @param  string	Expression which sets the value
	 */
	private function setVariable($varString)
	{
		$assignment = explode("=", $varString);
		$assignment[0] = trim($assignment[0]);
		$assignment[1] = trim($assignment[1]);
		
		$this->variables[$assignment[0]] = $assignment[1];
	}
	
	/**
	 * Helper function for generating CSS
	 * 
	 * @access private
	 * @param  File 	The CSS file being parsed
	 */
	private function startScope($file, $pseudoSelector = FALSE)
	{
		// If this is a new selector, add it to the tree
		if (!isset($this->cssTree[$this->selector]))
			$this->cssTree[$this->selector] = array();
		
		$rule = ""; // New CSS rule
		$char = ""; // Current character from the file
		while (!feof($file) && $char != "}") {
			$char = fgetc($file);
			
			// Ignore whitespace
			if (strlen($rule) == 0 && preg_match(self::$whitespace, $char))
				continue;
			else if ($rule == "/*") {
				// Ignore comments
				while ($rule !== "*/") {
					$rule = substr($rule, 1);
					$rule .= fgetc($file);
				}
				
				$rule = "";
				$char = fgetc($file);
				
				if (preg_match(self::$whitespace, $char))
					continue;
			}
			
			switch ($char) {
				// Start up a new scoping
				case "{":
					// Clip off any extra spaces and the additional ':' at the end
					$rule = trim($rule);
					if ($rule[strlen($rule) - 1] == ":")
						$rule = substr($rule, 0, strlen($rule) - 1);
					
					// If it's not a pseudo-selector, add a space
					if ($rule[0] !== ":")
						$this->selector .= " ";
					
					// Add the new scope to the selector
					$this->selector .= $rule;
					$this->startScope($file, ($rule[0] == ":"));
					$rule = "";
					break;
					
				// Save the rule for the current selector
				case ";":
					if (strrpos($rule, "$") !== FALSE) {
						if (strrpos($rule, "=") !== FALSE) {
							$this->setVariable($rule);
							$rule = "";
							break;
						} else {
							$variable = substr($rule, strrpos($rule, "$"), strlen($rule) - strrpos($rule, "$"));
							if (isset($this->variables[$variable])) {
								$rule = str_replace($variable, $this->variables[$variable], $rule);
							}
						}
					}
					
					$rulePair = explode(":", $rule);
					$rule = trim($rulePair[0]);
					$value = trim($rulePair[1]);
					$this->cssTree[$this->selector][$rule] = $value;
					$rule = "";
					break;
					
				default:
					$rule .= $char;
					break;
			}
		}
		
		// Split off the newest scope
		$lastChar = ($pseudoSelector) ? ":" : " ";
		$this->selector = trim(substr($this->selector, 0, strrpos($this->selector, $lastChar)));
		
		// If there's a special end on the selector, split it off
		$endOfSelector = "";
		if(strlen($this->selector) > 0)
			$endOfSelector = $this->selector[strlen($this->selector) - 1];
		if ($endOfSelector == "+" || $endOfSelector == ">")
			$this->selector = substr($this->selector, 0, strlen($this->selector) - 2);
	}
}

?>