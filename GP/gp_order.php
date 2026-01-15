<?php
require('gp_confer.php');   

//

/////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////
////////// ZACATEK CASTI, JEJIZ ZMENA MUZE ZNAMENAT NEFUNKCNOST!!!  /////////////

// Naplnime seznam polozek pro formular odesilany zpet do gp_order.php
//  nebo na platebni branu (podle vracene $URL)
$vs = (isset($_POST['VS'])) ? $_POST['VS'] : null;
List($order, $URL) = nastavPromenne($vs);

//de($URL);
//de($order); de($_POST);
// pole $order ma nyni tyto klice, ktere lze pouzit ve formulari (krome AMOUNT):
// MERCHANTNUMBER // string(10)
// OPERATION      // string(20)
// ORDERNUMBER    // number(15) - musi byt unikatni
// AMOUNT         // number(15)
// CURRENCY       // number(3)
// DEPOSITFLAG    // number(1)
// MERORDERNUM    // number(30)
// URL            // string(300)
// DESCRIPTION    // string(255)
// MD             // string(255)
// DIGEST         // string()

// $order['AMOUNT'] je definovano jen pokud:
//   - je definovana promenna $_POST['CASTKA'] nebo $_POST['CASTKAJINA'] (z formulare z predchoziho volani) nebo
//   - je-li definovana konstanta CASTKA v konfiguracnim souboru konference gp_confer.php
// a pouze v tomto pripade je spravne vygenerovany DIGEST (a $URL smeruje na platebni branu, jinak
// smeruje zpet na tento skript)

/////////// KONEC CASTI, JEJIZ ZMENA MUZE ZNAMENAT NEFUNKCNOST!!! ///////////////
/////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////
//
// formular se sumarizaci platby a presmerovanim do GP
// promenne predchoziho gp_payment.php jsou: name,email,price,VATRN

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></meta>
<link rel="stylesheet" href="document.css" type="text/css"></link>
<link rel="stylesheet" href="lib-menu.css" type="text/css"></link>
<title>EMS-MASC Kacov 2025 - payment - Order number <?php echo $order['ORDERNUMBER']; ?></title>
</head>

<body>
<center>
<div class="normalbox">
  <div class="normal">
    <img border="0" src="images/banner.png" > <br>
  </div>
</div>
<br><br>

<form  action="<?php echo $URL;?>" method="post">  <!-- MUSI BYT ZDE -->

<table width="766" border="0" cellpadding="0" cellspacing="0">
<tr><td width="20%" valign="top">

<div class="normalbox"><div class="normal"><div align=center>

<table width="550" bgcolor="#b5bdce" border=0 cellpadding=0 cellspacing=0>
<tr><td align=center>    
  <table width="550" border=0 cellpadding=2 cellspacing=0>
  <tr>
   <td colspan="2" align=center bgcolor="#929bad"><font size=2><b>Order summary</b></font></td>   
  </tr>
  <tr> <td> &nbsp; </td>
       <td> &nbsp; </td> </tr>
  <tr> <th align=right> Order number: </th> 
       <td align=left> <?php echo $order['ORDERNUMBER']; ?> </td>
  </tr> 
  <tr> <th align=right> Name: </th> 
       <td align=left> <?php echo $_POST['name']; ?> </td>
  </tr> 
  <tr> <th align=right> Amount: </th> 
       <td align=left> <?php echo $order['CASTKA']; ?> CZK</td>
  </tr>' 
  <tr> <th align=right> Invoice address: </th> 
       <td align=left> <?php echo $_POST['VATRN']; ?></td>
  </tr>    
  <tr> <td> &nbsp; </td>
       <td> &nbsp; </td> 
  </tr>
  
  <tr><td colspan="2" align=center>After clicking on "Payment",<br/>
           you will be redirected to the bank secure payment system.</td>
  </tr>

  <tr> <td> &nbsp; </td>
       <td> &nbsp; </td> 
  </tr>

  <!-- NASLEDUJE CAST ODESILAJICI DATA DO GP - NEEDITOVAT --> 
  <tr><td colspan="2" align=center>
  <input type="hidden" name="MERCHANTNUMBER" value="<?php echo $order['MERCHANTNUMBER']; ?>" />
  <input type="hidden" name="OPERATION" value="<?php echo $order['OPERATION']; ?>" />
  <input type="hidden" name="ORDERNUMBER" value="<?php echo $order['ORDERNUMBER']; ?>" />
  <input type="hidden" name="AMOUNT" value="<?php echo $order['AMOUNT']; ?>" />
  <input type="hidden" name="CURRENCY" value="<?php echo $order['CURRENCY']; ?>" />
  <input type="hidden" name="DEPOSITFLAG" value="<?php echo $order['DEPOSITFLAG']; ?>" />
  <input type="hidden" name="MERORDERNUM" value="<?php echo $order['MERORDERNUM']; ?>" />
  <!--input type="hidden" name="URL" value="<?php echo $order['URL']; ?>" /-->
  <input type="hidden" name="URL" value="https://essam-masc.karlin.mff.cuni.cz/gp_response.php" /> <!-- url napevno registrovane v GP, nelze svevolne menit, ani dodat ?.... -->
