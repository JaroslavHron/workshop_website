<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></meta>
<!--link rel="stylesheet" href="document.css" type="text/css"></link-->
<title>EMS-MASC Kacov 2024 - payment - Order number <?php echo $order['ORDERNUMBER']; ?></title>
</head>

<body>

<script>
function nastaveni() {
  var el1 = document.getElementById('neprov');
  var el2 = document.getElementById('nedok');
  window.location.replace("?neprov="+el1.checked+"&nedok="+el2.checked);
}
</script>

<?php 
if (isset($_GET['neprov'])) $neprov = $_GET['neprov']; else $neprov = "false";
if (isset($_GET['nedok']))  $nedok  = $_GET['nedok'];  else $nedok  = "false";
?>

Datum - zacatku (vyplneni formulare a presmerovani na banku) a konce transakce (transakce vyhodnocena bankou), <br/>
Platba - castka, Zacata/uKoncena transkace,<br/>
Vysledek - OK - transakce probehla, <br/>
Ord - vnitrni identifikator platby<br/>
Zobrazit i 
<input type='checkbox' id='neprov' onclick='nastaveni();'
       <?php if ($neprov == "true") echo "checked='checked'";?>/> neprovedene platby
<input type='checkbox' id='nedok' onclick='nastaveni();'
       <?php if ($nedok == "true") echo "checked='checked'";?>/> nedokoncene platby<br/>


<?php
require('gp_params.php');    // parametry pro komunikaci, podepisovani, testovani a dump
require('gp_confer.php');    // parametry pro komunikaci, podepisovani, testovani a dump

$rows = file(FPLATBY);
print_r(FPLATBY);
foreach($rows as $row) {
  // parsujeme radek podle stredniku
  $pole = explode("; ",$row);
  unset($zaznam); // kazdy radek je samostatne
  foreach($pole as $par) {
    if (preg_match("/^([^=]+)=(.*)/",$par,$s)) {
      $key = $s[1];
      $val = $s[2];
      $zaznam[$key] = $val;
    }
  }
  // zpracovani dvojic
  $ordnumber = $zaznam['MERORDERNUM'];
  if ($zaznam['PLATBA'] == 'Z') $zaznamy[$ordnumber]['PLATBA_Z'] = "Z"; // odeslani do banky
  if ($zaznam['PLATBA'] == 'K') $zaznamy[$ordnumber]['PLATBA_K'] = "K"; // zpracovani z banky
  if (isset($zaznam['PRCODE']) && $zaznam['PRCODE'] == 0) $zaznamy[$ordnumber]['ZAPLACENO'] = "OK"; //
  if (isset($zaznam['RESULTTEXT']))    $zaznamy[$ordnumber]['RESULT'] = $zaznam['RESULTTEXT'];
  if (isset($zaznam['name']))          $zaznamy[$ordnumber]['NAME'] = $zaznam['name'];
  if (isset($zaznam['AMOUNT']))        $zaznamy[$ordnumber]['AMOUNT'] = $zaznam['AMOUNT'];
  if (isset($zaznam['VATRN']))         $zaznamy[$ordnumber]['VATRN'] = $zaznam['VATRN'];
  if (isset($zaznam['DATE']) && $zaznam['PLATBA'] == 'Z')    $zaznamy[$ordnumber]['DATE_Z'] = $zaznam['DATE'];
  if (isset($zaznam['DATE']) && $zaznam['PLATBA'] == 'K')    $zaznamy[$ordnumber]['DATE_K'] = $zaznam['DATE'];
}

echo "<table border='1'>
      <tr>
          <th>Datum</th>
          <th>Platba</th>
          <th>OK</th>
          <th>Jmeno</th>
          <th>Adresa</th>
          <th>Vysledek</th>
          <th>Ord</th>
      </tr>";
  foreach($zaznamy as $key => $z) {
    foreach(Array("PLATBA_Z","PLATBA_K","ZAPLACENO","RESULT","NAME","AMOUNT","REGEMAIL","VATRN","DATE_Z","DATE_K") as $k) 
    	if (isset($z[$k])) ${$k} = $z[$k]; else ${$k} = "";
    $AMOUNT = $AMOUNT / 100; // prevod z haleru na koruny
    if ($neprov == "false" && $RESULT != "OK") continue;
    if ($nedok == "false" &&  !$PLATBA_K) continue;
    echo "<tr>";
    echo "<td style='font-size:9px;white-space:nowrap'>$DATE_Z<br/>$DATE_K</td>\n";
    echo "<td style='white-space:nowrap'>$AMOUNT $PLATBA_Z/$PLATBA_K</td>\n";
    echo "<td>$ZAPLACENO</td>\n";
    echo "<td>$NAME</td>\n";
    echo "<td>$VATRN</td>\n";
    echo "<td style='font-size:9px'>$RESULT</td>\n";
    echo "<td>$key</td>\n";
    echo "</tr>";
  }
?>

