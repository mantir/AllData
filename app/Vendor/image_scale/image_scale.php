<?


class ImageScale{
	/*
	@file the imagefile to be scaled
	@offset: if image height > width it is the y position, else the x position where the image is cropped for 240x240. The reference image to get the offset must be 240px on the shorter site.
	returns an array  with 0 => original image width, 1 => orginal image height
	*/
	function scaleImage($file, $offset){
		$filename = basename($file);
		$path = dirname($file);
		if(!$offset) $offset = 0;
		
		//Sizes:
		//240x240, 80x80, 48x48, orginal max: 1024x1024
		//Other sizes maybe later:
		//large: 	1024 x 768
		//medium: 	 512 x 384
		//small: 	 256 x 192
		//tiny: 	  85 x 64
		//square	  50 x 50
		
		$maxWidthHeight = 1024;
		
		if(DS == '\\') { //WINDOWS
			$cmd = 'C:\\windows\\system32\\cmd.exe /c ';
			//echo $cmd.WWW_ROOT.'inc/scale_and_thumb_image.bat '.$path.' '.$filename.' 1024 200';
			exec($cmd.dirname(__FILE__).DS.'scale_and_thumb.bat '.$path.' '.$filename.' '.$maxWidthHeight.' '.$offset.'', $out);
		} else //LINUX
			exec('cd '.__DIR__.'; ./scale_and_thumb.sh '.$path.' '.$filename.' '.$maxWidthHeight.' '.$offset.' >& test.txt', $out, $return_var);
			
		
		//Last string in the output must be WidthxHeight of the original image
		//debug($return_var);	
		//debug($out);
		$wh = explode('x', end($out));
		return $wh;
			
	}
	
	function testmagick(){
		//exec('cd '.__DIR__.'; convert test.jpg -resize 100x100 teZt.jpg >& test.txt', $out, $return_var);
		//exec('ls -l /etc/', $out, $return_var);
		exec('convert > test.txt',$out, $return_var);
		debug($return_var);
		debug($out);
		//exec('cd '.__DIR__.'; ./scale_and_thumb.sh . test.jpg 1024 0 >& test.txt', $out, $return_var);
	}
}

?>