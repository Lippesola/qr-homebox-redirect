<?php

$apiUrl = "https://inventar.lippesola.de/api/v1/";

if (file_exists(dirname(__FILE__)."/homebox_login.php")) {
  require_once(dirname(__FILE__)."/homebox_login.php");
}

$homeboxUsername= $_ENV["homeboxUsername"] ;
$homeboxPassword= $_ENV["homeboxPassword"];



// =============================================

function GetFullPathOfItem($itemId, $apiToken): array
{

  $curl = curl_init();

  curl_setopt_array($curl, [
    CURLOPT_URL => "https://inventar.lippesola.de/api/v1/items/".$itemId."/path",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
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
    // echo print_r($response,true);
    $fullPathofItem = json_decode($response, true);
  }
  return $fullPathofItem;
} 

?>