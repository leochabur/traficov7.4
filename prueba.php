<?php

	print round(1250,-1);

		$dom = new DOMDocument('1.0', 'utf-8');
		$dom->xmlStandalone = true;

	$element = $dom->createElement('se:solicitud');
	$element->setAttribute( "xmlns:se", "http://www.senasa.gov.ar/solicitud" );
	$element->setAttribute( "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance" );
	$element->setAttribute( "se:schemaLocation", "http://www.senasa.gov.ar/solicitud solicitudCertCarnicos.xsd" ); 

	// Insertamos el nuevo elemento como raíz (hijo del documento)
	$dom->appendChild($element);
	//	echo $dom->save("test.xml") 

?>