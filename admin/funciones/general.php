<?php
 
function urls_amigables($url) {
 
    $find = array('á', 'é', 'í', 'ó', 'ú','ü', 'ñ');
    $repl = array('a', 'e', 'i', 'o', 'u','u', 'n');
    $url = str_replace ($find, $repl, $url);
 
    $find = array(' ', '&', '\r\n', '\n', '+'); 
    $url = str_replace ($find, '-', $url);
 
    $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
    $repl = array('', '-', '');
    $url = preg_replace ($find, $repl, $url);
    
    $url = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $url); 
	$url = preg_replace("/[\/_|+ -]+/", '-', $url);
    
    $url = strtolower($url);
    return $url;
}

function sanear_string($string)
{
	
	$string = trim($string);
	
	$string = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$string
	);

	$string = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$string
	);

	$string = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$string
	);

	$string = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$string
	);

	$string = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$string
	);

	$string = str_replace(
			array('ñ', 'Ñ', 'ç', 'Ç'),
			array('n', 'N', 'c', 'C',),
			$string
	);
	
	$find = array(' ', '&', '\r\n', '\n', '+');
	$string = str_replace ($find, '-', $string);
	
	//Esta parte se encarga de eliminar cualquier caracter extraño
	$string = str_replace(
			array("\\", "¨", "º",   "~",
					"#", "@", "|", "!", "\"",
					"·", "$", "%", "&", "/",
					"(", ")", "?", "'", "¡",
					"¿", "[", "^", "`", "]",
					"+", "}", "{", "¨", "´",
					">", "< ", ";", ",", ":",
					".", " "),
			'',
			$string
	);

	
	$string = strtolower($string);
	
	return $string;
}
 
function create_slug($phrase, $maxLength=100000000000000)
{
	$result = strtolower($phrase);

	$result = preg_replace("/[^A-Za-z0-9\s-._\/]/", "", $result);
	$result = trim(preg_replace("/[\s-]+/", " ", $result));
	$result = trim(substr($result, 0, $maxLength));
	$result = preg_replace("/\s/", "-", $result);

	return $result;
}

function url_slug($str, $options = array()) {
	// Make sure string is in UTF-8 and strip invalid UTF-8 characters
	$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

	$defaults = array(
			'delimiter' => '-',
			'limit' => null,
			'lowercase' => true,
			'replacements' => array(),
			'transliterate' => false,
	);

	// Merge options
	$options = array_merge($defaults, $options);

	$char_map = array(
			// Latin
			'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
			'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
			'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
			'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
			'ß' => 'ss',
			'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
			'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
			'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
			'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
			'ÿ' => 'y',

			// Latin symbols
			'©' => '(c)',
 
	);

	// Make custom replacements
	$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

	// Transliterate characters to ASCII
	if ($options['transliterate']) {
		$str = str_replace(array_keys($char_map), $char_map, $str);
	}

	// Replace non-alphanumeric characters with our delimiter
	$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

	// Remove duplicate delimiters
	$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

	// Truncate slug to max. characters
	$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

	// Remove delimiter from ends
	$str = trim($str, $options['delimiter']);

	return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}

function CrearThumb($avatarth, $file_ext, $file_tmp, $conCrop=false,$newwidth=50){
     
    /* creo la imagen */                     
    if($file_ext=="jpg" || $file_ext=="jpeg" )
    { 
        $src = imagecreatefromjpeg($file_tmp);
    }
    else if($file_ext=="png")
    { 
        $src = imagecreatefrompng($file_tmp);
    }
    else 
    {
        $src = imagecreatefromgif($file_tmp);
    }

    list($width,$height)=getimagesize($file_tmp);
 
    $newheight=($height/$width)*$newwidth;
    
    if ($conCrop)
    {

        $tmp = CropImagen($src, $width,$height, $newwidth, $newheight);
        imagejpeg($tmp,$avatarth,100);
    }
    else
    {
    // creo thumb
        $tmp=imagecreatetruecolor($newwidth,$newheight);

        imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
        imagejpeg($tmp,$avatarth,100);
    }

    imagedestroy($src);
    imagedestroy($tmp); 
} 

function CropImagen($src,$owidth, $oheight, $cwidth, $cheight, $pos ='center') {

 
	$thumb = imagecreatetruecolor($cwidth,$cheight);
	if ($owidth > $oheight) {
	    $off_w = ($owidth-$oheight)/2;
	    $off_h = 0;
	    $owidth = $oheight;
	} elseif ($oheight > $owidth) {
	    $off_w = 0;
	    $off_h = ($oheight-$owidth)/2;
	    $oheight = $owidth;
	} else {
	    $off_w = 0;
	    $off_h = 0;
	}
	
	imagecopyresampled($thumb, $src, 0, 0, $off_w, $off_h, $cwidth, $cheight, $owidth, $oheight);
	 
	return $thumb;
}
  