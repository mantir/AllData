<? 
if(!$debug) {
	if($data) {
		if($format == 'csv') {
			echo f::array_to_csv($data);
		} else
		if($format == 'xml') {
			echo f::array_to_xml($data, $xml);
		} else {
			echo json_encode($data);//, JSON_HEX_TAG); //Json ausgeben
		}
	} else {
		echo $this->Session->flash();
	}
}
else {
	echo '<pre>';
	print_r($data); //Menschenlesbar ausgeben
	echo '</pre>';
} ?>