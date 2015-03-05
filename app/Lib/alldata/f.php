<?php
/**
 * Helper functions for model, view and controller
 *
 * @copyright     Martin Kapp 2014-15
 * @link          http://headkino.de
 * @package       app.Lib
 * @since         v 0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class f{
	static function is_array_numeric($arr) {
    	return array_keys($arr) === range(0, count($arr) - 1);
	}
	/** 
	* function definition to convert array to xml, purpose is for later XML export
	* 
	*/
	static function array_to_xml($array, &$xml, $top = 1, $pluralize = 1) {
		if($top == 1) {
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><root></root>');
			$top = 'Element';
		}
		foreach($array as $key => $value) {
			if(is_array($value)) {
				if(is_numeric($key)){
					if(f::is_array_numeric($value) && $pluralize) {
						$subnode = $xml->addChild(Inflector::pluralize($top));
					} else {
						$subnode = $xml->addChild($top);
					}
					f::array_to_xml($value, $subnode, $top, $pluralize);
				}
				else {
					if(f::is_array_numeric($value) && $pluralize) {
						$subnode = $xml->addChild(Inflector::pluralize($key));
					} else {
						$subnode = $xml->addChild($key);
					}
					f::array_to_xml($value, $subnode, $key, $pluralize);
				}
			} else {
				if(is_numeric($key))
					$xml->{$top} = s::escapeAmp($value);
				else {
					$xml->{$key} = s::escapeAmp($value);
				}
			}
		}
		return $xml->asXml();
	}
	
	/**
	* Wrapper for array_to_xml
	*/
	function arr_to_xml($ar, &$xml) {
		App::uses('Array2XML', 'Lib'); //helper functions
		$xml = Array2XML::createXML('root', $ar);
		return $xml->saveXML();
	}
	
	/**
	* Not used, can be used to convert an array to CSV
	*/
	function array_to_csv(array &$array)
	{
	   if (count($array) == 0) {
		 return null;
	   }
	   ob_start();
	   $df = fopen("php://output", 'w');
	   //fputcsv($df, array_keys(reset($array)));
	   foreach ($array as $row) {
		  fputcsv($df, $row);
	   }
	   fclose($df);
	   return ob_get_clean();
	}
	
	/**
	* Converts an XML string into JSON format
	*/
	static function xml_to_json($s){
		$s = preg_replace('#<(/?\w+):(\w+)>#', '<$1_$2>', $s);
		$s = simplexml_load_string($s, 'SimpleXMLElement', LIBXML_NOCDATA);
		$json = json_encode($s);
		$d = json_decode($json, true);
		return $d;
	}
	
	/**
	* Returns a good human readable format a variable
	* @param $w Variable
	* @return string
	*/
	static function debug($w){
		ob_start();
		debug($w);
		$c = ob_get_contents();
		ob_end_clean();
		return $c;
	}
	
	/**
	* Check if an array is associative
	* @return bool
	*/
	static function isAssoc($arr) {
		return array_keys($arr) !== range(0, count($arr) - 1);
	}
	
	/**
	* Converts a URL to an absolute URL
	* @param string $absolute Absolute Url e.g. http://google.de/test/nothing
	* @param string $relative Relative URL e.g. ../../search
	* @param return combination of absolute and relative e.g. http://google.de/search
	*/
	static  function url_to_absolute($absolute, $relative) {
        $p = parse_url($relative);
        if($p["scheme"]) return $relative;
        
        extract(parse_url($absolute));
        
        $path = dirname($path); 
    
        if($relative{0} == '/') {
            $cparts = array_filter(explode("/", $relative));
        }
        else {
            $aparts = array_filter(explode("/", $path));
            $rparts = array_filter(explode("/", $relative));
            $cparts = array_merge($aparts, $rparts);
            foreach($cparts as $i => $part) {
                if($part == '.') {
                    $cparts[$i] = null;
                }
                if($part == '..') {
                    $cparts[$i - 1] = null;
                    $cparts[$i] = null;
                }
            }
            $cparts = array_filter($cparts);
        }
        $path = implode("/", $cparts);
        $url = "";
        if($scheme) {
            $url = "$scheme://";
        }
        if($user) {
            $url .= "$user";
            if($pass) {
                $url .= ":$pass";
            }
            $url .= "@";
        }
        if($host) {
            $url .= "$host/";
        }
        $url .= $path;
        return $url;
    }
}