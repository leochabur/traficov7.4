<?php
  set_time_limit(0);
  session_start();
    include_once ('../../controlador/bdadmin.php');
    include_once ('sendmail.php');
    include ('../../modelo/utils/dateutils.php');
           $fecha = dateToMysql($_POST['fecha'], '/');
          // die($fecha);
  //  function generateExcel(){
	$conn = conexcion();
	
	$consulta = "SELECT ts.tipo, tu.tipo, if(i_v = 'i', ori.ciudad, d.ciudad) as localidad, o.nombre, interno,
                        time_format(if(i_v = 'i', hllegadaplantareal, hsalidaplantareal), '%H:%i') as horario, cantpax,
                        date_format(fservicio,'%d/%m/%Y') as fservicio,
                        if (pts.id is not null,
                                    if (pts.presupuestado, upper(articulo), (SELECT upper(articulo)
                                                                            FROM precioTramoServicio p
                                                                            inner join articulosClientes ac on ac.id = p.id_articulo
                                                                            where id_cronograma = c.id and id_estructuraCronograma = c.id_estructura and presupuestado
                                                                            limit 1)),
                                                                            (SELECT upper(articulo)
                                                                            FROM precioTramoServicio p
                                                                            inner join articulosClientes ac on ac.id = p.id_articulo
                                                                            where id_cronograma = c.id and id_estructuraCronograma = c.id_estructura and presupuestado
                                                                            limit 1))
                 FROM ordenes o
                 left join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                 left join tiposervicio ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
                 inner join cicloinforme ci on ci.id_tiposervicio = ts.id and ci.id_estructuratiposervicio = ts.id_estructura
                 left join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                 left join ciudades ori on ori.id = ciudades_id_origen and ori.id_estructura = c.ciudades_id_estructura_origen
                 left join ciudades d on d.id = ciudades_id_destino and d.id_estructura = ciudades_id_estructura_destino
                 left join unidades u on u.id = o.id_micro
                 left join tipounidad tu on tu.id = u.id_tipounidad and tu.id_estructura = u.id_estructura_tipoUnidad
                 left join precioTramoServicio pts on pts.id_cronograma = c.id and pts.id_estructuraCronograma = c.id_estructura and pts.id_tipoUnidad = tu.id and pts.id_estructuraTipoUnidad = tu.id_estructura
                 left join articulosClientes ac on ac.id = pts.id_articulo
                 where concat(fservicio, ' ', o.hsalida) between date_sub(concat('$fecha',' ',horacierreinforme), interval 1 day) and concat('$fecha', ' ',horacierreinforme) and o.id_estructura = $_SESSION[structure] and o.id_cliente = 10 and not borrada and not suspendida
                 order by ts.tipo, fservicio, horario";
  //  die($consulta);
    $result = mysql_query($consulta, $conn);
	if(mysql_num_rows($result)){
						
		date_default_timezone_set('America/Mexico_City');

		if (PHP_SAPI == 'cli')
			die('Este archivo solo se puede ver desde un navegador web');

		/** Se agrega la libreria PHPExcel */
		require_once '../../reporteexcel/lib/PHPExcel/PHPExcel.php';

		// Se crea el objeto PHPExcel
		$objPHPExcel = new PHPExcel();

		// Se asignan las propiedades del libro
		$objPHPExcel->getProperties()->setCreator("Informe Auto Generado") //Autor
							 ->setLastModifiedBy("Informe Auto Generado") //Ultimo usuario que lo modificó
							 ->setTitle("Reporte servicios")
							 ->setSubject("Reporte servicios")
							 ->setDescription("Reporte diario de servicios")
							 ->setKeywords("Reporte")
							 ->setCategory("Reporte excel");

		$tituloReporte = "Reporte de servicios autogenerado";
		$titulosColumnas = array('TIPO SERVICIO', 'FECHA SERVICIO', 'TIPO UNIDAD', 'LOCALIDAD', 'RECORRIDO', 'ARTICULO', 'INTERNO', 'HORARIO', 'PASAJEROS');
		
		$objPHPExcel->setActiveSheetIndex(0)
        		    ->mergeCells('A1:D1');

		// Se agregan los titulos del reporte
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1',$tituloReporte)
        		    ->setCellValue('A3',  $titulosColumnas[0])
		            ->setCellValue('B3',  $titulosColumnas[1])
        		    ->setCellValue('C3',  $titulosColumnas[2])
            		->setCellValue('D3',  $titulosColumnas[3])
        		    ->setCellValue('E3',  $titulosColumnas[4])
            		->setCellValue('F3',  $titulosColumnas[5])
            		->setCellValue('G3',  $titulosColumnas[6])
            		->setCellValue('H3',  $titulosColumnas[7])
            		->setCellValue('I3',  $titulosColumnas[8]);
		
		//Se agregan los datos de los alumnos
		$i = 4;
		while ($fila = mysql_fetch_array($result)) {
			$objPHPExcel->setActiveSheetIndex(0)
        		    ->setCellValue('A'.$i,  $fila['0'])
                    ->setCellValue('B'.$i,  $fila['7'])
		            ->setCellValue('C'.$i,  $fila['1'])
        		    ->setCellValue('D'.$i,  ($fila['2']))
            		->setCellValue('E'.$i, utf8_decode($fila['3']))
        		    ->setCellValue('F'.$i,  $fila['8'])
        		    ->setCellValue('G'.$i,  $fila['4'])
		            ->setCellValue('H'.$i,  $fila['5'])
        		    ->setCellValue('I'.$i,  $fila['6']);
                    $i++;
		}

		
		$estiloTituloReporte = array(
        	'font' => array(
	        	'name'      => 'Verdana',
    	        'bold'      => true,
        	    'italic'    => false,
                'strike'    => false,
               	'size' =>16,
	            	'color'     => array(
    	            	'rgb' => 'FFFFFF'
        	       	)
            ),
	        'fill' => array(
				'type'	=> PHPExcel_Style_Fill::FILL_SOLID,
				'color'	=> array('argb' => 'FF220835')
			),
            'borders' => array(
               	'allborders' => array(
                	'style' => PHPExcel_Style_Border::BORDER_NONE                    
               	)
            ), 
            'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'rotation'   => 0,
        			'wrap'          => TRUE
    		)
        );

		$estiloTituloColumnas = array(
            'font' => array(
                'name'      => 'Arial',
                'bold'      => true,                          
                'color'     => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'fill' 	=> array(
				'type'		=> PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation'   => 90,
        		'startcolor' => array(
            		'rgb' => 'c47cf2'
        		),
        		'endcolor'   => array(
            		'argb' => 'FF431a5d'
        		)
			),
            'borders' => array(
            	'top'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                ),
                'bottom'     => array(
                    'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
                    'color' => array(
                        'rgb' => '143860'
                    )
                )
            ),
			'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'wrap'          => TRUE
    		));
    		

			
		$estiloInformacion = new PHPExcel_Style();
		$estiloInformacion->applyFromArray(
			array(
           		'font' => array(
               	'name'      => 'Arial',               
               	'color'     => array(
                   	'rgb' => '000000'
               	)
           	),
           	'fill' 	=> array(
				'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
				'color'		=> array('argb' => 'FFd9b7f4')
			),
           	'borders' => array(
               	'left'     => array(
                   	'style' => PHPExcel_Style_Border::BORDER_THIN ,
	                'color' => array(
    	            	'rgb' => '3a2a47'
                   	)
               	)             
           	)
        ));
		 

		$objPHPExcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($estiloTituloReporte);
		$objPHPExcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($estiloTituloColumnas);
		$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:I".($i-1));
				
		for($i = 'A'; $i <= 'I'; $i++){
			$objPHPExcel->setActiveSheetIndex(0)			
				->getColumnDimension($i)->setAutoSize(TRUE);
		}
		

		
		// Se asigna el nombre a la hoja
		$objPHPExcel->getActiveSheet()->setTitle('Servicios');

		// Se activa la hoja para que sea la que se muestre cuando el archivo se abre
		$objPHPExcel->setActiveSheetIndex(0);
		// Inmovilizar paneles 
		//$objPHPExcel->getActiveSheet(0)->freezePane('A4');
	//	$objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);
                                          				//		print 'cabeceras ok;';
//		exit;
		// Se manda el archivo al navegador web, con el nombre que se indica (Excel2007)
	//	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	//	header('Content-Disposition: attachment;filename="reporteservicios.xlsx"');
	//	header('Cache-Control: max-age=0');




		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('reporteservicios.xlsx');
	//	$objWriter->save(str_replace(__FILE__,'/reporteservicios.xlsx',__FILE__));
		
         $file = 'reporteservicios.xlsx';
         $sql = "SELECT correo FROM correos_informe";
         $result = mysql_query($sql,$conn);
         $dest = '';
         while ($row = mysql_fetch_array($result)){
               if ($dest){
                  $dest.=",$row[0]";
               }
               else{
                    $dest = "$row[0]";
               }
         }
         enviarMailAdjunto($dest, "Informacion del dia $_POST[fecha]", "Correo autogenerado", $file);

	}
	else{
		print('No hay resultados para mostrar');
	}
	
//	return 'totornjaaaa';
//	}
?>
