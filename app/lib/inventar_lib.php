<?php

$apiUrl = "https://inventar.lippesola.de/api/v1/";


// =============================================

function GetFullPathOfItem($itemId, $apiToken): array
{
  global $apiUrl;
  $curl = curl_init();

  curl_setopt_array($curl, [
    CURLOPT_URL => $apiUrl."entities/".$itemId."/path",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_SSL_VERIFYHOST => getenv('CURLOPT_SSL_VERIFYHOST'),
    CURLOPT_SSL_VERIFYPEER => getenv('CURLOPT_SSL_VERIFYPEER'),
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
      "Accept: application/json",
      "Authorization: ".$apiToken
    ],
  ]);

  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  $fullPathofItem=array();
  if ($err) {
    echo "cURL Error #:" . $err;
  } else {
    //echo print_r($response,true);
    $fullPathofItem = json_decode($response, true);
  }
  return $fullPathofItem;
} 

?>