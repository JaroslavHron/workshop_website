<?php

require_once('gp_signature.php'); // trida pro podepisovani a verifikaci
require_once('gp_confer.php');    // parametry specificke pro konferenci
require_once('gp_params.php');    // parametry specificke pro konferenci

function de($co) {
  echo "<pre>".print_r($co,true)."</pre>";
}

// ocisteni vstupni promenne od nezadoucich znaku (bezpecnost)

function bezpecne($var) {
   $var = htmlspecialchars($var);
   return($var);
}

// odstraneni nebezpecnych retezcu ze vstupnich parametru

foreach($_GET as $key => $val) { $_GET[$key] = bezpecne($val); }
foreach($_POST as $key => $val) { $_POST[$key] = bezpecne($val); }

// Vraci pole ($order, $URL), kde 
//      $order je naplnene pole z konfiguracnich konstant a promennych $_POST z formulare
//      $URL   je odkaz bud prazdny (pokud neni definovana ani 
//             promenna $_POST['AMOUNT']) ani konstanta CASTKA

function nastavPromenne($vs = null) {

  // podle konstant v konfiguracich se nastavi pole $order a odkaz $URL, na ktery smerovat objednavku 
  // pricemz $order['AMOUNT'] je definovano jen pokud:
  //   - je definovana promenna $_POST['CASTKA'] resp. $_POST['CASTKAJINA']
  //     (z formulare z predchoziho volani) nebo
  //   - je-li definovana konstanta CASTKA v konfiguracnim souboru konference gp_confer.php 
  // a pouze v tomto pripade je spravne vygenerovany DIGEST (a $URL smeruje na platebni branu, jinak
  // smeruje zpet na tento skript)

  // CASTKA je vzdy v KC
  if (isset($_POST['CASTKA']) && $_POST['CASTKA'] > 0) {
    $CASTKA = bezpecne($_POST['CASTKA']);
  } elseif (isset($_POST['CASTKA']) && $_POST['CASTKA'] == -1 && 
            isset($_POST['CASTKAJINA']) && $_POST['CASTKAJINA']) {
    $CASTKA = bezpecne($_POST['CASTKAJINA']);
  } elseif (defined('CASTKA')) {
    $CASTKA = CASTKA;
  }

  // Pozadavek a jeho podepsani - poradi polozek je:
  
  // MERCHANTNUMBER // string(10)
  // OPERATION      // string(20)
  // ORDERNUMBER    // number(15) - musi byt u nikatni
  // AMOUNT         // number(15)
  // CURRENCY       // number(3)
  // DEPOSITFLAG    // number(1)
  // MERORDERNUM    // number(30)
  // URL            // string(300)
  // DESCRIPTION    // string(255)
  // MD             // string(255)
  // DIGEST         // string()
  
  $order = array();
  
  define('GLUE', '|');      // oddelovac polozek pro podpis

  $Signature = new CSignature(PRIVATE_KEY, PRIVATE_KEY_PSW, PUBLIC_KEY);
  
  $poradove_cislo = sprintf("%09d", time() - 1647300000);  // generujeme 9 cislic
  $URL                     = "";
  // data cerpana z formulare v 1. kroku
  $order['MERCHANTNUMBER'] = MERCHANT_NUMBER;
  $order['OPERATION']      = GP_OPERATION;
  $order['ORDERNUMBER']    = CISLO_AKCE . $poradove_cislo; // max. 15 cislic
  if (isset($CASTKA)) {
    $order['AMOUNT']       = $CASTKA / GP_CURRENCY_UNIT;
    $URL                   = GP_URL;
  }
  $order['CURRENCY']       = GP_CURRENCY_CZK;
  $order['DEPOSITFLAG']    = ((GP_DEPOSIT_FLAG) ? 1 : 0);
  $order['MERORDERNUM']    = CISLO_AKCE . (($vs) ? sprintf("%09d", $vs) : $poradove_cislo);
  $order['URL']            = URL_RETURN_FROM_GP;
  $order_string            = join(GLUE, $order);
  $order['DIGEST']         = $Signature->sign($order_string);

  // toto nechci odesilat na branu - jsou s tim potize, neni soucasti podpisu
  $order['DESCRIPTION']    = KONFER . " - " . $_POST['name'];
  $order['MD']             = KONFER;

  // kdyz mame vygenerovany podpis z polozek vys, muzeme pridat vlastni pro formular:
  if (isset($CASTKA)) {
    $order['CASTKA']       = $CASTKA;
  }

  return(Array($order, $URL));

}

