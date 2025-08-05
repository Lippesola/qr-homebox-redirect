<?php
include(dirname(__FILE__)."/lib/inventar_lib.php");

// /echo "Post: ".print_r($_POST,true)."<br><br>";
// echo "Get: ".print_r($_GET,true)."<br><br>";


echo "<!doctype html>
<html>
  <head>
    <meta charset=\"utf-8\">
    <title>…</title>
    <!-- <link rel=\"stylesheet\" href=\"abc.css\"> -->
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />
    <style>   
    </style>
  </head>
  <body>";

// =============================================

if (!isset($inventarQueryString)) {
    $inventarQueryString= "";
    $inventarQueryString= filter_input(INPUT_GET, 'q');
}
//echo "inventarQueryString = ".$inventarQueryString."<br><br>";

// =============================================

echo "<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"get\">
  <input type=\"text\" id=\"q\" name=\"q\" value=\"".$inventarQueryString."\">&nbsp;&nbsp;&nbsp;
  <input type=\"submit\" value=\"Suchen\">
</form>
<hr>
<br>";

if ($inventarQueryString== '') {
    die();
}

// =============================================

$curl = curl_init();

// Log in and get token
curl_setopt_array($curl, [
  CURLOPT_URL => $apiUrl."users/login",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_SSL_VERIFYHOST => false,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode([
    'password' => $homeboxPassword,
    'stayLoggedIn' => null,
    'username' => $homeboxUsername
  ]),
  CURLOPT_HTTPHEADER => [
    "Accept: application/json",
    "Content-Type: application/json"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
  die();
}

$responseObject = json_decode($response, true);
// print_r( $responseObject);
$token = $responseObject["token"];
// echo "". $token ."<br><br>";



// =============================================

// Search for item

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => $apiUrl."items?q=".$inventarQueryString,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_SSL_VERIFYHOST => false,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => [
    "Accept: application/json",
    "Authorization: ".$token
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
  die();
} 

$responseObject = json_decode($response, true);
// echo print_r( $responseObject["items"],true)."<br>ybr>";

// ################################################

if (sizeof($responseObject["items"]) > 0) {
    foreach ($responseObject["items"] as $item) {   
        echo "<small>Titel:</small><br>";  
        echo "<b>".$item["name"]."</b><br><br>";
        
        if (isset($item["description"]) & $item["description"]!="") {
          echo "<small>Beschreibung:</small><br>";  
          echo $item["description"]."<br><br>";
        }

        echo "<small>Pfad:</small><br>";  
        $fullPathOfItem= GetFullPathOfItem($item["id"],$token);
        $path= array();
        foreach ($fullPathOfItem as $key => $value) {
           // echo $value["name"]."<br>";
            array_push($path, $value["name"]);
        }
        $path = array_reverse($path);
        echo "&bull;&nbsp;".implode("<br>&bull;&nbsp;", $path);
        echo "<br><br>";

        if (preg_match("/(K-[0-9]{3})/",$item["name"], $matches))
        {  
          echo "Kistenschild:&nbsp;&nbsp;&nbsp;".
          "<a target=\"_blank\" href=\"kistenschild.php?q=".$matches[1]."\">Normal</a>&nbsp;&nbsp;&nbsp;". 
          "<a target=\"_blank\" href=\"kistenschild.php?q=".$matches[1]."&kistenschild_groesse=w115h90\">Klein</a><br>";
        }
        
        echo "<br><br>
        <hr>
        <br>";
    }
}

echo "</body>
</html>";
?>