 <?php 
//
//  please edit the following four lines before each conference
//
    $confname = "EMS MASC Kacov 2025";
    $homepage = "ems-masc.cuni.cz";
    $confmail = "ems.masc@karlin.mff.cuni.cz";
//   $confmail = "mail.tichy@gmail.com";
    $secretary = "hbilkova@math.cas.cz";
//    $secretary = "ptichy@karlin.mff.cuni.cz";
    $events = "events-mgr@karlin.mff.cuni.cz";
//    $events = "ptichy@karlin.mff.cuni.cz";
//
//
       $first        = $_POST["first"];       // first name
       $last         = $_POST["last"];        // last name
       $email        = $_POST["email"];
       $affiliation  = $_POST["affiliation"];
$department   = $_POST["department"];
       $address      = $_POST["address"];
       $country      = $_POST["country"];
       $phone        = $_POST["phone"];
       $bus          = $_POST["bus"];         // yes no
       $male         = $_POST["male"];         // yes no
       $talk         = $_POST["talk"];        // no, talk, poster
//       if ($_POST["talk"]) {$talk = "yes";} else {$talk = "no";}
       $title = $_POST["title"];
       $coauthors = $_POST["coauthors"];
       $abstract = $_POST["abstract"];
       $comments     = $_POST["comments"];       
//       $room    = $_POST["room"];
       $roommate = $_POST["roommate"];
       $gdpr     = $_POST["gdpr"];         // yes no

//                              
       $first = str_replace("\\\\","\\",$first);
       $first = str_replace("\\\\","\\",$first);
       $last = str_replace("\\\\","\\",$last);
       $last = str_replace("\\\\","\\",$last);
       $affiliation = str_replace("\\\\","\\",$affiliation);
       $affiliation = str_replace("\\\\","\\",$affiliation);
       $address = str_replace("\\\\","\\",$address);
       $address = str_replace("\\\\","\\",$address);
       $title = str_replace("\\\\","\\",$title);
       $title = str_replace("\\\\","\\",$title);
       $coauthors = str_replace("\\\\","\\",$coauthors);
       $coauthors = str_replace("\\\\","\\",$coauthors);
       $abstract = str_replace("\\\\","\\",$abstract);
       $abstract = str_replace("\\\\","\\",$abstract);
       $roommate = str_replace("\\\\","\\",$roommate);
       $roommate = str_replace("\\\\","\\",$roommate);
       
// variable symbol generator $vs = time();
//       $soubor="count";    // all users have to be able to write to this file (chmod)
//       $file=fopen("$soubor","r+");
//       $pocet=fgets($file,100);
//       $pocet++;
//       fseek($file,0);
//       fputs($file,$pocet);
//       fclose($file);
//       $spocet = sprintf("%04s", $pocet);
//       $vs = "616005" . $spocet;
       
       

//       
       $rfdata = ""; 
       $rfdata = $rfdata . "First name: " . $first . "\n";
       $rfdata = $rfdata . "Last name: " . $last . "\n";
       if ($male=="yes") {
            $rfdata = $rfdata . "Gender: " . "Male" . "\n";
        } else {
            $rfdata = $rfdata . "Gender: " . "Female" . "\n";
        }
//       $rfdata = $rfdata . "Variable symbol: " . $vs . "\n";
       $rfdata = $rfdata . "E-mail: " . $email . "\n";
       $rfdata = $rfdata . "Organization: " . $affiliation . "\n";
       $rfdata = $rfdata . "Address: " . $address . "\n"; 
       $rfdata = $rfdata . "Country: " . $country . "\n";
       $rfdata = $rfdata . "Phone: " . $phone . "\n"; 
       $rfdata = $rfdata . "Bus: " . $bus . "\n";
  //     $rfdata = $rfdata . "Room        : " . $room . "\n";
       $rfdata = $rfdata . "Roommate: " . $roommate . "\n";
       $rfdata = $rfdata . "Comments: ".  $comments . "\n";
       $rfdata = $rfdata . "List of paricipants: " . $gdpr . "\n";
       $rfdata = $rfdata . "Talk: " . $talk . "\n";
       $rfdata = $rfdata . "Title: " . $title . "\n";
       $rfdata = $rfdata . "Coauthors: " . $coauthors .  "\n";
       $rfdata = $rfdata . "Abstract: " . $abstract . "\n\n";

//              
//       $rfdata = $rfdata . "TAB DELIMINATED RECORD\n\n";
//       $rfdata = $rfdata . $first . "\t" . $last ."\t" . $vs ."\t" . $email ."\t" . $affiliation ."\t" . $address ."\t";
//       $rfdata = $rfdata . $country . "\t" . $phone ."\t" . $bus ."\t" . $roommate . "\t" . $comments ."\t" . $talk ."\t" . $title ."\t";
//       $rfdata = $rfdata .  $coauthors . "\t" . $abstract . "\n";

