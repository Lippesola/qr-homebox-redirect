<?php


/*

001AA0000xxx

QR Code zu Inventargegenständen
AA = Art des Objekts

AA=01 => K-xxx Kiste (dreistellig!)

AA=02 => P-xx Palette (=Stellplatz im Palettenregal) 

AA=03 => B-xx Boden (=Stellfläche in der Lagerhalle, die zugestellt wird)

AA=04 => D-xx Dusche

AA=05 => G-xx Gang (Gang in Lagerhalle vor/neben den Palettenregalen

AA=06 => Inventarzettel 
(Kunststoff 4 cm x 10 cm)
(alle zweistellig!) NEU IN 2015

AA=07 ? R-xx Regal-Boden in Springer-Wechselbrücke
NEU in 2017

AA=08 ? W-xx Wechselbrücke

*/

#echo "*code.php<br>";


$code= "";
$code= filter_input(INPUT_GET, 'code');
#echo "*".$code."+<br>";

$inventarQueryString="";
$matches=array();
if(preg_match("/001([0-9]{2})0000([0-9]{3})/",$code,$matches) ==true){
    // echo print_r($matches,true)."<br>";
    if ($matches[1]== "01") {
        $inventarQueryString="K";
    }
    else if ($matches[1]== "02") { 
         $inventarQueryString="P";   
    } 
    else if ($matches[1]== "07") {
         $inventarQueryString="R";
    } 
    else if ($matches[1]== "08") {
         $inventarQueryString="W";
    }   
    $inventarQueryString.="-".$matches[2];
}
else {
    echo "Code nicht erkannt.<br>";
    die();
}


# echo "inventarQueryString = ".$inventarQueryString."<br>";

include("./search.php");
?>