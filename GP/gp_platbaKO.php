<?php 
require("gp_confer.php"); 
// SGSIA21 SPECIFIC: zacatek souboru a obsah generujeme z index.html
echo zacatekSouboru("index.html","meta.*http-equiv.*charset", 1);

$OSEP = (isset($_GET['o'])) ? "!<br/>" : ".";
?>

<p>
Your payment by card was unsuccessful<?php echo $OSEP;?>
You can try it again or contact organisers of 
the conference by e-mail.
</p>

<br>
<p>By clicking on the following link you will return to the conference website
<a href="https://ems-masc.karlin.mff.cuni.cz">Kacov 2025</a>.
</p>

</body>
</html>


