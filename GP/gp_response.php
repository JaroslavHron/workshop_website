<?php 
// skript pro prijem informaci o potvrzene nebo chybove transakci
// z platebni brany zobrazeni informace uzivatele - viz konec skriptu

require('gp_confer.php');

List($digestok, $digest1ok, $response) = odpovedBanky();

// Vyhodnoceni a zpracovani odpovedi masc a maff se lisi nastavenim $servername
// Redirect je vhodny jednak kvuli oddeleni kodu a jednak kvuli nemoznosti udelat reload stranky

if ($digestok && $digest1ok && $response['PRCODE'] == "0" && $response['SRCODE'] == "0") { 
  // platba probehla ok
  header("Location: " . URL_RESPONSE_OK);
} elseif (!$digestok || !$digest1ok) {
  // neoverene podpisy - nema nastat 
  $add = "dg=$digestok&dg1=$digest1ok";
  header("Location: " . URL_RESPONSE_KO_DIGEST . "&$add");
} else {    
  // jine problemy v bance
  $add = "pc=" . $response['PRCODE'] . "&sc=" . $response['SRCODE'];
  header("Location: " . URL_RESPONSE_KO_OBECNE . "&$add");
}

exit; // protoze vzdy je pred tim presmerovani

