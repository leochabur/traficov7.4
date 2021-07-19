<?php
/*************************************
 * 
 ************************************/


	//Ruta de la imagen original
	$rutaImagenOriginal="./imagen/aprilia classic.jpg";
	
	//Creamos una variable imagen a partir de la imagen original
	$img_original = imagecreatefromjpeg($rutaImagenOriginal);
	
	//Se define el maximo ancho o alto que tendra la imagen final
	$max_ancho = 200;
	$max_alto = 200;
	
	//Ancho y alto de la imagen original
	list($ancho,$alto)=getimagesize($rutaImagenOriginal);
	
	//Se calcula ancho y alto de la imagen final
	$x_ratio = $max_ancho / $ancho;
	$y_ratio = $max_alto / $alto;
	
	//Si el ancho y el alto de la imagen no superan los maximos, 
	//ancho final y alto final son los que tiene actualmente
	if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){//Si ancho 
		$ancho_final = $ancho;
		$alto_final = $alto;
	}
	/*
	 * si proporcion horizontal*alto mayor que el alto maximo,
	 * alto final es alto por la proporcion horizontal
	 * es decir, le quitamos al alto, la misma proporcion que 
	 * le quitamos al alto
	 * 
	*/
	elseif (($x_ratio * $alto) < $max_alto){
		$alto_final = ceil($x_ratio * $alto);
		$ancho_final = $max_ancho;
	}
	/*
	 * Igual que antes pero a la inversa
	*/
	else{
		$ancho_final = ceil($y_ratio * $ancho);
		$alto_final = $max_alto;
	}
	
	//Creamos una imagen en blanco de tamaño $ancho_final  por $alto_final .
	$tmp=imagecreatetruecolor($ancho_final,$alto_final);	
	
	//Copiamos $img_original sobre la imagen que acabamos de crear en blanco ($tmp)
	imagecopyresampled($tmp,$img_original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
	
	//Se destruye variable $img_original para liberar memoria
	imagedestroy($img_original);
	
	//Definimos la calidad de la imagen final
	$calidad=95;
	
	//Se crea la imagen final en el directorio indicado
	imagejpeg($tmp,"./imagen/retoque.jpg",$calidad);
	
	/* SI QUEREMOS MOSTRAR LA IMAGEN EN EL NAVEGADOR
	 * 
	 * descomentamos las lineas 64 ( Header("Content-type: image/jpeg"); ) y 65 ( imagejpeg($tmp); ) 
	 * y comentamos la linea 57 ( imagejpeg($tmp,"./imagen/retoque.jpg",$calidad); )
	 */ 
	//Header("Content-type: image/jpeg");
	//imagejpeg($tmp);
	
	

?>