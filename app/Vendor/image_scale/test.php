<?
	$filename = 'test.jpg';
	$path = '.';
	//echo $cmd.WWW_ROOT.'inc/scale_and_thumb_image.bat '.$path.' '.$filename.' 1024 200';
	
	/*if(DS == '\\') {
		exec($cmd.WWW_ROOT.'inc/scale_and_thumb_image.bat '.$path.' '.$filename.' 1024 200 50 > test.txt');
	} else */
	//echo 'cd '.__DIR__.'; ./scale_and_thumb.sh '.$path.' '.$filename.' 1024 200 50';
	echo exec('cd '.__DIR__.'; ./scale_and_thumb.sh '.$path.' '.$filename.' 1024 200 50 >& test.txt');
?>