//
// funkce evidence plateb
// $request    - pole dat odesilanych do GP nebo null (pro evidenci zacatku plateb)
// $response   - pole dat ziskanych z GP (pro evidenci prubehu platby)
// $mailAddr   - emailove adresy, na ktere posilat info o probihajici platbe
// $urlPrehled - URL stranky, na ktere lze ziskat prehled plateb (pise se do emailu)
// FPLATBY     - konstanta obsahujici nazev souboru, kam se zapisuji informace o platbach
//
function evidence($request, $response, $mailAddr, $urlPrehled) {
  // ulozeni dat do souboru:
  date_default_timezone_set('Europe/Prague');
  $line = "DATE=" . Date("Y-m-d H:i:s",time()) . "; ";
  $body = "";
  $body = "Prehled plateb je na adrese $urlPrehled\n\n";
  if ($request) {
    $line .= "PLATBA=ZAC; ";
    foreach($request as $key => $val) { 
      if (preg_match("/,$key,/",",DIGEST,URL,")) continue; // toto neevidujeme
      $line .= "$key=$val; "; 
      $body .= sprintf("%-20s: %s\n",$key,$val);
      if ($key == "ORDERNUMBER") $subj = "GP:IN  - $val";
      if ($key == "DESCRIPTION") $subj .= " $val";
    }
  } elseif ($response) {
    $line .= "PLATBA=KON; ";
    foreach($response as $key => $val) {
      $line .= "$key=$val; "; 
      $body .= sprintf("%-20s: %s\n",$key,$val);
      if ($key == "ORDERNUMBER") $subj = "GP:OUT - $val";
    }
  }
  $body .= sprintf("%-20s: %s\n","Remote IP",$_SERVER['REMOTE_ADDR']);
  if (!isset($subj)) $subj = "GP - platby konference"; // default predmet
  $hlavicka = "From: confer-noreply@karlin.mff.cuni.cz\n";
  $fd = fopen(FPLATBY, "a");
  if ($fd) {
    fwrite($fd,"$line\n");
    fclose($fd);
  }
  if ($mailAddr) {
    $subj = INFOPREF . $subj;
    mail($mailAddr, $subj, $body, $hlavicka);
  }
  return;
}

