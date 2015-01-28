<?
class console{
	static $systemName = 'AllData';
	static $noreplyEmail = 'noreply@headkino.de';
	static $serviceEmail = 'info@headkino.de';
	static $singleReport = array('Gesammte Sendung', 'Einzelbeitrag');
	static $timeFSK = array(20 => 12, 22 => 16, 23 => 18);
	static $weekdays = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');
	static $weekdaysShort = array('Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So');
	static $intervalTypes = array('input timestamps', 'minutes', 'hours', 'days', 'weeks', 'months', 'years');
	static $logTypes = array('_error' => 'Error');
	static $unit_prefixes = array('p' => 'Piko', 'n' => 'Nano', 'µ' => 'Mikro', 'm' => 'Milli', 'c' => 'Zenti', 'd' => 'Dezi', 'h' => 'Hekto', 'k' => 'Kilo', 'M' => 'Mega', 'G' => 'Giga', 'T' => 'Tera');
	static $defaultDateformat = 'd.m.Y,H:i';
	
	static $disabled_functions = array('eval', 'assert', 'preg_replace', 'create_function', 'include', 'include_once', 'require', 'require_once', 'exec', 'passthru', 'system', 'shell_exec', '``','popen','proc_open','pcntl_exec','phpinfo','posix_mkfifo','posix_getlogin','posix_ttyname','getenv','get_current_user','proc_get_status','get_cfg_var','disk_free_space','disk_total_space','diskfreespace','getcwd','getlastmo','getmygid','getmyinode','getmypid','getmyuid','extract','parse_str','putenv','ini_set','mail','header','proc_nice','proc_terminate','proc_close','pfsockopen','fsockopen','apache_child_terminate','posix_kill','posix_mkfifo','posix_setpgid','posix_setsid','posix_setuid','fopen','tmpfile','bzopen','gzopen','SplFileObject->__construct','chgrp','chmod','chown','copy','file_put_contents','lchgrp','lchown','link','mkdir','move_uploaded_file','rename','rmdir','symlink','tempnam','touch','unlink','imagepng ','imagewbmp','image2wbmp','imagejpeg','imagexbm','imagegif','imagegd','imagegd2','iptcembed','ftp_get','ftp_nb_get','file_exists','file_get_contents','file','fileatime','filectime','filegroup','fileinode','filemtime','fileowner','fileperms','filesize','filetype','glob','is_dir','is_executable','is_file','is_link','is_readable','is_uploaded_file','is_writable','is_writeable','linkinfo','lstat','parse_ini_file','pathinfo','readfile','readlink','realpath','stat','gzfile','readgzfile','getimagesize','imagecreatefromgif','imagecreatefromjpeg','imagecreatefrompng','imagecreatefromwbmp','imagecreatefromxbm','imagecreatefromxpm','ftp_put','ftp_nb_put','exif_read_data','read_exif_data','exif_thumbnail','exif_imagetype','hash_file','hash_hmac_file','hash_update_file','md5_file','sha1_file','highlight_file','show_source','php_strip_whitespace','get_meta_tags');
	
	static $htmlInput = array('div' => array('class' => 'form-group'), 'class' => 'form-control');
	
	static function range($low, $high, $step = 1){
		$r = range($low, $high);
		if(!function_exists('padLeadingZero')) {
			function padLeadingZero(&$val){
				$val = str_pad($val, $padLength = 2, $padString = '0', $padType = STR_PAD_LEFT);
			}
		}
		//Pad the min and hr fields with a leading zero
		array_walk($r, 'padLeadingZero');
		
		return $r;
	}
	
	static function makeTimestamp($s){
		if(!is_numeric($s))
			$s = strtotime($s);
		return $s;
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
		if(strpos($url, 'edit')) return __('Edit'); else return __('Add');
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