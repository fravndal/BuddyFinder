
<!DOCTYPE html>
<html>
<head>
	<title>BuddyFinder</title>
	<meta charset="utf8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">

	<!--##Device = Mobiles (Portrait)-->
	<link rel="stylesheet" type="text/css" href="css/default/defaultMobilePortrait.css" media="screen and (min-width: 234px) and (max-width: 480px)" />
	<!--##Device = Low Resolution Tablets, Mobiles (Landscape)-->
	<link rel="stylesheet" type="text/css" href="css/default/defaultMobileLandscape.css" media="screen and (min-width: 481px) and (max-width: 767px)" />
	<!--##Device = Tablets, Laptops, Desktops (portrait)-->
  <link rel="stylesheet" type="text/css" href="css/default/defaultTabletPortrait.css" media="screen and (min-width: 768px) and (max-width: 900px)" />
  <!--##Device = Tablets, Laptops, Desktops (Landscape)-->
  <link rel="stylesheet" type="text/css" href="css/default/defaultTabletLandscape.css" media="screen and (min-width: 901px) and (max-width: 1280px)" />
	<!--##Device = Tablets, Laptops, Desktops (portrait)-->
	<link rel="stylesheet" type="text/css" href="css/default/defaultDesktop.css" media="screen and (min-width: 1281px)">
</head>

<?php
include("include/config.php");
include("genererRegler.php");
session_start();


