<?php

$clientAddr = $_SERVER['REMOTE_ADDR'];
if (!preg_match("/^(10\.|195\.113\.3[0-1]|195\.113\.26)/",$clientAddr)) { return; } // jen z Karlina

require("gp_confer.php");

$handle = fopen(FPLATBY, "r");
$uc  = Array();   // ucastnici
$hl = Array();   // hlavicka (nazvy polozek)
$on2uid = Array(); // pole prevodu OERDERNUMBER na $uid
$pl = Array();   // platby
$povolene = ["DATE","PLATBA","ORDERNUMBER","AMOUNT","CASTKA","name","VATRN","PRCODE","SRCODE"];
if ($handle) {
    $lineno = 0;
    while (($line = fgets($handle)) !== false) {
        $lineno++;
        if (preg_match("/^uid::/",$line)) {
           // radek s prihlasenim ucastnika
           $slozky = explode("$SEP",$line);
           foreach($slozky as $pol) {
               if (preg_match("/^([^:]+):(.*)/",$pol,$s)) {
                  $key = $s[1];
                  if ($s[1] == "uid") {
                     $uid = $s[2];
                  }
                  if (isset($uc[$uid][$key])) $uc[$uid][$key] .= " ||| " .$s[2];
                  else                        $uc[$uid][$key] = $s[2];
                  $hl[$key] = $key;
               }
           } 
        } elseif (preg_match("/^DATE.*PLATBA=([^;]+).*ORDERNUMBER=([0-9]+);/",$line,$s)) {
           $platba = $s[1];
           $on = $s[2];
           if (preg_match("/VS= *([0-9]+);/",$line,$s)) {
               // radek s platbou - zacatek
               $uid = $s[1];
               $on2uid[$on] = $uid;         // pro zpetny prevod
           } elseif (preg_match("/ORDERNUMBER=([0-9]+);/",$line,$s)) {
               $uid = $s[1];
           } elseif (preg_match("/VATRN= *([^;]+) *;/",$line,$s)) {
               // radek s platbou - zacatek
               $uid = $s[1];
               $on2uid[$on] = $uid;         // pro zpetny prevod
           } elseif (isset($on2uid[$on])) {
               $uid = $on2uid[$on];
           } else {
               echo "BNEVYHODNOCENO:l.$lineno:$line<br/>";
               continue;
           }
           $slozky = explode(";",$line);
           foreach($slozky as $pol) {
               if (preg_match("/^([^=]+)=(.*)/",$pol,$s)) {
                  $key = trim($s[1]);
                  if (in_array($key,$povolene)) {
                     $uc[$uid][$platba][$on][$key] = $s[2];
                     $hl[$key] = $key;
                  }
               }
           } 
        } else {
           echo "ANEVYHODNOCENO1:l.$lineno:$line<br/>";
        }
    }
    fclose($handle);
//    de($hl); 
//de($uc); 
//de($hlp); de($on2uid); de($uid2on); de($pl);
} else {
    echo "Nelze cist soubor " . FPLATBY;
} 


$poleZahlavi = Array("VS","Conf.","Name","Email","Arrival","Departure","Amount","Payment","Bank VS","Participant","Student","GDPR","Talk","Address","Room","Roommate","Comment");
$poleZahlavi = $povolene;
$poleZahlavi = ["ORDERNUMBER", "DATE","CASTKA","NAME","VATRN","DATE","PRCODE","SRCODE"];

$csv = "";
$html = "<table border='1'>";
if (!PRODUKCNI) {
   $html .= "<tr><th colspan=14 style='background-color:yellow'>TESTOVACÍ</th></tr>";
}
$html .= "<tr><th></th><th colspan=4>Zahájení platby</th><th colspan=3>Dokončení platby</th></tr>";
$html .= "<tr><th>";
$html .= join("</th><th>",$poleZahlavi);
$html .= "</th></tr>";
foreach($uc as $vs => $u) {
    $ZAC = $u['ZAC'][$vs];
    $radek = [];
    $radek[] = $vs;
    $radek[] = $ZAC['DATE'];
    $radek[] = $ZAC['CASTKA'];
    $radek[] = $ZAC['name'];
    $radek[] = $ZAC['VATRN'];
    if (!empty($u['KON'])) {
       $KON = $u['KON'][$vs];
       $radek[] = $KON['DATE'];
       if ($KON['PRCODE'] == 0 && $KON['SRCODE'] == 0) {
          $html .= "<tr class='ok'><td>" . join("</td><td>",$radek);
          $html .=  "</td><td colspan=2>Zaplaceno</td></tr>\n";
          $radek[] = "OK";
       } else {
          $radek[] = $KON['PRCODE'];
          $radek[] = $KON['SRCODE'];
          $html .= "<tr><td>" . join("</td><td>",$radek) . "</td></tr>\n";
          $radek[] = "KO";
       }
    } else {
       $radek[] = "";
       $radek[] = "";
       $radek[] = "";
       $html .= "<tr><td>" . join("</td><td>",$radek) . "</td></tr>\n";
    }
   $csv .= '"' . join('";"',$radek) . '"' . "\n";
}
if (0) {
    $conf = isset($u['confirmation']) ? "Yes" : "No";
    $castka = "";
    $stav = "";
    $bon  = "";
    if (isset($u['ZAC'])) {
       foreach($u['ZAC'] as $on => $pole) {
          $stav   .= (($castka) ? "+" : "") . "Z";  // zacatek operace
          $bon    .= (($castka) ? "<br/>" : "") . $on;
          $stav   .= (isset($u['KON'][$on]['RESULTTEXT'])) ? $u['KON'][$on]['RESULTTEXT'] : ""; // konec, existuje-li
          $castka .= (($castka) ? "+" : "") . ($pole['AMOUNT'] / 100);
       }
    }
    $poleRadku = Array($vs,$conf,
         "$u[last] $u[first]", $u['email'],
         $u['arrival'],$u['departure'],
         $castka, $stav,$bon,
         $u['participant'],
         $u['student'],$u['gdpr'],$u['talk'],
         "$u[affiliation], $u[address], $u[country]",
         $u['room'],$u['roommate'],
         $u['comments']
        );
    $html .= "<tr><td>" . join("</td><td>", $poleRadku) . "</td></tr>";
    $csvRadek = '"' . join('";"', $poleRadku) . '"';
    $csvRadek = preg_replace("/[\n\r\+]/"," ",$csvRadek);
    if (!preg_match("/ulrych@karlin.mff.cuni.cz/",$csvRadek)) {
        $csv .= preg_replace("/<br\/>/"," ",$csvRadek) . "\n";
    }
}

$html .= "</table>";

echo $html;

echo "<style> .ok { background-color:#0f0 } </style>\n";

echo "<pre>$csv</pre>";
?>