//
// zpracovani dat po presmerovani z GP (nacteni a overeni podpisu)
// vraci:
//   $digestok = 1  - podpis zakladni casti je korektni
//   $digest1ok = 1 - podpis rozsirene casti je korektni
//   $response      - pole vracenych hodnot z banky, klice jsou: 
//     OPERATION
//     ORDERNUMBER
//     MERORDERNUM
//     MD
//     PRCODE
//     SRCODE
//     RESULTTEXT
//     DETAILS
//     USERPARAM1
//     ADDINFO
//
function odpovedBanky() {

   // nacteni promennych z volani
   $data = "";
   $pipe = false;
   
   $operace        = "";
   $ordernumber    = "";
   $merordernumber = "";  $vssend=false;
   $prcode         = "";
   $srcode         = "";
   $md             = "";  $mdsend=false;
   $resulttext     = "";
   $details        = "";
   $addinfo        = "";
   
   foreach(Array("operace"    =>"OPERATION",
                 "ordernumber"=>"ORDERNUMBER",
                 "merordernumber"=>"MERORDERNUM", 
                 "md"         =>"MD", 
                 "prcode"     =>"PRCODE", 
                 "srcode"     =>"SRCODE", 
                 "resulttext" =>"RESULTTEXT", 
                 "details"    =>"DETAILS", 
                 "userparam1" =>"USERPARAM1", 
                 "addinfo"    =>"ADDINFO", 
                ) as $key => $name) {
     if (isset($_GET[$name])) { 
       if ($pipe) {$data.="|";}
       $data.=$_GET[$name]; 
       $pipe=true; 
       ${$key} = $_GET[$name];
       $response[$name] = $_GET[$name];
     } else if (isset($_POST[$name])) {
       if ($pipe) {$data.="|";}
       $data.=$_POST[$name]; 
       $pipe=true; 
       ${$key} = $_POST[$name];
       $response[$name] = $_POST[$name];
     } else if (isset(${$name})) {
       if ($pipe) {$data.="|";}
       $data.=${$name};
       $pipe=true;
       $operace = ${$name};
       $response[$name] = ${$name};
     }
   }
   if (isset($merordernumber) && $merordernumber) $vssend=true;
   if (isset($md) && $md)                         $mdsend=true;
   
   $digest = "";
   if      (isset($_GET["DIGEST"]))   $digest = $_GET["DIGEST"];
   else if (isset($_POST["DIGEST"]))  $digest = $_POST["DIGEST"];
   else if (isset($DIGEST))           $digest = $DIGEST;
   $digest1 = "";
   if      (isset($_GET["DIGEST1"]))  $digest1 = $_GET["DIGEST1"];
   else if (isset($_POST["DIGEST1"])) $digest1 = $_POST["DIGEST1"];
   else if (isset($DIGEST1))          $digest1 = $DIGEST1;
   
   // overeni korektnosti zaslanych dat (podpisu)
  
   $sign = new CSignature(PRIVATE_KEY, PRIVATE_KEY_PSW, PUBLIC_GP_KEY);
   
   $digestok = 0;
   if ($sign->verify($data, $digest))  $digestok = 1;
   $digest1ok = 0;
   $dataorig = $data;
   $data .= "|" . MERCHANT_NUMBER;
   if ($sign->verify($data, $digest1)) $digest1ok = 1;

   evidence(null, $response, MAILADDRINFO, URLPREHLED); // evidence konec platby

   return(Array($digestok, $digest1ok, $response));
}

// vypis souboru $soubor az po radek obsahujici vyraz $vzor (vcetne) a vlozenim makra pro jquery

function zacatekSouboru($soubor, $vzor, $vlozitJS = 0) {

   $radky = explode("\n", file_get_contents($soubor));
   $out = "";
   for ($i = 0; $i < Count($radky); $i++ ) {
       $out .= "$radky[$i]\n";
       if ($vlozitJS && preg_match("/$vzor/", $radky[$i])) {
           // vlozime do hlavicky volani javascriptu pro jquery
           $out .= "   <script type=\"text/javascript\" src=\"jquery-3.2.1.min.js\"></script>\n";
       }
       if (preg_match("/div.*id=.*main/",$radky[$i])) break; // tento je posledni
   }
   if (!PRODUKCNI) {
      $out .= "<div style='width:550px;background-color:yellow;text-align:center;'>Testing mode</div>";
   }
   return($out);
}

// vytvorime kratsi otisk md5 souctu, at neni odkaz v dopise tak dlouhy
function md5s($co) {
   return(substr(md5($co),5,10));
}

// generovani ci overovani naseho peska

function pesek($cmp = null) {
   $pesek = ($cmp) ? substr($cmp,12,3).substr($cmp,29) : substr(microtime(),2,6);
   $zac = substr($pesek,0,3);
   $kon = substr($pesek,3,3);
   $md5 = md5("$zac-$pesek");
   $pesek = substr($md5,0,12).$zac.substr($md5,18).$kon;
   if ($cmp) { 
      if ($cmp == $pesek) return("1");  // je ok
      else return("0");                 // neni ok
   }
   return($pesek);                      // vracim vygenerovany
}



?>
