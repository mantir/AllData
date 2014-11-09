<? 
App::import('Vendor', 'html2text');
ob_start();
echo $this->element('../Emails/html/client_booking');
$html = ob_get_contents();
ob_end_clean();
echo convert_html_to_text($html)
?>