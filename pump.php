<?php

        set_time_limit(0);

        function wsGetSoapCli()
        {
        	$soap = new SoapClient('https://ws.fleet-online.com.ar/s/wservices/GpsFleetService.wsdl');
                return $soap;
        }


        function getOdometroUrbetrack($id, $token)
        {
                $urlOdometro = "https://app.urbetrack.com/api/v2/companies/173/vehicles/$id/vehicleodometer";
                $curl = curl_init();
                curl_setopt_array($curl, array(
                      CURLOPT_URL => $urlOdometro,
                      CURLOPT_CUSTOMREQUEST => "GET",
                      CURLOPT_RETURNTRANSFER => 1, 
                      CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "Authorization: Bearer $token"
                      ),
                    ));
                $response = curl_exec($curl);
                $info = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                $odometro = 0;
                if ($info == 200)
                {
                        $km = json_decode($response, true);
                        $odometro = $km['kilometer']['totalValue']['value'];
                }
                return $odometro;
        }


        $conn = new mysqli('mariadb-masterbus-trafico.planisys.net', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');

        mysqli_query($conn, "SET NAMES 'utf8'");

        $param = array(
                        'user' => 'WS.MASTERBUS',
                        'password' => 'Pump2022',
                        'company' => 'MASTERBUS',  
                        'store' => '01'
                        );
        $marker = array(
                        'user' => 'WS.MASTERBUS',
                        'password' => 'Pump2022',
                        'company' => 'MASTERBUS'
                        );

        $credentialUrbetrack = array(                                                
                                         "user" => "mdepeon",
                                         "password" => "Mde*678"
                                    );
        $urlLoginUrbetrack = "https://app.urbetrack.com/api/authentication/login";

        $curl = curl_init();
        curl_setopt_array($curl, array(
              CURLOPT_URL => $urlLoginUrbetrack,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode($credentialUrbetrack),
              CURLOPT_RETURNTRANSFER => 1, 
              CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
              ),
            ));
        $response = curl_exec($curl);

        $info = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $token = null;
        curl_close($curl);
        $idVehiculos = array();

        if ($info == 200)
        {
                //recupero el token el usuario
                $data = json_decode($response, true);
                $token = $data['AuthToken'];

                $urlGetVehicles = "https://app.urbetrack.com/api/core/vehicles?companyId=173";
                $curl = curl_init();
                curl_setopt_array($curl, array(
                      CURLOPT_URL => $urlGetVehicles,
                      CURLOPT_CUSTOMREQUEST => "GET",
                      CURLOPT_RETURNTRANSFER => 1, 
                      CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "Authorization: Bearer $token"
                      ),
                    ));
                $response = curl_exec($curl);
                $info = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                if ($info == 200)
                {
                        $dataMoviles = json_decode($response, true);
                        $vehiculos = $dataMoviles['Data'];
                        foreach ($vehiculos as $v)
                        {       
                                $key = $v['unitNumber'];
                                if (is_numeric($key))
                                {
                                        $key+=0;
                                }
                                $idVehiculos[$key] = $v['id'];
                        }
                }
                else
                {
                        $token = null;
                }

        }
        




        try{
                if ($token)
                {
			$clients = wsGetSoapCli();
            
                        $response = $clients->getFirstPendingTransaction($param);
                        
                        while (property_exists($response, 'trx_id'))
                        {
                                $idVehicle = null;
                                $key = null;
                                $odometro_urbe = 0;
                                if (is_numeric($response->vehicle_alias))
                                {
                                        $key = ($response->vehicle_alias + 0);
                                }

                                if (array_key_exists($key, $idVehiculos))
                                {
                                        $idVehicle = $idVehiculos[$key];
                                        $odometro_urbe = getOdometroUrbetrack($idVehicle, $token);
                                }
                                //odometro_urbetrack, km_urbe

                                $fccha_carga =  DateTime::createFromFormat('d-m-Y H:i:s', $response->trx_date);
                                $fechamysql = $fccha_carga->format('Y-m-d H:i:s');

                                $odometro = $response->car_kilometer;
                                $litros = $response->trx_volume;
                                $tipo = $response->prod_desc;
                                $surtidor = $response->pump;
                                $manguera = $response->hose;
                                $dominio = $response->car_plate;
                                $interno = $response->vehicle_alias;
                                $trx = $response->trx_id;

                                $lastCarga = "SELECT odometro_pump, odometro_urbetrack
                                                FROM carga_combustible_pump
                                                where interno = '$interno'
                                                order by fecha_carga desc
                                                limit 1";
                                $result = mysqli_query($conn, $lastCarga);
                                $lastKm = 0;
                                $lastKmUrbetrack = 0;

                                if ($row = mysqli_fetch_array($result))
                                {
                                        $lastKm = $row['odometro_pump'];
                                        $lastKmUrbetrack = $row['odometro_urbetrack'];
                                }

                                $kmNow = intval($odometro);
                                $kmNowUrbetrack = $odometro_urbe;

                                $kmRecorridos = $kmNow - $lastKm;
                                $kmRecorridosUrbetrack = $kmNowUrbetrack - $lastKmUrbetrack;




                                $sql = "INSERT INTO carga_combustible_pump (fecha_carga, odometro_pump, litros, tipo_combustible, surtidor, manguera, dominio, interno, trx_id, km, odometro_urbetrack, km_urbe)
                                        VALUES ('$fechamysql', '$odometro', '$litros', '$tipo', '$surtidor', '$manguera', '$dominio', '$interno', '$trx', $kmRecorridos, $odometro_urbe, $kmRecorridosUrbetrack)";

                                mysqli_query($conn, $sql);

                                $marker['trx_id'] = $response->trx_id;

                                $clients->SetLastInformedTransaction($marker);

                                $response = $clients->getFirstPendingTransaction($param);

                        }
                }
      }
      catch(Exception $fault)
      {
        echo 'ocurrio un error';
      }

      mysqli_close($conn);

?>