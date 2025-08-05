<?php

error_reporting(E_ALL & ~E_NOTICE & E_DEPRECATED);
ini_set("display_errors", "1");

include(dirname(__FILE__)."/lib/inventar_lib.php");


// ############################################################################################

// Parameter
/*
$_GET["kistenschild_groesse"]= "w115h90"; // klein
$_GET["kistenschild_groesse"]; // groß, wenn Parameter nicht da, oder nicht w115h90 ist.// ?
*/

// ############################
// ############################

# Parameter zusammenstellung

$kistennummer = filter_input(INPUT_GET, 'q');
// "K-015"; // $gegenstaende[$_GET["id"]]["name"];

// ############################
// ############################



// ############################################################################################



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
  CURLOPT_URL => $apiUrl."items?q=".$kistennummer,
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
// echo print_r( $responseObject["items"],true)."<br>";


if (sizeof($responseObject["items"]) > 1) {
   echo "Mehr als einen Gegenstand gefunden. Schild kann nur für einen Gegenstand erstellt werden.";
   die();
}



// #####################################################

// Get Photo

$curl = curl_init();



curl_setopt_array($curl, [
  CURLOPT_URL => $apiUrl."items/".$responseObject["items"][0]["id"]."/attachments/".$responseObject["items"][0]["imageId"],
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => [
    "Accept: application/octet-stream",
    "Authorization: ".$token
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

$foto="";

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  $photoData = $response;
}

// #####################################################

# Parameter zusammenstellung

$fullPathOfItem = GetFullPathOfItem($responseObject["items"][0]["id"],$token);
if (sizeof($fullPathOfItem) > 0) { 
   $hauptlagerort = $fullPathOfItem[0]["name"];  
}
if (sizeof($fullPathOfItem) >= 3) { 
   $lagerortText = "Lagerort ".$fullPathOfItem[sizeof($fullPathOfItem)-2]["name"];  
} else {
   $lagerortText = "";  
}

$kistenschild_titel = $responseObject["items"][0]["name"]; // $gegenstaende[$_GET["id"]]["inhalt_titel"];
// $hauptlagerort = "???"; GetFullPathOfItem($responseObject["items"][0]["id"],$token); // $lagerorthistorie[0];
$zusatztext = $responseObject["items"][0]["description"]; // Zusatzinfos
$kistenschild_groesse= filter_input(INPUT_GET, 'kistenschild_groesse');  // w115h90 = klein, leer =



// ############################################################################################
// ############################################################################################


#echo ini_get('display_errors');
// if (!ini_get('display_errors')) {
//     ini_set('display_errors', '1');
// }
#echo ini_get('display_errors');


require_once(dirname(__FILE__)."/vendor/autoload.php");




#########################################################


/*
if ($fehlermeldung=="")
   {

   $inhaltsausgabe.= "Name: ".$gegenstaende[$_GET["id"]]["name"]."<br><br>";

   $inhaltsausgabe.="<u>Zusatzinfos:</u><br>".
   nl2br($gegenstaende[$_GET["id"]]["zusatzinfos"]).
   "<hr>";


 // Lagerort

   $inhaltsausgabe.="<u>Lagerort:</u><br>";

   $rekursivgegenstandid = $_GET["id"];
   $lagerorthistorie=array();

   #$inhaltsausgabe.= OutputArray($inhaltgegenstand[$rekursivgegenstandid]);

   while (isset($inhaltgegenstand[$rekursivgegenstandid]))
      {
      foreach ($inhaltgegenstand[$rekursivgegenstandid] as $trash => $aktinhaltgegenstandid)
         {
         if ($aktinhaltgegenstandid != 459)
            {
            #$inhaltsausgabe.= "*".OutputArray($aktinhaltgegenstandvalue)."+";
            #$inhaltsausgabe.= "*".OutputArray($gegenstaende[$aktinhaltgegenstandvalue])."+";
             array_push($lagerorthistorie, $gegenstaende[$aktinhaltgegenstandid]["name"]);


             }
           $rekursivgegenstandid = $aktinhaltgegenstandid;
         }
      }

   $lagerorthistorie = array_reverse($lagerorthistorie);
   $lagerorthistorieGekuerzt=$lagerorthistorie;
   array_shift($lagerorthistorieGekuerzt);
   $lagerortText= implode("\n", $lagerorthistorieGekuerzt);





   // Inhalt
   $inhaltsliste=array();

   if (isset($gegenstandinhalt[$_GET["id"]]))
      {
      foreach ($gegenstandinhalt[$_GET["id"]] as $trash => $aktinhaltgegenstandid)
         {
         //$inhaltsausgabe.= "*".OutputArray($aktinhaltgegenstandid)."+";
         //$inhaltsausgabe.= "*".OutputArray($gegenstaende[$aktinhaltgegenstandid])."+";
         array_push($inhaltsliste,
            trim($gegenstaende[$aktinhaltgegenstandid]["name"]));
         }
      }

   $inhaltText.= implode("\n", $inhaltsliste);
   sort($inhaltsliste);


   $inhaltsausgabe.="<u>Menge & Einheit:</u><br>".
   nl2br($gegenstaende[$_GET["id"]]["menge"])." ".
   nl2br($gegenstaende[$_GET["id"]]["einheit"])." ";
   if ($gegenstaende[$_GET["id"]]["einheit_kom"]!="")
      {
       $inhaltsausgabe.= " (".nl2br($gegenstaende[$_GET["id"]]["einheit_kom"]).")";
      }
   $inhaltsausgabe.="<hr>";




   $inhaltsausgabe.="<U>Verwendungszweck:</u><br>".
   nl2br($gegenstaende[$_GET["id"]]["zweck"]).
   "<hr>";

   $inhaltsausgabe.="<U>Kommentar zum Lagerort:</u><br>".
   nl2br($gegenstaende[$_GET["id"]]["lagerort_kom"]).
   "<hr>";

   } // Ende wenn ID ok ist
*/






#######################################################################################################
######################################################################################################

$showBorders=0; // 0 = aus 1 = an


// Einstellungen für A5

$defaultFontSize=12;
$marginLeftRight=5;


$titelFontSize=20;
$titelX=5;
$titelY=1;
$titelW=210-2*$marginLeftRight;
$titelH=8;

$kistennummerFontSize=18;
$kistennummerX=90;
$kistennummerY=68;
$kistennummerW=82;
$kistennummerH=10;

$lageortFontSize=18;
$lagerortX=90;
$lagerortY=78;
$lagerortW=82;
$lagerortH=28;

$qrCodeX=108;
$qrCodeY=103;
$qrCodeW=45;
$qrCodeH=45;

$inhaltTabelleX=$marginLeftRight;
$inhaltTabelleY=13;
$inhaltTabelleArray=array(54,13,13);
$inhaltTabelleHeaderHeight=7;
$inhaltTabelleCellHeight=10;
$inhaltTabelleZeilen=10;

$hautplagerortFontSize=26;
$hautplagerortX=175;
$hautplagerortY=125;
$hautplagerortW=112;
$hautplagerortH=30;

$fotoX=93;
$fotoY=13;
$fotoW=75;
$fotoH=50;

$solaLogoX=175;
$solaLogoY=130;
$solaLogoW=30;
$solaLogoH=15;

$infoTexteFontSize=10;
$infoTexteX=5;
$infoTexteY=125;
$infoTexteW=95;
$infoTexteH=20;


if ($kistenschild_groesse==="w115h90") {
   $defaultFontSize=9;
   $marginLeftRight=2;

   $titelFontSize=18;
   $titelX=2;
   $titelY=2;
   $titelW=115-2*$marginLeftRight;
   $titelH=6;

   $kistennummerFontSize=14;
   $kistennummerX=53;
   $kistennummerY=11;
   $kistennummerW=42;
   $kistennummerH=9;

   $lageortFontSize=12;
   $lagerortX=53;
   $lagerortY=20;
   $lagerortW=42;
   $lagerortH=20;

   $qrCodeX=59;
   $qrCodeY=62;
   $qrCodeW=30;
   $qrCodeH=30;

   $inhaltTabelleFontSize=8;
   $inhaltTabelleX=$marginLeftRight;
   $inhaltTabelleY=45;
   $inhaltTabelleArray=array(30,10,10);
   $inhaltTabelleHeaderHeight=6;
   $inhaltTabelleCellHeight=8;
   $inhaltTabelleZeilen=4;

   $hautplagerortFontSize=18;
   $hautplagerortX=96;
   $hautplagerortY=78;
   $hautplagerortW=68;
   $hautplagerortH=19;

   $fotoX=$marginLeftRight;
   $fotoY=10;
   $fotoW=50;
   $fotoH=33;

   $solaLogoX=96;
   $solaLogoY=80;
   $solaLogoW=20;
   $solaLogoH=10;

   $infoTexteFontSize=8;
   $infoTexteX=53;
   $infoTexteY=40;
   $infoTexteW=42;
   $infoTexteH=23;
}


// ###############################################################################
// ###############################################################################




// create new PDF document
$pdf = new TCPDF("P", "mm", "A4", true, 'UTF-8', false);
 
// set image scale factor
$pdf->setImageScale(1);

// set font
$pdf->SetFont('helvetica', '', $defaultFontSize);

// add a page
$pdf->AddPage();


###########################################################


// Hilfslininen für kleines Format
// if ($kistenschild_groesse=="w115h90") {
//    $pdf->Line(1,90,115,90);
//    $pdf->Line(115,1,115,90);
// }






// Inhalt-Titel (ganz oben)
$pdf->SetFont('helvetica', 'B', $titelFontSize);
$inhaltTitel=trim($kistenschild_titel); // trim(mb_convert_encoding($kistenschild_titel,"UTF-8","ISO-8859-1"));
// if ($inhaltTitel=="" && sizeof(value: $inhaltsliste)>0) {
//    $inhaltTitel = mb_convert_encoding(implode(", ",$inhaltsliste),"UTF-8","ISO-8859-1");
// }
$pdf->MultiCell($titelW, $titelH, $inhaltTitel,
"B", 'C', false, $marginLeftRight, $titelX, $titelY, true, 0, false, true, 0, 'T', false);
$pdf->SetFont('helvetica', '', $defaultFontSize);


###########################################################


// Name = Kistennummer
$pdf->SetFont('helvetica', 'B', $kistennummerFontSize);
$pdf->MultiCell($kistennummerW, $kistennummerH, $kistennummer , 
$showBorders, 'C', false, $marginLeftRight, $kistennummerX, $kistennummerY, true, 0, false, true, 0, 'T', false);
$pdf->SetFont('helvetica', '', $defaultFontSize);
// mb_convert_encoding($kistennummer,"UTF-8","ISO-8859-1")



###########################################################

// Lagerort
$pdf->SetFont('helvetica', '', $lageortFontSize);

// public MultiCell(float $w, float $h, string $txt, mixed $border[, string $align = 'J' ]
// [, bool $fill = false ][, int $ln = 1 ][, float|null $x = null ][, float|null $y = null ][, bool $reseth = true ], int $stretch[, bool $ishtml = false ][, bool $autopadding = true ], float $maxh[, string $valign = 'T' ][, bool $fitcell = false ]) : int

$pdf->MultiCell($lagerortW, $lagerortH, $lagerortText, $showBorders, 'C', 
   false, 1, $lagerortX, $lagerortY, true, 0, false, true, 0, 'T', false);
// mb_convert_encoding($lagerortText,"UTF-8","ISO-8859-1")

   $pdf->SetFont('helvetica', '', $defaultFontSize);

###########################################################


// QR-Code

$lagerortZuNummer = array ("K" => 1,
"P-" => 2,
"B" => 3,
"D" => 4,
"G" => 5,
"R" => 7,
"W" => 8);


// echo $kistennummer."<br>";
$code="";
if (preg_match("/(.*)\-(.*)/",$kistennummer,$matches)){
   // echo "match";
   if (isset($lagerortZuNummer[$matches[1]])) {
      $code="0010".$lagerortZuNummer[$matches[1]]."0000".$matches[2];
   }
}

// print_r($matches)."<br>";
// echo "#".$code."#";
// die();

// QR-Code nur zeigen, wenn Code aus Titel ermittelt werden konnte
if ($code!="")
{
   // set style for QR Code
   $style = array(
      'border' => 0,
      'vpadding' => 'auto',
      'hpadding' => 'auto',
      'fgcolor' => array(0,0,0),
      'bgcolor' => false, //array(255,255,255)
      'module_width' => 1, // width of a single module in points
      'module_height' => 1 // height of a single module in points
   );
   // QRCODE,H : QR-CODE Best error correction
   $pdf->write2DBarcode("https://www.lippesola.de/code/".$code, 'QRCODE,H', $qrCodeX, $qrCodeY, $qrCodeW, $qrCodeH, $style, 'N');

}


###########################################################

// Trennline A5
// $pdf->Line(1,148,209,148);

###########################################################

// Tabelle mit Inhalt

$pdf->SetFont('helvetica', 'B', $inhaltTabelleFontSize);
$header = array('Bezeichnung', ' ', ' ');
$pdf->setXY($inhaltTabelleX,$inhaltTabelleY);

 // Header
 $w =$inhaltTabelleArray;
 $num_headers = count($header);
 for($i = 0; $i < $num_headers; ++$i) {
     $pdf->Cell($w[$i], $inhaltTabelleHeaderHeight, $header[$i], 'LRTB', 0, 'L', 0);
 }
 $pdf->Ln();
 $pdf->SetFont('helvetica', '', $defaultFontSize-2);
 // Data
 //foreach($inhaltsliste as $currentInhalt) {
for ($i=0;$i<$inhaltTabelleZeilen;$i++) {
   
    
   //$currentInhalt = mb_convert_encoding($currentInhalt ,"UTF-8","ISO-8859-1");
     $pdf->setX($marginLeftRight);
     $pdf->Cell($w[0], $inhaltTabelleCellHeight, "", 'LRTB', 0, 'L', 0); // hier war  $currentInhalt
     $pdf->Cell($w[1], $inhaltTabelleCellHeight, "", 'LRTB', 0, 'L', 0);
     $pdf->Cell($w[2], $inhaltTabelleCellHeight, "", 'LRTB', 0, 'R', 0);
     $pdf->Ln();
 }
 $pdf->SetFont('helvetica', '', $defaultFontSize);


###########################################################


   $lagerortToFillColor = array(
      "Rote Scheune" => array(255,0,0),
      "Wechselbrücke W-03 (Großes Zelt)" => array(0xcc,0,0x99),
      "Wechselbrücke W-02 (Küche)" => array(255,85,0),
      "Wechselbrücke W-01 (Springer)" => array(0xa6,0xa6,0xa6),
      "Gemeinde am Grasweg" => array(0,255,0),
      "FeG Extertal" => array(0,0,255)     
   );

   

// Hauptlagerort seitlich

$pdf->SetFont('helvetica', 'B', $hautplagerortFontSize);
$pdf->StartTransform();
// Rotate 20 degrees counter-clockwise centered by (70,110) which is the lower left corner of the rectangle
$pdf->Rotate(90, $hautplagerortX, $hautplagerortY);
// echo "*".$hauptlagerort."+<br><br>";
// print_r( $lagerortToFillColor[$hauptlagerort])."<br><br>";
// print_r($lagerortToFillColor);
// die();
$pdf->setFillColorArray($lagerortToFillColor[$hauptlagerort]);
$pdf->MultiCell($hautplagerortW, $hautplagerortH, trim($hauptlagerort), $showBorders, 'C', true, 1, $hautplagerortX, $hautplagerortY, true, 0, false, true, $hautplagerortH+1, 'M', false);
// mb_convert_encoding($hauptlagerort,"UTF-8","ISO-8859-1")
$pdf->setFillColor(255,255,255);
$pdf->StopTransform();
$pdf->SetFont('helvetica', 'B', $defaultFontSize);



###########################################################

// Bild von Kiste

// set JPEG quality
$pdf->setJPEGQuality(90);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Image method signature:
// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

// The '@' character is used to indicate that follows an image data stream and not an image file name
// Image example with resizing




// From binary data
$pdf->Image('@'.$photoData, $fotoX, $fotoY, $fotoW, $fotoH, 'JPG', '', '', true, 300, '', false, false, 1, false, false, false);


// from image file: $pdf->Image($imagePath, $fotoX, $fotoY, $fotoW, $fotoH, 'JPG', '', '', true, 300, '', false, false, 1, false, false, false);

// Other comment: $pdf->Image('@'.$imgdata, 92, 13, 75, 50, 'JPG', '', '', true, 300, '', false, false, 1, false, false, false);



###########################################################



// Sola-Logo
// Rect(float $x, float $y, float $w, float $h[, string $style = '' ][, array<string|int, mixed> $border_style = array() ][, array<string|int, mixed> $fill_color = array() ]) 
//$pdf->setFillColor(0,0,0);
//$pdf->Rect(175, 127, 30, 15);
//$pdf->MultiCell(30, 15, "", 1, 'C', true, 1, 175, 127, true, 0, false, true, 31, 'M', false);
//$pdf->setFillColor(255,255,255);
$pdf->Image(dirname(__FILE__)."/lib/sola-logo-fuer-inventarschild.jpg", $solaLogoX, $solaLogoY, $solaLogoW, $solaLogoH, 'JPG', '', '', true, 300, '', false, false, 0, false, false, false);



###########################################################



// Weitere Info-Texte

$pdf->SetFont('helvetica', '', $infoTexteFontSize);


$pdf->MultiCell($infoTexteW, $infoTexteH, $zusatztext , 
$showBorders, 'L', false, $marginLeftRight, $infoTexteX, $infoTexteY, true, 0, false, true, 0, 'T', false);
// mb_convert_encoding($zusatztext,"UTF-8","ISO-8859-1")
$pdf->SetFont('helvetica', '', $defaultFontSize);


###########################################################


//Close and output PDF document
$pdf->Output('Kistenschild '. $kistennummer.".pdf", 'I');
// mb_convert_encoding($kistennummer,"UTF-8","ISO-8859-1")
?>