<!--
  <input type="hidden" name="DESCRIPTION" value="<?php echo $order['DESCRIPTION']; ?>" />
  <input type="hidden" name="MD" value="<?php echo $order['MD']; ?>" />
-->
 <?php if (!isset($CASTKA) || 1) {
     foreach(Array("name","email","VATRN","VS") as $key) {
         if (isset($_POST[$key])) {
             echo "<input type='hidden' name='$key' value=\"$_POST[$key]\"/>\n";
         }
     }
   }
  ?>
  <input type="hidden" name="DIGEST" value="<?php echo $order['DIGEST']; ?>" />
  <input type="submit" value="Payment" />
      </td>
  </tr>
  <tr> <td> &nbsp; </td>
       <td> &nbsp; </td> 
  </tr>

  </table>

</td>
<tr> 
</table>

</div></div></div>
</td></tr></table>
</form>

</center>

<?php   // NEEDITOVAT !!!
////////////////////////////////////////////////
////////////////////////////////////////////////
////////////////////////////////////////////////
//
// Kontrolni vypis
//
// if (gp_dump)
// {
//   //printf("POST: "); print_r($_POST); printf("<br/>");
//   echo "POST: <pre>" . print_r($_POST, true) . "</pre><br/>";
//   echo "ORDER: <pre>" . print_r($order, true) . "</pre><br/>";
//   echo "ORDER string: <pre>" . print_r($order_string, true) . "</pre><br/>";
// 
//   $odpovedpars = "OPERATION=$order[OPERATION]" .
//                  "&ORDERNUMBER=$order[ORDERNUMBER]" .
//                  "&MERORDERNUM=$order[ORDERNUMBER]" .
//                  "&PRCODE=200" .
//                  "&SRCODE=TXT" . 
//                  "&MD=$order[MD]" .
//                  "&RESULTTEXT=odpoved textova" .
//                  "&DETAILS=$order[DESCRIPTION]" .
//                  "&USERPARAM1=userparams1" .
//                  "&ADDINFO=dodatecne_info";
//  
//   $odpoved = "gp_response.php?$odpovedpars";
//   echo "SIMULACE ODPOVEDI: <a href='$odpoved'>$odpovedpars</a><br/>";
//   echo "Navratovy retezec (DIGEST) nelze simulovat, nemam privatni klic banky.<br/>";
// }

////////////////////////////////////////////////
//
// Evidence platby (rozsirene pole pro evidenci)
//
$order_ext = $order;
$order_ext['name']     = $_POST['name'];
$order_ext['VATRN']    = $_POST['VATRN'];
$order_ext['VS']       = $_POST['VS'];
evidence($order_ext, null, MAILADDRINFO, URLPREHLED); // evidence pocatku platby

?>

</body>
</html>
