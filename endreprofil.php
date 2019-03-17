<?php
include("include/config.php");
include("include/session.php");

$aktivBruker = $pdo->prepare("SELECT profil_aktiv FROM bruker WHERE :brukernavn = brukernavn");
$aktivBruker->bindParam(':brukernavn', $_COOKIE['user_id']);
$aktivBruker->execute();
$aktiv = $aktivBruker->fetch();
if($aktiv[0] == 0) {
    header("location: logout.php");
}

$epostStatement = $pdo->prepare("SELECT epost FROM bruker WHERE :brukernavn = brukernavn");
$epostStatement->bindParam(':brukernavn', $_COOKIE['user_id']);
$epostStatement->execute();
$epostAdresse = $epostStatement->fetch();
$synligBruker = $pdo->prepare("SELECT profil_synlig FROM bruker WHERE :brukernavn = brukernavn");
$synligBruker->bindParam(':brukernavn', $_COOKIE['user_id']);
$synligBruker->execute();
$synlig = $synligBruker->fetch();



if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
    $_SESSION['tilbakemelding'] = "Profilen er endret...";
    if(isset($_POST['slettProfil'])) {
        $oppdaterSynlig = $pdo->prepare("UPDATE bruker SET profil_aktiv = '0' WHERE :brukernavn = brukernavn");
        $oppdaterSynlig->bindParam(':brukernavn', $_COOKIE['user_id']);
        $oppdaterSynlig->execute();
        $_SESSION['tilbakemelding'] = "Profilen er slettet...";
        $_SESSION['tidsholder'] = time();
        header("location: logout.php");
    }
    if(isset($_POST['submit-synlig'])) {
        $synlig_post = $_POST['synlig'];

        $oppdaterSynlig = $pdo->prepare("UPDATE bruker SET profil_synlig = :synlig WHERE :brukernavn = brukernavn");
        $oppdaterSynlig->bindParam(':synlig', $synlig_post);
        $oppdaterSynlig->bindParam(':brukernavn', $_COOKIE['user_id']);
        $oppdaterSynlig->execute();
        if($synlig_post==0) $printSynlig = "ikke synlig"; else $printSynlig = "synlig";
        $_SESSION['tilbakemelding'] = "Profilen din er " . $printSynlig . " for andre.";
        $_SESSION['tidsholder'] = time();

    }


    if(isset($_POST['submit-email'])) {
        $oppdaterEpost = $pdo->prepare("UPDATE bruker SET epost = :epost WHERE :brukernavn = brukernavn");
        $oppdaterEpost->bindParam(':epost', $_POST['bytt_epost']);
        $oppdaterEpost->bindParam(':brukernavn', $_COOKIE['user_id']);
        $oppdaterEpost->execute();
        $_SESSION['tilbakemelding'] = "Eposten din er endret til " . $_POST['bytt_epost'];
        $_SESSION['tidsholder'] = time();
    }


    $sjekkOmBrukerHarBeskrivelse = $pdo->prepare("SELECT beskrivelse FROM bruker AS b LEFT JOIN bruker_beskrivelse AS bb ON b.id_bruker = bb.id_bruker WHERE brukernavn = :brukernavn");
    $sjekkOmBrukerHarBeskrivelse->bindParam(':brukernavn', $_COOKIE['user_id']);
    $sjekkOmBrukerHarBeskrivelse->execute();
    $testOmBrukerHarBeskrivelse = $sjekkOmBrukerHarBeskrivelse->fetch();

    if(isset($_POST['submit-beskrivelse'])) {
        if($testOmBrukerHarBeskrivelse[0] !== NULL) {
            $oppdaterBeskrivelse = $pdo->prepare(
                    "UPDATE bruker_beskrivelse
                    SET beskrivelse= :beskrivelse
                    WHERE id_bruker = :id_bruker"
            );
            $oppdaterBeskrivelse->bindParam(':beskrivelse', $_POST['bruker_beskrivelse']);
            $oppdaterBeskrivelse->bindParam(':id_bruker', $_COOKIE['bruker_id']);
            $oppdaterBeskrivelse->execute();
            $_SESSION['tilbakemelding'] = "Din beskrivelse er endret.";
            $_SESSION['tidsholder'] = time();

        }
        else {
            $leggTilBeskrivelse = $pdo->prepare("INSERT INTO bruker_beskrivelse (id_bruker, beskrivelse) VALUES(:id_bruker, :beskrivelse)");
            $leggTilBeskrivelse->bindParam(':id_bruker', $_COOKIE['bruker_id']);
            $leggTilBeskrivelse->bindParam(':beskrivelse', $_POST['bruker_beskrivelse']);
            $leggTilBeskrivelse->execute();
            $_SESSION['tilbakemelding'] = "Din beskrivelse er lagt til.";
            $_SESSION['tidsholder'] = time();
        }
    }

    // sjekker om siste post i formen er satt
    if(isset($_POST['endre_passord'])){
        $gammeltPassord=$_POST['gammelt_passord'];
        $nyttPassord=$_POST['nytt_passord'];
        $rePassord=$_POST['re_passord'];

        // salter og hasher passord
        $passord = $salt . $_POST['gammelt_passord'];
        $passord = sha1($passord);

        // select statement opp mot databasen +  binde parameterne for å forhindre SQL injections
        $byttPassord = $pdo->prepare("SELECT * FROM bruker WHERE brukernavn = :brukernavn AND passord = :passord");
        $byttPassord->bindParam(':brukernavn', $_SESSION['login_user']);
        $byttPassord->bindParam(':passord', $passord);

        // executer queryen
        $byttPassord->execute();

        // fetcher som assosiativ array
        $rader = $byttPassord->fetch(PDO::FETCH_ASSOC);

        // hvis rows er true er brukerinfo valid
        if(count($rader)){
            // sjekker om det nye passorder er det samme i begge inputfieldsa + at det game passordet ikke er lik det nye
            if($nyttPassord===$rePassord AND $gammeltPassord !== $nyttPassord){
                $nyttPassord = $salt . $_POST['nytt_passord'];
                $nyttPassord = sha1($nyttPassord);

                // Updaten til databasen +  binde parameterne for å forhindre SQL injections
                $oppdaterPassord = $pdo->prepare("UPDATE bruker set passord = :nytt_passord where brukernavn = :brukernavn");
                $oppdaterPassord ->bindParam(':brukernavn', $_SESSION['login_user']);
                $oppdaterPassord ->bindParam(':nytt_passord', $nyttPassord);

                // executer query
                $oppdaterPassord ->execute();

                $_SESSION['tilbakemelding'] = "Passordet er oppdatert.";
                $_SESSION['tidsholder'] = time();
            }
            else{
                $_SESSION['tilbakemelding'] = "Passordene stemmer ikke overens.";
                $_SESSION['tidsholder'] = time();
            }
        }
        else {
            $_SESSION['tilbakemelding'] = "Ditt gamle passord er feil.";
            $_SESSION['tidsholder'] = time();
        }
    }

    if(isset($_POST['submit-int'])) {
        $finnAlleInteresser = $pdo->prepare("SELECT interesse FROM alle_interesser");
        $finnAlleInteresser->execute();
        $alleInteresser = $finnAlleInteresser->fetchAll();

        if (!in_array($_POST['interesse'], $alleInteresser) && $_POST['interesse'] !== "") {
            //legger inn dataen i interesse tablet i databasen hvis det ikke er der fra før
            $leggTilInteresseAlleInteresser = $pdo->prepare("INSERT INTO alle_interesser (interesse) VALUES(:interesse)");
            $leggTilInteresseAlleInteresser->bindParam(':interesse', $_POST['interesse']);
            $leggTilInteresseAlleInteresser->execute();
            $_SESSION['tilbakemelding'] = "Interessen er opprettet.";
            $_SESSION['tidsholder'] = time();
        }
    }

    if(isset($_POST['submit-int-bruker'])) {
        $leggTilInteresseBrukerInteresserListbox = $pdo->prepare("INSERT INTO bruker_interesser (id_bruker, interesse) VALUES(:id_bruker, :interesse)");
        $leggTilInteresseBrukerInteresserListbox->bindParam(':id_bruker', $_COOKIE['bruker_id']);
        $leggTilInteresseBrukerInteresserListbox->bindParam(':interesse', $_POST['valgt_interesse']);
        $leggTilInteresseBrukerInteresserListbox->execute();
        $_SESSION['tilbakemelding'] = "Interessen er lagt til på din profil.";
        $_SESSION['tidsholder'] = time();

    }

    // sletter interessen
    if(isset($_POST['submit-slett-interesse'])) {
        $slettInteresse = $pdo->prepare("DELETE FROM bruker_interesser WHERE id_bruker = :id_bruker AND interesse = :interesse");
        $slettInteresse->bindParam(':interesse', $_POST['slett_interesse']);
        $slettInteresse->bindParam(':id_bruker', $_COOKIE['bruker_id']);
        $slettInteresse->execute();
        $_SESSION['tilbakemelding'] = "Interessen er fjernet fra din profil.";
        $_SESSION['tidsholder'] = time();
    }

    if(isset($_POST['submit-stu'])) {
        $finnAlleStudium = $pdo->prepare("SELECT interesse FROM alle_interesser");
        $finnAlleStudium->execute();
        $alleStudium = $finnAlleStudium->fetchAll();

        if (!in_array($_POST['studium'], $alleStudium) && $_POST['studium'] !== "") {
            //legger inn dataen i interesse tablet i databasen hvis det ikke er der fra før
            $leggTilStudiumAlleStudium = $pdo->prepare("INSERT INTO alle_studium (studium) VALUES(:studium)");
            $leggTilStudiumAlleStudium->bindParam(':studium', $_POST['studium']);
            $leggTilStudiumAlleStudium->execute();
            $_SESSION['tilbakemelding'] = "Studiumet er opprettet.";
            $_SESSION['tidsholder'] = time();
        }

    }

    if(isset($_POST['submit-stu-bruker'])) {
        $leggTilStudiumBrukerStudiumListbox = $pdo->prepare("INSERT INTO bruker_studium (id_bruker, studium) VALUES(:id_bruker, :studium)");
        $leggTilStudiumBrukerStudiumListbox->bindParam(':id_bruker', $_COOKIE['bruker_id']);
        $leggTilStudiumBrukerStudiumListbox->bindParam(':studium', $_POST['valgt_studium']);
        $leggTilStudiumBrukerStudiumListbox->execute();
        $_SESSION['tilbakemelding'] = "Studiumet er lagt til på din profil.";
        $_SESSION['tidsholder'] = time();
    }

    // Endre studium
    if(isset($_POST['submit-endre-studium'])) {
        $slettStudium = $pdo->prepare("UPDATE bruker_studium SET studium = :studium WHERE :id_bruker = id_bruker");
        $slettStudium->bindParam(':studium', $_POST['endre_studium']);
        $slettStudium->bindParam(':id_bruker', $_COOKIE['bruker_id']);
        $slettStudium->execute();
        $_SESSION['tilbakemelding'] = "Studiumet ditt er endret.";
        $_SESSION['tidsholder'] = time();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>MinSide</title>
	<meta charset="utf8">
	<meta name="viewport" content="width=device-width,initial-scale=1,
	maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">
    <!--##Device = Mobiles (Portrait)-->
    <link rel="stylesheet" type="text/css" href="css/endreprofil/endreprofilMobilePortrait.css" media="screen and (min-width: 234px) and (max-width: 480px)" />
    <!--##Device = Low Resolution Tablets, Mobiles (Landscape)-->
    <link rel="stylesheet" type="text/css" href="css/endreprofil/endreprofilMobileLandscape.css" media="screen and (min-width: 481px) and (max-width: 767px)" />
    <!--##Device = Tablets, Laptops, Desktops (portrait)-->
    <link rel="stylesheet" type="text/css" href="css/endreprofil/endreprofilTabletPortrait.css" media="screen and (min-width: 768px) and (max-width: 900px)" />
    <!--##Device = Tablets, Laptops, Desktops (Landscape)-->
    <link rel="stylesheet" type="text/css" href="css/endreprofil/endreprofilTabletLandscape.css" media="screen and (min-width: 901px) and (max-width: 1280px)" />
    <!--##Device = Laptops, Desktops (Landscape)-->
	<link rel="stylesheet" type="text/css" href="css/endreprofil/endreprofilDesktop.css" media="screen and (min-width: 1281px)">

</head>

<body>
<div id="parent">
<header id="child1">
    <?php
    include("include/logo.html");
    ?>
</header>

<nav id="child2">
    <?php
    include("include/menu.html");
    ?>
</nav>
</div>

<mark>
    <?php
    include("error.php");
    ?>
</mark>

<article id="rammeOverskrift">
    <h1 id="overskrift">Administrer din profil!</h1>
</article>

<article id="ytre">
    <div id="kolonne1Desktop">
    <div id="kolonne1">
        <article id="rammeBilde">
            <?php
            $picturepathstatement = $pdo->prepare(
                "SELECT br.id_bruker, brukernavn, bilde 
                          FROM bruker AS br LEFT JOIN bruker_bilde AS bb
                          ON br.id_bruker=bb.id_bruker
                          WHERE brukernavn = :brukernavn"
            );
            $picturepathstatement->bindParam(':brukernavn', $_COOKIE['user_id']);
            $picturepathstatement->execute();
            $picturepath = "picture/minside/profilepicture.jpg";
            $choose = 0;
            for($i=0; $r = $picturepathstatement->fetch(); $i++){
                if ($r['bilde']) {
                    $picturepath = $r['bilde'];
                    $choose = 1;
                }
            }
            ?>
            <img id="bildeProfil" src="<?php if($choose == 1){echo $picturepath;}else{echo $picturepath;} ?>" alt="Profilbilde" />
        </article>

        <article id="rammeVelkommen">
            <h1 id="overskrift1Velkommen">Min Profil</h1>
            <p id="paragrafInnloggetBruker">Ditt brukernavn: <?php echo $_COOKIE['user_id'];?></p>
            <p id="pragrafEpostBruker">Din epost-adresse: <?php echo $epostAdresse[0];?> </p>
            <p id="paragrafSynligBruker">Din profil er
                <?php if($synlig[0]==0) echo "ikke synlig"; else echo "synlig" ?>
                for andre brukere.
            </p>
        </article>
    </div>

    <div id="kolonne2">
        <article id="minBeskrivelse">
            <h1 id="overskrift1Beskrivelse">Min Beskrivelse</h1>
            <?php
            //Spørring for å få beskrivelse av bruker
            $brukerBeskrivelse = $pdo->prepare("SELECT br.id_bruker, brukernavn, beskrivelse
                                                      FROM bruker AS br LEFT JOIN bruker_beskrivelse AS bb
                                                      ON br.id_bruker=bb.id_bruker
                                                      WHERE brukernavn = :brukernavn");
            $brukerBeskrivelse->bindParam(':brukernavn', $_COOKIE['user_id']);
            $brukerBeskrivelse->execute();

            for($i=0; $row = $brukerBeskrivelse->fetch(); $i++){
                ?>
                <label for="textBeskrivelse"></label><textarea id="textBeskrivelse" rows="4" cols="40" readonly="readonly"><?php echo $row['beskrivelse']; ?></textarea>
                <?php
            }
            ?>

        </article>
        <div id="tableRamme">
            <article id="rammeInteresser" style="overflow-y:auto;">
                <table border="1" cellspacing="2" cellpadding="5" id="tableInteresser">
                    <thead id="tableheadInteresser">
                        <tr id="tablerow1Interesser">
                            <th id="tableheaderInteresser">Mine interesser</th>
                        </tr>
                    </thead>

                    <tbody id="tablebodyInteresser">
                    <?php
                    // Spørring for å få alle interesser på brukeren som er logget inn
                    $alleBrukersInteresser = $pdo->prepare('SELECT br.id_bruker, brukernavn, interesse
                                                              FROM bruker AS br LEFT JOIN bruker_interesser AS bi
                                                              ON br.id_bruker=bi.id_bruker
                                                              WHERE brukernavn = :brukernavn');
                    $alleBrukersInteresser->bindParam(':brukernavn', $_COOKIE['user_id']);
                    $alleBrukersInteresser->execute();

                    for($i=0; $brukerInteresse = $alleBrukersInteresser->fetch(); $i++){
                        ?>
                        <tr id="tablerow2Interesser">
                            <td id="tabledefinitionInteresser"><label><?php echo $brukerInteresse['interesse']; ?></label></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </article>

            <article id="rammeStudium" style="overflow-y:auto;">
                <table border="1" cellspacing="2" cellpadding="5" id="tableStudium">
                    <thead id="tableheadStudium">
                    <tr id="tablerow1Studium">
                        <th id="tableheaderStudium">Mitt studium</th>
                    </tr>
                    </thead>

                    <tbody id="tablebodyStudium">
                    <?php
                    // Spørring for å få alle interesser på brukeren som er logget inn
                    $brukersStudium = $pdo->prepare('SELECT br.id_bruker, brukernavn, studium
                                                              FROM bruker AS br LEFT JOIN bruker_studium AS bs
                                                              ON br.id_bruker=bs.id_bruker
                                                              WHERE brukernavn = :brukernavn');
                    $brukersStudium->bindParam(':brukernavn', $_COOKIE['user_id']);
                    $brukersStudium->execute();
                    for($i=0; $brukerStudium = $brukersStudium->fetch(); $i++){
                        ?>
                        <tr id="tablerow2Studium">
                            <td id="tabledefinitionStudium"><label><?php echo $brukerStudium['studium']; ?></label></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </article>
        </div>
    </div>
    </div>
    <div id="kolonne3">
        <aside id="synligForm">
            <form action="endreprofil.php" method="post">
                <h1 id="visProfil">Vis profilen til andre</h1>
                <h2 id="headerSynlig">Velg om profilen din skal være synlig for andre:</h2>
                <?php
                $synligBruker = $pdo->prepare("SELECT profil_synlig FROM bruker WHERE :brukernavn = brukernavn");
                $synligBruker->bindParam(':brukernavn', $_COOKIE['user_id']);
                $synligBruker->execute();
                $synlig = $synligBruker->fetch();
                ?>
                <input id="radiobuttonSynlig" type="radio" value="1" name="synlig" <?php if($synlig[0]==1) echo "checked"; ?>>
                <label for="radiobuttonSynlig">Vis</label>
                <input id="radiobuttonIkkeSynlig" type="radio" value="0" name="synlig" <?php if($synlig[0]==0) echo "checked"; ?>>
                <label for="radiobuttonIkkeSynlig">Skjul</label>
                <input id="buttonsavesynlig" type="submit" value="Lagre" name="submit-synlig">
            </form>
        </aside>

        <aside id="infoForm">
            <form action="endreprofil.php" method="post">
                <h1 id="header3">Skriv om deg selv</h1>
                <textarea id="userinfo" name="bruker_beskrivelse" placeholder="Beskriv deg selv her..."></textarea>
                <br>
                <input id="buttonsaveuserinfo" type="submit" value="Lagre" name="submit-beskrivelse">
            </form>
        </aside>

        <aside id="interesseForm">
            <form action="endreprofil.php"  method="post" enctype="multipart/form-data">
                <h1 id="interesser">Interesser</h1>
                <h2 id="header2Interesser">Legg til interesse på deg selv:</h2>
                <?php
                $resultat = $pdo->prepare("SELECT * FROM alle_interesser");
                $resultat->execute();
                $resultatAlleInteresser = $resultat->fetchAll(PDO::FETCH_COLUMN, 1);
                $resultatbruker = $pdo->prepare(
                    "SELECT bruker.brukernavn, GROUP_CONCAT(bruker_interesser.interesse) as 'interesser'
                        FROM bruker JOIN bruker_interesser ON bruker_interesser.id_bruker = bruker.id_bruker
                        WHERE bruker.id_bruker = :id_bruker
                        GROUP BY bruker.id_bruker;"
                );
                $resultatbruker->bindParam(':id_bruker', $_COOKIE['bruker_id']);
                $resultatbruker->execute();
                $resultatBrukerInteresser = $resultatbruker->fetch();

                $brukerInteresser = $resultatBrukerInteresser['interesser']; // fra spørringene fra bruker
                $arrayInteresser = explode(',', $brukerInteresser);
                $valgAvAlleMuligeBrukerInteresser = [];

                foreach($resultatAlleInteresser as $interesser) {
                    if(!in_array($interesser, $arrayInteresser)){
                        $valgAvAlleMuligeBrukerInteresser[] = $interesser;
                    }
                }

                if(count($valgAvAlleMuligeBrukerInteresser)){
                    ?>
                    <label for="valgtInteresse"></label><select id="valgtInteresse" name="valgt_interesse">
                        <?php
                        foreach($valgAvAlleMuligeBrukerInteresser as $enMuligInteresse) {
                            ?>
                            <option value="<?php echo $enMuligInteresse; ?>"><?php echo $enMuligInteresse; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <?php
                }
                ?>
                <br>
                <input id="knappLeggTilInteresse" type="submit" value="Legg til interesse" name="submit-int-bruker">
                <br>
            </form>
            <form action="endreprofil.php"  method="post" enctype="multipart/form-data">
                <h2 id="header2Interesser2">Opprett en ny interesse:</h2>
                <input id="nyInteresse" type="text" name="interesse" placeholder="Ny interesse...">
                <br>
                <input id="knappOpprettInteresse" type="submit" value="Opprett interesse" name="submit-int">

                <h1 id="slettInteresser">Slett en interesse</h1>
                <label for="valgtInteresseSlett"></label><select id="valgtInteresseSlett" name="slett_interesse">
                    <?php
                    $alleInteresserBruker = $pdo->prepare("SELECT interesse FROM bruker_interesser WHERE :id_bruker = id_bruker");
                    $alleInteresserBruker->bindParam(':id_bruker', $_COOKIE['bruker_id']);
                    $alleInteresserBruker->execute();

                    for($i = 0; $rader = $alleInteresserBruker->fetch(); $i++) {
                        ?>
                        <option value="<?php echo $rader['interesse']?>"><?php echo $rader['interesse']?></option>
                        <?php
                    }
                    ?>
                </select>
                <br>
                <input id="knappSlettInteresse" type="submit" value="Slett en interesse" name="submit-slett-interesse">
            </form>
        </aside>

        <aside id="studiumForm">
            <form action="endreprofil.php" method="post">
                <h1 id="studium">Studium</h1>
                <h2 id="header2Studium">Legg til studium på deg selv:</h2>
                <?php
                $resultat = $pdo->prepare("SELECT * FROM alle_studium");
                $resultat->execute();
                $resultatAlleStudium = $resultat->fetchAll(PDO::FETCH_COLUMN, 1);

                $resultatbruker = $pdo->prepare(
                    "SELECT bruker.brukernavn, GROUP_CONCAT(bruker_studium.studium) as 'studium'
                        FROM bruker
                        JOIN bruker_studium ON bruker_studium.id_bruker = bruker.id_bruker
                        WHERE bruker.id_bruker = :id_bruker
                        GROUP BY bruker.id_bruker;"
                );
                $resultatbruker->bindParam(':id_bruker', $_COOKIE['bruker_id']);
                $resultatbruker->execute();
                $resultatBrukerStudium = $resultatbruker->fetch();

                $brukerStudium = $resultatBrukerStudium['studium'];
                $arrayStudium = explode(',', $brukerStudium);
                $valgAvAlleMuligeBrukerStudium = [];

                foreach($resultatAlleStudium as $studium) {
                    if(!in_array($studium, $arrayStudium)){
                        $valgAvAlleMuligeBrukerStudium[] = $studium;
                    }
                }

                if(count($valgAvAlleMuligeBrukerStudium)){
                    ?>
                    <label>
                        <select id="valgtStudium" name="valgt_studium">
                            <?php
                            foreach($valgAvAlleMuligeBrukerStudium as $enMuligStudium) {
                                ?>
                                <option value="<?php echo $enMuligStudium; ?>"><?php echo $enMuligStudium; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </label>
                    <?php
                }
                ?>
                <br>
                <input id="knappLeggTilStudium" type="submit" value="Legg til studium" name="submit-stu-bruker">
            </form>
            <form action="endreprofil.php" method="post">
                <h2 id="header2Studium2">Opprett et nytt studium:</h2>
                <label>
                    <input id="nyttStudium" type="text" name="studium" placeholder="Nytt studium...">
                </label>
                <br>
                <input id="knappLeggTilStudium" type="submit" value="Legg til studium" name="submit-stu">
            </form>

            <form action="endreprofil.php"  method="post">
                <h1 id="endreStudium">Endre studium</h1>
                <label for="valgtStudiumEndre"></label><select id="valgtStudiumEndre" name="endre_studium">
                    <?php
                    $alleStudiumBruker = $pdo->prepare("SELECT studium FROM bruker_studium WHERE :id_bruker = id_bruker");
                    $alleStudiumBruker->bindParam(':id_bruker', $_COOKIE['bruker_id']);
                    $alleStudiumBruker->execute();
                    $brukerStudie = $alleStudiumBruker->fetch();

                    $studiumBrukerIkkeHar = $pdo->prepare("SELECT studium FROM alle_studium WHERE :studium != studium ");
                    $studiumBrukerIkkeHar->bindParam(':studium', $brukerStudie[0]);
                    $studiumBrukerIkkeHar->execute();

                    for($i = 0; $rader = $studiumBrukerIkkeHar->fetch(); $i++) {
                        ?>
                        <option value="<?php echo $rader['studium']?>"><?php echo $rader['studium']?></option>
                        <?php
                    }
                    ?>
                </select>
                <br>
                <input id="knappEndreStudium" type="submit" value="Endre studium" name="submit-endre-studium">
            </form>
        </aside>


        <aside id="imgUpload">
            <form action="include/lastoppbilde.php" method="post" enctype="multipart/form-data">
                <h1 id="lastOpp">Last opp profilbilde</h1>
                <input id="filetoupload" type="file" name="fileToUpload" accept="*/image">
                <br>
                <input id="buttonupload" type="submit" value="Last opp bilde" name="submit">
            </form>
        </aside>

        <aside id="emailForm">
            <form action="endreprofil.php" method="post">
                <h1 id="headerEmail">Endre email:</h1>
                <h2 id="emailText">Skriv inn ny epost</h2>
                <input id="emailInfo" type="email" name="bytt_epost" placeholder="eksempel@hotmail.com" required="" pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}">
                <br>
                <input id="emailEndreButton" type="submit" value="Lagre" name="submit-email">
            </form>
        </aside>

        <aside id="passordForm">
            <h1 id="header1Passord">Bytt passord</h1>
            <form method="post" action="endreprofil.php">
                <h2 id="header2GammeltPassord">Gammelt passord</h2>
                <input id="gammeltPassord" type="password" name="gammelt_passord" placeholder="Gammelt passord" value="" required />
                <h2 id="header2NyttPassord">Nytt passord</h2>
                <input id="nyttPassord" type="password" name="nytt_passord" placeholder="Nytt passord" value=""  required />
                <h2 id="header2NyttPassordIgjen">Skriv inn nytt passord</h2>
                <input id="rePassord" type="password" name="re_passord" placeholder="Gjenta nytt passord" value="" required />
                <br>
                <input type="submit" class="btn" value="Endre Passord" name="endre_passord" />
            </form>
        </aside>

        <aside id="slettForm">
            <form action="endreprofil.php" method="POST" id="slettProfil">
                <h1 id="slettProfil">Slett profil</h1>
                <h2 id="header2slettProfil">Sletter profilen fra nettsiden:</h2>
                <input id="knappSlettProfil" type="submit" name="slettProfil" value="Slett profil" onclick="return confirm('Are you sure you want to do that?');">
            </form>
        </aside>
    </div>


</article>

<footer id="footer">
    <?php
    include("include/footer.html");
    ?>
</footer>

<script>


</script>

</body>
<!-- Denne siden er utviklet av Fredrik Ravndal, Håvard Betten, Ola Bredviken og Fredrik Hulaas, siste gang endret 08.04.2018 -->
<!-- Denne siden er kontrollert av Fredrik Ravndal, siste gang 09.04.2018 -->
</html>