//
//  rdata -> will be send to participant
//
       $rdata = ""; 
       $rdata = $rdata . "First name: " . $first . "\n";
       $rdata = $rdata . "Last name: " . $last . "\n";
       $rdata = $rdata . "E-mail: " . $email . "\n";
       $rdata = $rdata . "Organization: " . $affiliation . "\n";
       $rdata = $rdata . "Address: " . $address . "\n"; 
       $rdata = $rdata . "Country: " . $country . "\n";
       $rdata = $rdata . "Phone: " . $phone . "\n";
       $rdata = $rdata . "Roommate: " . $roommate . "\n";
       $rdata = $rdata . "Comments: " . $comments ."\n\n";
//
       if ($bus=="yes") { 
             $rdata = $rdata . "[X] I will take advantage of the chartered bus from Prague to Kacov and back\n\n";            
        } else {
            $rdata = $rdata . "[X] I will come individually\n\n";            
        }
       if ($gdpr=="yes") {
            $rdata = $rdata . "[X] I agree with publishing my name in the list of participants, which will be available at https://ems-masc.cuni.cz\n\n";
        } else {
            $rdata = $rdata . "[X] I do not agree with publishing my name\n\n";
        }


        if ($talk=="communication") {
            $rdata = $rdata . "[X] I intend to present a short $talk \n\n";            
            if ($title) {
                $rdata = $rdata . "Title:\n"."$title\n\n";
            }
            if ($coauthors) {
                $rdata = $rdata . "Coauthors:\n"."$coauthors\n\n";
            }
            if ($abstract) {
                $rdata = $rdata . "Abstract:\n"."$abstract\n\n";
            }            
        }
        if ($talk=="plenary") {
            $rdata = $rdata . "[X] I will present a series of lectures \n\n";            
            if ($title) {
                $rdata = $rdata . "Title:\n"."$title\n\n";
            }
            if ($coauthors) {
                $rdata = $rdata . "Coauthors:\n"."$coauthors\n\n";
            }
            if ($abstract) {
                $rdata = $rdata . "Abstract:\n"."$abstract\n\n";
            }            
        }

        $rdata = $rdata . "\n";
       
    $mm = "      ";
    $ics = "FROM: $confname\n".$mm;
    $ics = $ics . "Local organizing committee\n".$mm;
    $ics = $ics . "Faculty of Mathematics and Physics\n".$mm;
    $ics = $ics . "Charles University\n".$mm;
    $ics = $ics . "Sokolovska 83\n".$mm;
    $ics = $ics . "18675 Praha 8\n".$mm."Czech Republic\n\n";
//
//    
    $to = "TO:   $first $last <$email>\n\n";
//    
//
    $ms = "Dear participant,\n\n"."This is to confirm that the following registration data\n";
    $ms = $ms . "has been received by the local organizing committee:\n\n"."$rdata \n";    
//    
    $ms = $ms . "Information about the workshop can be found at\n\n"; 
    $ms = $ms . "     https://$homepage \n\n";
    $ms = $ms . "These pages are regularly updated. If you have any question,\n";
    $ms = $ms . "please feel free to contact us any time at this e-mail address.\n";
    $ms = $ms . "We look forward to seeing you in Kacov!\n\n";    
    $ms = $ms . "With our best regards,\n\n";    
    $ms = $ms . "Local organizing committee\n";
//
//    
    $ms = $ics . $to . $ms;
# Registration data $first $last
if (!empty($department)) {
             $FAKED = "FAKED lze ignorovat " . $_SERVER['REMOTE_ADDR'] . " ";
             mail("ulrych@karlin.mff.cuni.cz", "$FAKED $confname - Receipt of the registration form", $ms . "department:$department\n","From: $confname <$confmail>");
} else {
    if (mail($confmail, "$confname - Registration data", $rfdata,"From: $confname <$confmail>")){
             mail($secretary, "$confname - Registration data", $rfdata,"From: $confname <$confmail>");
             mail($events, "$confname - Registration data", $rfdata,"From: $confname <$confmail>");
             mail($confmail, "$confname - Receipt of the registration form", $ms,"From: $confname <$confmail>");
             mail($email, "$confname - Receipt of the registration form", $ms,"From: $confname <$confmail>");
             echo "<br> <h1 align=center> Thank you for registration! </h1> <br>\n<center>";
             echo "Your registration data has been sent to the organizers of the $confname meeting.</center><br>\n\n";
             echo "<center>In a few minutes you should obtain a <b>confirmation e-mail</b>. <br><br><a href=\"https://$homepage\">$confname</a>";
    } else {
             echo "<br> <h1> Some problems occured during the registration! </h1> <br>\n";
             echo "<center>Please send us your registration data via email. </center><br>\n";
             echo "<center>We apologize for complications.</center><br>\n";
    }
}

############ preposilani dat z formulare do administrace konference

# odkaz, kam smerovat datacurl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$url = 'https://admin.karlin.mff.cuni.cz/konfer/registrace.php';

# id akce v administrativnim systemu (nutno nejdriv registrovat akci)
$_POST['idAkce'] = 2;  # 1 = testovaci,  2 = EMS MASC,    3 = EMS MAFF


# preposlani dat z promenne $_POST
$fields_string = http_build_query($_POST);
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

?>
