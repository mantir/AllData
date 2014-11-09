 <?php 

class ImageScale {
	
	/*
	@file the imagefile to be scaled
	@offset: if image height > width it is the y position, else the x position where the image is cropped for 240x240. The reference image to get the offset must be 240px on the shorter site.
	returns an array  with 0 => original image width, 1 => orginal image height
	*/
	function scaleImage($file, $offset, $original_ext = false){
		$filename = basename($file);
		$path = dirname($file).DS;
		
        $max_file = "34457280";                         // Approx 30MB
        $max_width = 1024;
		$max_height = 800;
        //debug($original_ext);
		if(!$original_ext)
			$original_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		else
			$original_ext = strtolower($original_ext);
        $file_ext = substr($filename, strrpos($filename, ".") + 1);
        $target = $file;

		$width = $this->getWidth($target);
		$height = $this->getHeight($target);
		// Scale the image if it is greater than the width set above
		if ($width > $max_width){
			$scale = $max_width / $width;
		} else 
		if($height > $max_height){
			$scale = $max_height / $height;
		} else {
			$scale = 1;
		}
		
		$uploaded = $this->resizeImage($target,$width,$height,$scale,$original_ext);
		$width = $this->getWidth($target);
		$height = $this->getHeight($target);
		
		/*define size*/
		$s1 = 240;
		$s2 = 80;
		$s3 = 48;
		$f2 = $path.$s1.DS.$filename; //define path for reference image (240, )
		//debug($target);
		//debug($f2);
		copy($target, $f2);
		
		if($height > $width){
			$y1 = $offset; $x1 = 0;
			$this->resizeImage($f2,$width,$height,$s1/$width,$file_ext);
			$this->cropImage($s1, $x1, $y1, $s1, $s1, $s1, $s1, $f2, $f2);
			
		} else {
			$x1 = $offset; $y1 = 0;
			$this->resizeImage($f2,$width,$height,$s1/$height,$file_ext);
			$this->cropImage($s1, $x1, $y1, $s1, $s1, $s1, $s1, $f2, $f2);
		}
		
		copy($f2, $path.$s2.DS.$filename);
		copy($f2, $path.$s3.DS.$filename);
		$this->resizeImage($path.$s2.DS.$filename,$s1,$s1,$s2/$s1,$file_ext);
		$this->resizeImage($path.$s3.DS.$filename,$s1,$s1,$s3/$s1,$file_ext);
        
        return array($width, $height);
	}
	
	
    function uploadImage($uploadedInfo, $uploadTo, $image_id, $offset = 0){
        $webpath = $uploadTo;
        $upload_dir = WWW_ROOT.str_replace("/", DS, $uploadTo);
        $upload_path = $upload_dir.DS;
        $max_file = "34457280";                         // Approx 30MB
        $max_width = 1024;

        $userfile_name = $uploadedInfo['name'];
        $userfile_tmp =  $uploadedInfo["tmp_name"];
        $userfile_size = $uploadedInfo["size"];
		$offset = 50;//$uploadedInfo['offset'];
        $filename = $image_id.'.jpg';
		$original_ext = strtolower(pathinfo($uploadedInfo['name'], PATHINFO_EXTENSION));
        $file_ext = substr($filename, strrpos($filename, ".") + 1);
        $uploadTarget = $upload_path.$filename;

        if(empty($uploadedInfo)) {
                  return false;
                }  

        if (isset($uploadedInfo['name'])){
            move_uploaded_file($userfile_tmp, $uploadTarget);
            chmod ($uploadTarget , 0777);
            $width = $this->getWidth($uploadTarget);
            $height = $this->getHeight($uploadTarget);
            // Scale the image if it is greater than the width set above
            if ($width > $max_width){
                $scale = $max_width/$width;
                $uploaded = $this->resizeImage($uploadTarget,$width,$height,$scale,$original_ext);
            } else {
                $scale = 1;
                $uploaded = $this->resizeImage($uploadTarget,$width,$height,$scale, $original_ext);
            }
			
			$size = array($this->getWidth($uploadTarget), $this->getHeight($uploadTarget));
			
			$s1 = 240;
			$s2 = 80;
			$s3 = 48;
			$f2 = $upload_path.$s1.DS.$filename;
			$f21 = $webpath.$s1.DS.$filename;
			debug($uploadTarget);
			debug($f2);
			copy($uploadTarget, $f2);
			
			if($height > $width){
				$y1 = $offset; $x1 = 0;
				$this->resizeImage($f2,$width,$height,$s1/$width,$file_ext);
				$this->cropImage($s1, $x1, $y1, $s1, $s1, $s1, $s1, $f21, $f21);
				
			} else {
				$x1 = $offset; $y1 = 0;
				$this->resizeImage($f2,$width,$height,$s1/$height,$file_ext);
				$this->cropImage($s1, $x1, $y1, $s1, $s1, $s1, $s1, $f21, $f21);
			}
			copy($uploadTarget, $upload_path.$s2.DS.$filename);
			copy($uploadTarget, $upload_path.$s3.DS.$filename);
			$this->resizeImage($upload_path.$s2.DS.$filename,$s1,$s1,$s2/$s1,$file_ext);
			$this->resizeImage($upload_path.$s3.DS.$filename,$s1,$s1,$s3/$s1,$file_ext);
        }
        return array('imagePath' => $webpath.$filename, 'imageName' => $filename, 'size' => array($this->getWidth($uploadTarget), $this->getHeight($uploadTarget)));
    }

