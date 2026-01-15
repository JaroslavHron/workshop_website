<?php 
require("gp_confer.php"); 
// SGSIA21 SPECIFIC: zacatek souboru a obsah generujeme z index.html
echo zacatekSouboru("index.html","meta.*http-equiv.*charset", 1);
?>


<p>
Thank you for the payment of the registration fee.
</p>
<p>
You will receive payment confirmation at the conference site.
</p>

<br>
<p>By clicking on the following link you will return to the conference website
<a href="https://ems-masc.karlin.mff.cuni.cz">Kacov 2025</a>.
</p>

</body>
</html>


