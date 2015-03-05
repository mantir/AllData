<?php
/**
 * String helper functions for model, view and controller
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Lib
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
class s{
	
	static function clearString($s){
		return html_entity_decode($s, ENT_COMPAT, 'UTF-8');
	}
	
	/**
	*
	*/
	static function startsWith($haystack, $needle){
		if(!$needle && $haystack) 
			return false;
		return !strncmp($haystack, $needle, strlen($needle));
	}
	
	/**
	*
	*/
	static function endsWith($haystack, $needle) {
		if(!$needle && $haystack) 
			return false;
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
		return (substr($haystack, -$length) === $needle);
	}
	
	static function toLower($s){
		return mb_strtolower($s, 'UTF-8');
	}
	
	static function equal($s1, $s2){
		return s::toLower(trim($s1)) == s::toLower(trim($s2));
	}
	
	static function strlen($s){
		return mb_strlen($s, 'UTF-8');
	}
	
	static function str_replace($search, $replace, $subject, &$count = 0) {
		if (!is_array($subject)) {
			// Normalize $search and $replace so they are both arrays of the same length
			$searches = is_array($search) ? array_values($search) : array($search);
			$replacements = is_array($replace) ? array_values($replace) : array($replace);
			$replacements = array_pad($replacements, count($searches), '');
		 
			foreach ($searches as $key => $search) {
				$parts = mb_split(preg_quote($search), $subject);
				$count += count($parts) - 1;
				$subject = implode($replacements[$key], $parts);
			}
		} else {
		// Call s::str_replace for each subject in array, recursively
			foreach ($subject as $key => $value) {
				$subject[$key] = s::str_replace($search, $replace, $value, $count);
			}
		}
		 
		return $subject;
	}
	
	static function escapeAmp($s){
		return s::str_replace('&', '&amp;', $s);
	}
	
	static function finds($haystack, $needle, $offset = 0){
		return strpos($haystack, $needle, $offset) !== false;
	}
	
	function loadXml($string){
		return simplexml_load_string(preg_replace('/&([^#])/', '&#038;$1', $string));
	}
}

?>