    function getHeight($image) {
        $sizes = getimagesize($image);
        $height = $sizes[1];
        return $height;
    }
    function getWidth($image) {
        $sizes = getimagesize($image);
        $width = $sizes[0];
        return $width;
    }

    function resizeImage($image,$width,$height,$scale, $original_ext) {
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		$ext = pathinfo($image, PATHINFO_EXTENSION);

        try {
			if($original_ext == "png"){$source = "";
				$source = imagecreatefrompng($image);
			}elseif($original_ext == "jpg" || $original_ext == "jpeg"){
				$source = imagecreatefromjpeg($image);
			}elseif($original_ext == "gif"){
				$source = imagecreatefromgif($image);
			}
		} catch( Exception $e){
			debug('error');
		}
        imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
        if($ext == "png" || $ext == "PNG"){
            imagepng($newImage,$image,0);
        }elseif($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG"){
            imagejpeg($newImage,$image,90);
        }elseif($ext == "gif" || $ext == "GIF"){
            imagegif($newImage,$image);
        }
        chmod($image, 0777);
        return $image;
    }

    function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
        $ext = strtolower(substr(basename($image), strrpos(basename($image), ".") + 1));
        $source = "";
        if($ext == "png"){
            $source = imagecreatefrompng($image);
        }elseif($ext == "jpg" || $ext == "jpeg"){
            $source = imagecreatefromjpeg($image);
        }elseif($ext == "gif"){
            $source = imagecreatefromgif($image);
        }
        imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);

        if($ext == "png" || $ext == "PNG"){
            imagepng($newImage,$thumb_image_name,0);
        }elseif($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG"){
            imagejpeg($newImage,$thumb_image_name,90);
        }elseif($ext == "gif" || $ext == "GIF"){
            imagegif($newImage,$thumb_image_name);
        }

        chmod($thumb_image_name, 0777);
        return $thumb_image_name;
    }

    function cropImage($thumb_width, $x1, $y1, $x2, $y2, $w, $h, $thumbLocation, $imageLocation){
        $scale = $thumb_width/$w;
        $cropped = $this->resizeThumbnailImage($thumbLocation,$imageLocation,$w,$h,$x1,$y1,$scale);
        return $cropped;
    }
}
?> 