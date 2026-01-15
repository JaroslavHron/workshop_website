<?php

define ("SAVE_SIGNATURE_TO_FILE", true); //ulozit vypocteny podpis do souboru / save digest to file

//URL pro zalozeni nove objednavky / URL for creating new order
if (PRODUKCNI) define ("GP_URL", "https://3dsecure.gpwebpay.com/pgw/order.do");
else           define ("GP_URL", "https://test.3dsecure.gpwebpay.com/pgw/order.do"); 

define('GP_OPERATION',       'CREATE_ORDER');
define('GP_CURRENCY_CZK',    203);
//define('GP_CURRENCY_EUR',   978);
define('GP_CURRENCY_UNIT',   0.01);
define('GP_DEPOSIT_FLAG',    true);
//      URL_RETURN_FROM_GP  musi souhlasit s registrovanou pri zprovoznovani eshopu
define('URL_RETURN_FROM_GP', "https://essam-masc.karlin.mff.cuni.cz/gp_response.php"); 

define('FILE_PREFIX', "uziv-");  // prefix pro uzivatelske soubory
?>
