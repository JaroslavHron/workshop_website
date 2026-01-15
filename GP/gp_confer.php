<?php
// PRODUKCNI   1 - pro pouziti produkcnich klicu a brany, 
//             0 - pro pouziti testovacich klicu a brany
define("PRODUKCNI", 1);     

// carkama oddeleny seznam adres, komu posilat info o platbach
define ("MAILADDRINFO",    "ems.masc@karlin.mff.cuni.cz,ulrych@karlin.mff.cuni.cz,ptichy@karlin.mff.cuni.cz,kasparova@karlin.mff.cuni.cz");
//define ("MAILADDRINFO",    "ulrych@karlin.mff.cuni.cz");

// carkama oddeleny seznam adres, komu posilat email o registracich - v gp* se nepouziva
define ("MAILADDRREG",    "ems.masc@karlin.mff.cuni.cz,ulrych@karlin.mff.cuni.cz,ptichy@karlin.mff.cuni.cz");
//define ("MAILADDRREG",    "ulrych@karlin.mff.cuni.cz");

// variabilni castku zde zadat pevne, nebo lze resit i pres formular gp_order a zde zakomentovat
define ("CASTKA", "13500");   
//define ("CASTKA", "1");     // castka pouzivana na testovani plateb (zakomentovat predchozi, odkomentovat tento radek)

// informace o konferenci 
define('CISLO_AKCE',624042); // soucast ORDERNUMBER - max. 6 cislic viz fce nastavPromenne()

// idenfikator eshopu v paltebni brane
define ("MERCHANT_NUMBER", "9675740009");                     // cislo obchodnika pridelene bankou EMS

/////////////////////// nastaveni niz nebyva potreba upravovat

if (PRODUKCNI) {  // 0 = testovani, 1 = produkcni
   define("INFOPREF", ""); 
} else   { 
   define("INFOPREF", "TEST ");  // pro info v mailech atp.
}

error_reporting(0);

// dalsi informace o konferenci 
define("KONFER",          "ems-masc");     // nazev konference - odkazy na web
define("DATA_DIR",        "/srv/beegfs/sekce23/data/" . KONFER);   // kam se ukladaji ruzna pracovni data

if (PRODUKCNI) define("FUCASTNICI",   DATA_DIR . "/ucastnici-prod");  // informace o ucastnicich
else           define("FUCASTNICI",   DATA_DIR . "/ucastnici-test");  // informace o ucastnicich

// kde ziskat prehled o platbach a registracich
define ("URLPREHLED",      "https://".KONFER.".karlin.mff.cuni.cz/gp_overview.php"); 

// souboru pro evidenci registraci a plateb 
if (PRODUKCNI) define ("FPLATBY",         DATA_DIR . "/platby-prod.txt");
else           define ("FPLATBY",         DATA_DIR . "/platby-test.txt");

//path to digest file, PHP function strftime() is used, value @ORDERID@ will be replaced by ORDERNUMBER
define ("SAVE_SIGNATURE_FILE_PATH", DATA_DIR . "/gp_signs/signature.@ORDERID@.%Y%m%d_%H%M%S.sign");

//path to digest URLEncoded file, PHP function strftime() is used, value @ORDERID@ will be replaced by ORDERNUMBER
define ("SAVE_SIGNATURE_FILE_PATH_ENCODED", DATA_DIR . "/gp_signs/signatureEnc.@ORDERID@.%Y%m%d_%H%M%S.sign");

// Odkazy po presmerovani z platebni brany po (ne)provedene platbe

// define ("URL_RESPONSE_OK",        "gp_platbaOK.php"); // platba probehla korektne
// define ("URL_RESPONSE_KO_DIGEST", "gp_platbaKO.php"); // nesouhlasi podpisy - NEMA NASTAT
// define ("URL_RESPONSE_KO_OBECNE", "gp_platbaKO.php"); // jiny problem pri platbe

define ("URL_RESPONSE_OK",        "index.php?file=paymentOK"); // platba probehla korektne
define ("URL_RESPONSE_KO_DIGEST", "index.php?file=paymentKO"); // nesouhlasi podpisy - NEMA NASTAT
define ("URL_RESPONSE_KO_OBECNE", "index.php?file=paymentKO"); // jiny problem pri platbe

////////
// radky niz by nemely byt potreba menit (patri po overeni presunout do gp_params.php)

if (PRODUKCNI) {
   // lze stahnout v https://portal.gpwebpay.com/portal/ => key management => vybrat svuj eshop => backup
   define ("PRIVATE_KEY",   DATA_DIR . "/gp_keys-prod/gpwebpay-pvk_9675740009.key");    //cesta k soukromemu produkcnimu klici obchodnika
   define ("PRIVATE_KEY_PSW", "Konferen4n9 Heslo.");                                    //heslo k soukromemu produkcnimu klici obchodnika
   define ("PUBLIC_GP_KEY", DATA_DIR . "/gp_keys-prod/gpe.signing_prod.pem");  //cesta k verejnemu klici platebni brany GPE
} else {
   // lze stahnout v https://test.portal.gpwebpay.com/portal/ => key management => vybrat svuj eshop => backup
   define ("PRIVATE_KEY",   DATA_DIR . "/gp_keys-test/gpwebpay-pvk_9675740009-test.key");    //cesta k soukromemu produkcnimu klici obchodnika
   define ("PRIVATE_KEY_PSW", "P59stup.");                                  //heslo k soukromemu testovacimu klici obchodnika
   define ("PUBLIC_GP_KEY", DATA_DIR . "/gp_keys-test/gpe.signing_test.pem");  //cesta k verejnemu klici platebni brany GPE
}
define ("URL", "https://".KONFER.".karlin.mff.cuni.cz/gp_response.php");

