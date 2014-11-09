<?
class f{
	static function is_array_numeric($arr) {
    	return array_keys($arr) === range(0, count($arr) - 1);
	}
	// function defination to convert array to xml
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
	
	function arr_to_xml($ar, &$xml) {
		App::uses('Array2XML', 'Lib'); //helper functions
		$xml = Array2XML::createXML('root', $ar);
		return $xml->saveXML();
	}
	
	
	static function timeSpan($timestring, $timestamp, &$plus, &$minus){
		$plus = strtotime('+'.$timestring, $timestamp);
		$minus = strtotime('-'.$timestring, $timestamp);
	}
	
	static function xml_to_json($s){
		$s = preg_replace('#<(/?\w+):(\w+)>#', '<$1_$2>', $s);
		$s = simplexml_load_string($s, 'SimpleXMLElement', LIBXML_NOCDATA);
		$json = json_encode($s);
		$d = json_decode($json, true);
		return $d;
	}
	
	static function debug($w){
		ob_start();
		debug($w);
		$c = ob_get_contents();
		ob_end_clean();
		return $c;
	}
	
	static function isAssoc($arr) {
		return array_keys($arr) !== range(0, count($arr) - 1);
	}
	
	static function parseTemplate($s, $v = array()){
		foreach($v as $key => $value)
			${$key} = $value;
		ob_start();
		eval('?> '.html_entity_decode($s));
		$c = ob_get_contents();
		ob_end_clean();
		return $c;
	}
}