<?php
  
  include_once $_SERVER["DOCUMENT_ROOT"].'/controlador/bdadmin.php';

	function comunicatePush($from)
	{
            $conn = conexcion(true);
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "http://iotdevices.masterbus.net/api/login",
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS =>array(
                                                    'user' => 'Leo',
                                                    'pass' => 'leoMB'
                                                ),
              CURLOPT_RETURNTRANSFER => 1, 
              CURLOPT_HTTPHEADER => array(
                                          'content-type' => 'application/json'
                                          ),
            ));
            $response = curl_exec($curl);    
            curl_close($curl);

            $insert = "INSERT INTO embeddingsSent (response, stamp) VALUES ('$response - LOGIN PUSH', now())";            
            mysqli_query($conn, $insert);

            $body = json_decode($response, true);


            $url = "http://iotdevices.masterbus.net/api/fcm/push";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $headers = array(
               "access-token: $body[token]",
               "Content-Type: application/json",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            $data = '{"msg":
                            {"data":
                                    {"backend":"faceid",
                                    "desc":"SistemaFaceID",
                                    "action":"sync"
                                    }
                            }
                    }';


            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $resp = curl_exec($curl);
            curl_close($curl);

            $insert = "INSERT INTO embeddingsSent (response, stamp) VALUES ('$resp - $from', now())";
            
            mysqli_query($conn, $insert);
            mysqli_close($conn);
    }
?>