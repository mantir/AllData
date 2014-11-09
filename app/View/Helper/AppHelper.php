<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {
	
	function attribute_icon($attributes){
		if(!is_array($attributes)) return '';
		$out = '';
		foreach ($attributes as $key => $value) {
			$name = false;
			$title = '';
			$height = 30;
			if($key == 'audioFormat' && $value != 'mono') {
				//$name = 'icon_' . $value . '.gif';
				$name = 'icon_' . $value . '_4.png';
				$title = ucfirst($value);
			} else if($key == 'hdtv' && $value == 1) {
				//$name = 'icon_hdtv.gif';
				$name = 'hd_4.png';
				$title = 'HD-TV';
			} else if($key == 'audioDescription' && $value == 1) {
				//$name = 'icon_ad_1.jpg';
				$name = 'ad_2.png';
				$title = 'Audiodeskription';
			 } else if($key == 'hearingAid' && $value == 1) {
				//$name = 'icon_ut_1.jpg';
				$name = 'ut_2.png';
				$title = 'Untertitel';
			} else if($key == 'screenFormat') {
				$name = 'icon_' . preg_replace('/\:/', '_zu_', $value) . '_format.png';
				$title = 'Format: ' . $value;
				$height = '30';
			} else if($key == 'color' && $value == 'schwarz weiss') {
				$name = 'icon_schwarz_weiss.gif';   
				$title = 'Schwarz Weiß';
				$height = 18;
			}
			if($name)
				$out .= $this->image($name, array('height' => $height ? $height.'px' : '', 'title' => $title));
		}
		return $out;
	}
	
	function loader_gif(){
		return $this->img('gloader.gif');
	}
	
}