$SEP = "XSEPX";                       // separator polozek v registracnim radku

// ostatni potrebne funkce a knihovny

require('gp_signature.php'); // trida pro podepisovani a verifikaci
require('gp_params.php');    // parametry specificke pro platebni branu
require('gp_functions.php'); // vlastni podpurne funkce

// vypis parametru, pokud je volan tento skript GET['debug']
function vypisNeco($neco, $prom, $popis) { echo "<tr><td>$prom</td><td>$popis</td><td>$neco</td></tr>"; }
if (!empty($_GET['debug']) && preg_match("/gp_confer/",$_SERVER['SCRIPT_NAME']) &&
    (preg_match("/^10.113./",$_SERVER['REMOTE_ADDR']) || preg_match("/^195.113.3/",$_SERVER['REMOTE_ADDR']))) {
   echo "<table border=1>";
   vypisNeco(PRODUKCNI,"PRODUKCNI", "pro pouziti produkcnich klicu");
   vypisNeco(INFOPREF,"INFOPREF", "INFOPREF");
   vypisNeco(MAILADDRINFO,"MAILADDRINFO", "carkama oddeleny seznam adres, komu posilat info o platbach");
   vypisNeco(MAILADDRREG,"MAILADDRREG", "carkama oddeleny seznam adres, komu posilat email o registracich");
   vypisNeco(CISLO_AKCE,"CISLO_AKCE", "soucast ORDERNUMBER - max. 6 cislic viz fce nastavPromenne()");
   vypisNeco(KONFER,"KONFER", "nazev konference - odkazy na web");
   vypisNeco(DATA_DIR,"DATA_DIR", "kam se ukladaji ruzna pracovni data");
   vypisNeco(FUCASTNICI,"FUCASTNICI", "informace o ucastnicich");
   vypisNeco(CASTKA,"CASTKA", "Castka");
   vypisNeco(URLPREHLED,"URLPREHLED", "kde ziskat prehled o platbach a registracich");
   vypisNeco(FPLATBY,"FPLATBY", "souboru pro evidenci registraci a plateb");
   vypisNeco(SAVE_SIGNATURE_FILE_PATH,"SAVE_SIGNATURE_FILE_PATH", "path to digest file, PHP function strftime() is used, value @ORDERID@ will be replaced by ORDERNUMBER");
   vypisNeco(SAVE_SIGNATURE_FILE_PATH_ENCODED,"SAVE_SIGNATURE_FILE_PATH_ENCODED", "path to digest URLEncoded file, PHP function strftime() is used, value @ORDERID@ will be replaced by ORDERNUMBER");
   vypisNeco(URL_RETURN_FROM_GP,"URL_RETURN_FROM_GP", "Navrat z GP");
   vypisNeco(URL_RESPONSE_OK,"URL_RESPONSE_OK", "platba probehla korektne");
   vypisNeco(URL_RESPONSE_KO_DIGEST,"URL_RESPONSE_KO_DIGEST", "nesouhlasi podpisy - NEMA NASTAT");
   vypisNeco(URL_RESPONSE_KO_OBECNE,"URL_RESPONSE_KO_OBECNE", "jiny problem pri platbe");
   vypisNeco(PRIVATE_KEY,"PRIVATE_KEY", "cesta k soukromemu testovacimu klici obchodnika");
   vypisNeco(PUBLIC_KEY,"PUBLIC_KEY", "cesta k verejnemu testovacimu klici obchodnika");
   vypisNeco(PUBLIC_GP_KEY,"PUBLIC_GP_KEY", "cesta k verejnemu klici GPE");
   vypisNeco(MERCHANT_NUMBER,"MERCHANT_NUMBER","cislo obchodnika pridelene bankou EMS");
   vypisNeco(URL,"URL","Navrat z platebni brany");
   echo "</table>";
   function mameHo($cesta,$jm) { if (file_exists($cesta)) { echo "OK"; } else { echo "KO"; }; echo " $cesta $jm<br/>\n"; }
   mameHo(DATA_DIR,"DATA_DIR");
   mameHo(FUCASTNICI,"FUCASTNICI");
   mameHo(FPLATBY,"FPLATBY");
   mameHo(URL_RESPONSE_OK,"URL_RESPONSE_OK");
   mameHo(URL_RESPONSE_KO_DIGEST,"URL_RESPONSE_KO_DIGEST");
   mameHo(URL_RESPONSE_KO_OBECNE,"URL_RESPONSE_KO_OBECNE");
   mameHo(PRIVATE_KEY,"PRIVATE_KEY");
   mameHo(PUBLIC_KEY,"PUBLIC_KEY");
   mameHo(PUBLIC_GP_KEY,"PUBLIC_GP_KEY");
echo FUCASTNICI;
}

?>
