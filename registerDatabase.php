<?php
include_once('include/config.php');
include("include/session.php");

/*
*** Sjekker om bruker finnes fra før
*/
$sjekkOmBrukerFinnes = $pdo->prepare("SELECT * FROM bruker WHERE brukernavn = :brukernavn");
$sjekkOmBrukerFinnes->bindParam(':brukernavn', $_POST['brukernavn']);

$sjekkOmBrukerFinnes->execute();
$bruker = $sjekkOmBrukerFinnes->fetchAll(PDO::FETCH_ASSOC);


$sjekkOmEpostFinnes = $pdo->prepare("SELECT * FROM bruker WHERE epost = :epost");
$sjekkOmEpostFinnes->bindParam(':epost', $_POST['epost']);

$sjekkOmEpostFinnes->execute();
$epost = $sjekkOmEpostFinnes->fetchAll(PDO::FETCH_ASSOC);

if (count($bruker)) {
    $_SESSION['tilbakemelding'] = "Brukernavn finnes fra før!";
    $_SESSION['tidsholder'] = time();
}
else {
    if(count($epost)){
        $_SESSION['tilbakemelding'] = "Eposten finnes fra før!";
        $_SESSION['tidsholder'] = time();
    }
    else {
        // salter passord + hash
        $passord = $salt . $_POST['passord'];
        $passord = sha1($passord);

        // insert statement mot databasen + forhindre sql injection med bindparam
        $registrerBruker = $pdo->prepare("INSERT INTO bruker (brukernavn, passord, epost) VALUES (:brukernavn, :passord, :epost)");
        $registrerBruker->bindParam(':brukernavn', $_POST['brukernavn']);
        $registrerBruker->bindParam(':passord', $passord);
        $registrerBruker->bindParam(':epost', $_POST['epost']);
        $registrerBruker->execute();

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
    }


}

// kobler av databasen
$pdo = null;
//Denne siden er utviklet av Håvard Betten og Fredrik Hulaas, Ola Bredviken og Fredrik Ravndal siste gang endret 09.04.2018 -->
//Denne siden er kontrollert av Fredrik Hulaas og Fredrik Ravndal, siste gang 09.04.2018 -->
?>