// Function to get the client ip addressa
// mangler å banne brukeren som har prøvd å logge inn 5 ganger + gi tilbakemelding
if($_SERVER["REQUEST_METHOD"] == "POST") {
    //salte passordet før vi sjekker det mot databasen
    $passord = $salt . $_POST['passord'];
    $passord = sha1($passord);

    $_SESSION['login_failed_count'] = 0;


    // select statementen mot databasen + binde parameterne for å forhindre SQL injections
    $sjekkOmBrukernavnPassordStemmer = $pdo->prepare("SELECT brukernavn FROM bruker WHERE brukernavn = :brukernavn AND passord = :passord");
    $sjekkOmBrukernavnPassordStemmer->bindParam(':brukernavn', $_POST['brukernavn']);
    $sjekkOmBrukernavnPassordStemmer->bindParam(':passord', $passord);

    // executer statementen
    $sjekkOmBrukernavnPassordStemmer->execute();

    // fetcher den assosiative arrayen
    $rader = $sjekkOmBrukernavnPassordStemmer->fetchAll(PDO::FETCH_ASSOC);

    $sjekkOmBrukerAktiv = $pdo->prepare("SELECT profil_aktiv FROM bruker WHERE brukernavn = :brukernavn");
    $sjekkOmBrukerAktiv->bindParam(':brukernavn', $_POST['brukernavn']);
    $sjekkOmBrukerAktiv->execute();
    $aktivBruker = $sjekkOmBrukerAktiv->fetch();

    // NY KODE LAR IKKE BANNEDE BRUKERE LOGGE INN!
    $sjekkOmBrukerBannet = $pdo->prepare("SELECT * FROM bruker_karantene WHERE brukernavn = :brukernavn");
    $sjekkOmBrukerBannet->bindParam(':brukernavn', $_POST['brukernavn']);
    $sjekkOmBrukerBannet->execute();
    $bannetBruker = $sjekkOmBrukerBannet->fetch();

    $finnIdbruker = $pdo->prepare("SELECT id_bruker FROM bruker WHERE brukernavn = :brukernavn");
    $finnIdbruker->bindParam(':brukernavn', $_POST['brukernavn']);
    $finnIdbruker->execute();
    $bruker_id = $finnIdbruker->fetch();


    $result = $pdo->prepare("SELECT * FROM loginn_feil WHERE id_bruker= :bruker_id");
    $result->bindParam(':bruker_id', $bruker_id[0]);
    $result->execute();
    $data = $result->fetch();
    $nyTid = $data['feil_logginn_siste'];
    $nyTid = str_replace("-", "", $nyTid);
    $nyTid = str_replace(" ", "", $nyTid);
    $nyTid = str_replace(":", "", $nyTid);

    $bannetTid = $bannetBruker['til_dato_ban'];
    $bannetTid = str_replace("-", "", $bannetTid);
    $bannetTid = str_replace(" ", "", $bannetTid);
    $bannetTid = str_replace(":", "", $bannetTid);

    $tid = date("YmdHis", time());



    // -------------------------------------------
    if (count($rader) and $aktivBruker[0] == "0") {
        $_SESSION['tilbakemelding'] = "Brukeren er slettet, kontakt en admin for å gjennåpne profilen.";
        $_SESSION['tidsholder'] = time();
    } else {
        if (count($rader) and $bannetBruker[1] == $_POST['brukernavn'] and $bannetTid > $tid) {
            $_SESSION['tilbakemelding'] = $bannetBruker[3];
            $_SESSION['tidsholder'] = time();
        } else {
            if (count($rader) and $aktivBruker[0] == "1" and $bannetTid < $tid or count($rader) and $aktivBruker[0] == "1" and empty($bannetBruker)) {
                if (!empty($bannetBruker)) {
                    $nullstillKarantene = $pdo->prepare("DELETE FROM bruker_karantene WHERE brukernavn = :brukernavn");
                    $nullstillKarantene->bindParam(':brukernavn', $_POST['brukernavn']);
                    $nullstillKarantene->execute();

                }
                if ($data['feil_logginn_teller'] > 0) {
                    $nullstillFeilloginn = $pdo->prepare("DELETE FROM loginn_feil WHERE id_bruker = :id_bruker");
                    $nullstillFeilloginn->bindParam(':id_bruker', $bruker_id[0]);
                    $nullstillFeilloginn->execute();
                }


                $_SESSION['login_user'] = $_POST['brukernavn'];
                $finnIdbruker = $pdo->prepare("SELECT id_bruker FROM bruker WHERE brukernavn = :brukernavn");
                $finnIdbruker->bindParam(':brukernavn', $_SESSION['login_user']);
                $finnIdbruker->execute();
                $id_bruker = $finnIdbruker->fetch();
                $gyldig_bruker = $id_bruker[0];

                /* Sjekker om brukeren er admin */
                $sjekkOmBrukerErAdmin = $pdo->prepare("SELECT profil_admin FROM bruker WHERE brukernavn = :brukernavn");
                $sjekkOmBrukerErAdmin->bindParam(':brukernavn', $_SESSION['login_user']);
                $sjekkOmBrukerErAdmin->execute();
                $profil_admin = $sjekkOmBrukerErAdmin->fetch();

                // Setter cookies:
                setcookie('bruker_type', $profil_admin[0]);
                setcookie('user_id', $_POST['brukernavn']);
                setcookie('bruker_id', $gyldig_bruker);
                header("location: minside.php");


            } else {
                $_SESSION['tilbakemelding'] = "Brukernavn eller passord er feil, prøv igjen!";
                $_SESSION['tidsholder'] = time();

                $finnBrukernavnBruker = $pdo->prepare("SELECT brukernavn FROM bruker WHERE brukernavn = :brukernavn");
                $finnBrukernavnBruker->bindParam(':brukernavn', $_POST['brukernavn']);
                $finnBrukernavnBruker->execute();
                $brukernavn_bruker = $finnBrukernavnBruker->fetchAll(PDO::FETCH_ASSOC);


                if (!empty($brukernavn_bruker)) {
                    $tidSjekk = $nyTid + 500;

                    if (empty($data)) {
                        $insertStatement = $pdo->prepare("INSERT INTO loginn_feil (id_bruker, feil_logginn_teller, feil_logginn_siste) values (:id_bruker, 1, NOW())");
                        $insertStatement->bindParam(':id_bruker', $bruker_id[0]);
                        $insertStatement->execute();
                    }
                    else {
                        if ($data['feil_logginn_teller'] > 0 and $data['feil_logginn_teller'] < 5 and $nyTid > $tidSjekk) {
                            $nullstillFeilloginn = $pdo->prepare("DELETE FROM loginn_feil WHERE id_bruker = :id_bruker");
                            $nullstillFeilloginn->bindParam(':id_bruker', $bruker_id[0]);
                            $nullstillFeilloginn->execute();

                            $insertStatement = $pdo->prepare("INSERT INTO loginn_feil (id_bruker, feil_logginn_teller, feil_logginn_siste) values (:id_bruker, 1, NOW())");
                            $insertStatement->bindParam(':id_bruker', $bruker_id[0]);
                            $insertStatement->execute();
                        }
                        else {
                            if ($data['feil_logginn_teller'] > 0 and $data['feil_logginn_teller'] < 5 and $nyTid < $tidSjekk) {
                                $teller = $data['feil_logginn_teller'] + 1;
                                $failedLoginAttempts = $pdo->prepare("UPDATE loginn_feil SET feil_logginn_teller = :teller, feil_logginn_siste=NOW() WHERE :id_bruker = id_bruker");
                                $failedLoginAttempts->bindParam(':id_bruker', $bruker_id[0]);
                                $failedLoginAttempts->bindParam(':teller', $teller);
                                $failedLoginAttempts->execute();

                                $result = $pdo->prepare("SELECT * FROM loginn_feil WHERE id_bruker= :bruker_id");
                                $result->bindParam(':bruker_id', $bruker_id[0]);
                                $result->execute();
                                $data = $result->fetch();

                                if ($data['feil_logginn_teller'] == 5) {
                                    $settIKarantene = $pdo->prepare("INSERT INTO bruker_karantene(brukernavn, admin, begrunnelse, fra_dato_ban, til_dato_ban) VALUES(:brukernavn, \"admin\", \"For mange innloggingsforsøk, du er nå utestengt i 5 minutter.\", now(), now() + INTERVAL 5 MINUTE) ");
                                    $settIKarantene->bindParam(':brukernavn', $_POST['brukernavn']);
                                    $settIKarantene->execute();
                                    $nullstillFeilloginn = $pdo->prepare("DELETE FROM loginn_feil WHERE id_bruker = :id_bruker");
                                    $nullstillFeilloginn->bindParam(':id_bruker', $bruker_id[0]);
                                    $nullstillFeilloginn->execute();
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}








?>

<body>
<!-- Header for logo -->
<header class="header">
    <div class="section">
        <?php
        include("include/logo.html");
        include("include/loginform.html");
        ?>
    </div>
</header>
<mark>
    <?php
    include("error.php");
    ?>
</mark>
<section id="registerbtn">
    <p id="registerText">Registrer deg for å ta del i studentlivet!</p>
    <a href="register.php" class="registerMobile" type="button" value="Registrer deg her!"> REGISTRER </a>
</section>

<!-- Registerform for desktop-->
<main id="container">
    <section id="topcontent">
        <article id="reg">
            <?php
            include("include/registerForm.html");
            ?>
        </article>
        <article id="infospan">
            <h1 id="articleHeader">Finn venner!</h1>
            <h2 id="articleHeader2">Registrer deg!</h2>
            <p id="article1">Bli kjent.</p>
            <p id="article2">Chat med venner.</p>
            <p id="article1">Delta på arrangementer.</p>
            <p id="article3">Sammarbeid med skoleoppgaver.</p>
        </article>
        <!-- Menu for Desktop -->
        <article id="faq">
            <h1 id="navheader">Noen spørsmål?</h1>
            <nav id="myDropdown" class="dropdown-content">
                <a href="kontaktOss.php" style="font-family: Arial;">Kontakt oss</a>
            </nav>
        </article>
    </section>
</main>
<footer id="footer">
    <?php
    include("include/footer.html");
    ?>
</footer>
</body>
<!-- Denne siden er utviklet av Fredrik Ravndal og Fredrik Hulaas, siste gang endret 09.04.2018 -->
<!-- Denne siden er kontrollert av Ola Bredviken og Håvard Betten, siste gang 09.04.2018 -->
</html>
