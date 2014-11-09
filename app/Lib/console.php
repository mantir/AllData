<?
class console{
	static $systemName = 'AllData';
	static $serviceEmail = 'info@headkino.de';
	static $singleReport = array('Gesammte Sendung', 'Einzelbeitrag');
	static $timeFSK = array(20 => 12, 22 => 16, 23 => 18);
	static $weekdays = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');
	static $weekdaysShort = array('Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So');
	static $logTypes = array('fetch' => 'Import EPG', 'matchProgram' => 'Programm-Matching', 'fetchProgram' => 'Programmimport', 'matchVideos' => 'Mediathek Matching', 'matchImages' => 'Image Matching', '_error' => 'Fehler');
	static $unit_prefixes = array('p' => 'Piko', 'n' => 'Nano', 'µ' => 'Mikro', 'm' => 'Milli', 'c' => 'Zenti', 'd' => 'Dezi', 'h' => 'Hekto', 'k' => 'Kilo', 'M' => 'Mega', 'G' => 'Giga', 'T' => 'Tera');
	
	static $htmlInput = array('div' => array('class' => 'form-group'), 'class' => 'form-control');
	
	static $startTime = '05:30'; //Konstante Startzeit für einen Sendetag
	
	static function ipgToEpg($id){
		return substr($id, 5);
	}
	
	static function durationToSec($d){
		$addHour = '00:';
		if(substr_count($d, ':') == 2) $addHour = ''; 
		if($addHour && intval($d[0]) > 5) {
			$e = explode(':', $d);
			$f = intval($e[0]);
			if($f > 59) {
				$addHour = '0'.floor($f / 60).':';
				$d = ($f % 60).':'.$e[1];
			}
		}
		return strtotime($addHour.preg_replace('/[^:0-9]/', '', $d).' UTC', 0);
	}
	static function secToDuration($s){
		if(!$s) $s = 0;
		return date('i:s', $s).' min';
	}
	
	static function debug($d){
		echo '<pre>';
		print_r($d);
		echo '</pre>';
	}
	
	static function bug($w){
		ob_start();
		debug($w);
		$c = ob_get_contents();
		ob_end_clean();
		return $c;
	}
	
	//returns ob ein Video ein Einzelbeitrag zu einer Sendung ist
	static function editOrAdd($url){
		if(strpos($url, 'edit')) return 'bearbeiten'; else return 'anlegen';
	}
	
	/*fetch_page nur mit codierung in utf8. Verwendet beim Parsen von Seiten wo diesbezüglich Fehler auftraten*/
	static function mb_fetch_page($method, $url, $p = array(), $header = array()){
		return utf8_encode(console::fetch_page($method, $url, $p, $header));
	}
	
	/*
	holt den Inhalt einer Website
	@method: get oder post
	@url: URL
	@p: array mit name - value paaren
	@header: array mit Headerdaten, die der Website gesendet werden sollen
	*/
	static function fetch_page($method, $url, $p = array(), $header = array()){
		$postdata = http_build_query($p, '', '&');
		$opts = array('http' =>
			array(
				'method'  => $method,
				'content' => $postdata
			)
		);
		
		/*cUrl initialisieren*/
		$ch = curl_init ($url);
		if(strtolower($method) == 'post') {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch,  CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true); //zum testen, ermöglicht den Ausgangsheader einzusehen
		//$f = fopen('request.txt', 'w'); //zum testen
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Rückgabe an
		curl_setopt($ch, CURLOPT_HEADER, 1); //Antwortheader mit angeben im result
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		
		$result = curl_exec ($ch); //Request ausführen
		/*Header und Inhalt voneinander trennen*/
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$headers = substr($result, 0, $header_size);
		$result = substr($result, $header_size);
			
		//debug($headers); //Antwortheader ansehen
		//debug(curl_getinfo($ch, CURLINFO_HEADER_OUT)); //Ausgangsheader ansehen
		curl_close($ch);
		$result = console::pre_regex($result);
		//$result = utf8_decode($result);
		//debug($result);
		return $result; 
	}
	
	/*Vereinfacht einen String für das Parsen*/
	static function pre_regex($s){
		$s = preg_replace("/\r/", " ", $s); //Zeilenumbrüche entfernen
		$s = preg_replace("/\n/", " ", $s); //Zeilenumbrüche entfernen
		$s = preg_replace("/\&#13;/", " ", $s); //Zeilenumbrüche entfernen
		$s = preg_replace("/\&#034;/", "\"", $s); //Quotes decodieren
		$s = preg_replace("/\&amp;/", "&", $s);	//& Zeichen decodieren
		$s = preg_replace("/[\s]{2,}/", " ", $s); //Aus mehr als einem Leerzeichen hintereinander 1 Leerzeichen machen
		$s = preg_replace("/>[\s]+/", ">", $s); //Alle Leerzeichen nach > entfernen
		$s = preg_replace("/[\s]+</", "<", $s);	//Alle Leerzeichen vor < entfernen	
		$s = preg_replace("/[\s]+>/", ">", $s); //Alle Leerzeichen vor > entfernen
		$s = preg_replace("/<[\s]+/", "<", $s);	//Alle Leerzeichen nach < entfernen		
		return $s;
	}
		
	static function file_get_contents_curl($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	static function html2text($html){
		App::import('Vendor', 'html2text');
		return @convert_html_to_text($html);
	}
	
	static function wiki2html($wiki){
		App::import('Vendor', 'wiki2html');
		return @WikiTextToHTML::convertWikiTextToHTML($wiki);
	}
	
}
?>