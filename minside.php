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

$isPressedInt = false;
$isPressedStu = false;
$isPressed = false;
$sokeord = '';
if(isset($_POST['submit-stu'])) {
    $sokeord = $_POST['valgt_studium'];

    $sokEtterStudium = $pdo->prepare('SELECT brukernavn
                                FROM bruker AS br LEFT JOIN bruker_studium AS bs
                                ON br.id_bruker = bs.id_bruker
                                WHERE studium LIKE :sokeord AND profil_synlig = "1"
                                ORDER BY brukernavn');
    $sokEtterStudium->bindParam(':sokeord', $sokeord);
    $sokEtterStudium->execute();
    $resultatAlleStudium = $sokEtterStudium->fetchAll(PDO::FETCH_ASSOC);
    $isPressedStu = true;
}
if(isset($_POST['submit-int'])) {
    $sokeord = $_POST['valgt_interesse'];

    $search_statement = $pdo->prepare('SELECT brukernavn
                                FROM bruker AS br LEFT JOIN bruker_interesser AS bi
                                ON br.id_bruker = bi.id_bruker
                                WHERE interesse LIKE :sokeord AND profil_synlig = "1"
                                ORDER BY brukernavn');
    $search_statement->bindParam(':sokeord', $sokeord);
    $search_statement->execute();
    $resultatAlleInteresser = $search_statement->fetchAll(PDO::FETCH_ASSOC);
    $isPressedInt = true;
}

if(isset($_POST['submit-sok'])) {
    $sokeord = $_POST['sok'];
    $sokEtterBrukernavn = $pdo->prepare('SELECT brukernavn
                                FROM bruker AS br LEFT JOIN bruker_studium AS bs
                                ON br.id_bruker = bs.id_bruker
                                WHERE brukernavn LIKE :sokeord
                                ORDER BY brukernavn');
    $sokEtterBrukernavn->bindParam(':sokeord', $sokeord);
    $sokEtterBrukernavn->execute();
    $resultatAlleBrukernavn = $sokEtterBrukernavn->fetchAll(PDO::FETCH_ASSOC);
    $isPressed = true;
    if(!empty($resultatAlleBrukernavn)){
        setcookie('profil_id', $_POST['sok']);
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
    <link rel="stylesheet" type="text/css" href="css/minside/minsideMobilePortrait.css" media="screen and (min-width: 234px) and (max-width: 480px)" />
    <!--##Device = Low Resolution Tablets, Mobiles (Landscape)-->
    <link rel="stylesheet" type="text/css" href="css/minside/minsideMobileLandscape.css" media="screen and (min-width: 481px) and (max-width: 767px)" />
    <!--##Device = Tablets, Laptops, Desktops (portrait)-->
    <link rel="stylesheet" type="text/css" href="css/minside/minsideTabletPortrait.css" media="screen and (min-width: 768px) and (max-width: 900px)" />
    <!--##Device = Tablets, Laptops, Desktops (Landscape)-->
    <link rel="stylesheet" type="text/css" href="css/minside/minsideTabletLandscape.css" media="screen and (min-width: 901px) and (max-width: 1280px)" />
    <!--##Device = Laptops, Desktops (Landscape)-->
	<link rel="stylesheet" type="text/css" href="css/minside/minsideDesktop.css" media="screen and (min-width: 1281px)">
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

<div id="ytre">

        <div id="kolonne1">
            <aside id="rammeBilde">
                <article id="rammeBrukernavn">
                    <h1 id="overskrift1Bruker"> <?php echo $_COOKIE['user_id'] ?> </h1>
                </article>
                <?php
                $picturepathstatement = $pdo->prepare(
                        "SELECT br.id_bruker, brukernavn, bilde
                                  FROM bruker AS br LEFT JOIN bruker_bilde AS bb
                                  ON br.id_bruker=bb.id_bruker
                                  WHERE brukernavn = :brukernavn"
                );
                $picturepathstatement->bindParam(':brukernavn', $_COOKIE['user_id']);
                $picturepathstatement->execute();
                $emptypicture = "picture/minside/profilepicture.jpg";
                $choose = 0;
                for($i=0; $r = $picturepathstatement->fetch(); $i++){
                    if ($r['bilde']) {
                        $picturepath = $r['bilde'];
                        $choose = 1;
                    }
                }
                ?>
                <img id="bildeProfil" src="<?php if($choose == 1){echo $picturepath;}else{echo $emptypicture;} ?>" alt="Profilbilde" />
            </aside>



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
            <div id="textBox">
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
                <textarea id="textBeskrivelse" rows="4" cols="50" readonly="readonly"><?php echo $row['beskrivelse']; ?></textarea>
            <?php
            }
            ?>
            </div>
            <div id="parentTable">
                <div id="rammeInteresser" style="overflow-y:auto;">
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
                </div>

                <div id="rammeStudium" style="overflow-y:auto;">
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
                </div>
            </div>
        </div>

        <div id="kolonne3">
            <aside>
                <table border="1" cellspacing="2" cellpadding="5" id="tableMinePaameldteArrangementer">
                    <thead id="tableheadMinePaameldteArrangementer">
                    <tr id="tablerow1MinePaameldteArrangementer">
                        <th id="tableheaderMinePaameldteArrangementer">Mine påmeldte arrangementer</th>
                    </tr>
                    </thead>

                    <tbody id="tablebodyMinePaameldteArrangementer">
                    <?php
                    // Spørring for å få alle interesser på brukeren som er logget inn
                    $minePaameldteArrangementer = $pdo->prepare("SELECT a.arrangement_navn
                                                            FROM arrangement as a left join arrangement_paameldte as ap
                                                            ON a.id = ap.id
                                                            WHERE ap.id_bruker = :id_bruker && a.til_dato > CURDATE()");
                    $minePaameldteArrangementer->bindParam(':id_bruker', $_COOKIE['bruker_id']);
                    $minePaameldteArrangementer->execute();
                    for($i=0; $mittPaameldteArrangement = $minePaameldteArrangementer->fetch(); $i++){
                        ?>
                        <tr id="tablerow2MinePaameldteArrangementer">
                            <td id="tabledefinitionMinePaameldteArrangementer"><label><?php echo $mittPaameldteArrangement['arrangement_navn']; ?></label></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </aside>
        </div>

        <div id="kolonne4" id="sok">
            <form id="formSok" name="SEARCH" method="post" action="minside.php">
                <h1 id="overskrift1Sok">Søk på et brukernavn, interesse eller studium</h1>
                <?php
                $resultatInt = $pdo->prepare("SELECT * FROM alle_interesser");
                $resultatInt->execute();
                $resultatAlleInteresserSok = $resultatInt->fetchAll(PDO::FETCH_COLUMN, 1);

                ?>
                <h2 id="overskrift2Sok1">Søk på en interesse</h2>
                <label for="rullGardin"></label>
                <select id="rullGardin" name="valgt_interesse">
                    <?php
                    foreach($resultatAlleInteresserSok as $enMuligInteresse) {
                        ?>
                        <option value="<?php echo $enMuligInteresse; ?>"><?php echo $enMuligInteresse; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <input type='submit' value='Søk på interesse' name="submit-int" id="sokKnapp">
            </form>
            <form id="formSok" name="SEARCH" method="post" action="minside.php">
                <br>
                <?php
                $resultatStu = $pdo->prepare("SELECT * FROM alle_studium");
                $resultatStu->execute();
                $resultatAlleStudiumSok = $resultatStu->fetchAll(PDO::FETCH_COLUMN, 1);

                ?>
                <h2 id="overskrift2Sok2">Søk på et studium</h2>
                <label for="rullGardin"></label>
                <select id="rullGardin" name="valgt_studium">
                    <?php
                    foreach($resultatAlleStudiumSok as $etMuligStudium) {
                        ?>
                        <option value="<?php echo $etMuligStudium; ?>"><?php echo $etMuligStudium; ?></option>
                        <?php
                    }
                    ?>
                </select>
                <input type='submit' value='Søk på studium' name="submit-stu" id="sokKnapp">
            </form>
            <form id="formSok" name="SEARCH" method="post" action="minside.php">



                <h2 id="overskrift2Sok3">Søk på brukernavn</h2>
                <input type='text' name='sok' value="" id='keyword' maxlength='25' placeholder="Skriv inn et brukernavn...">
                <input type='submit' value='Søk på brukernavn' name="submit-sok" id="sokKnapp">
            </form>

            <?php
            if($isPressedInt) {
                ?>
                <div id="rammeOverflowSok" style="overflow-y:auto;">
                    <table border="1" cellspacing="2" cellpadding="5" id="tableSok">
                        <thead id="tableheadSok">
                        <tr id="tablerow1Sok">
                            <th id="tableheaderSok">Alle brukernavn på søkt
                                <?php
                                if(!empty($resultatAlleInteresser)) {
                                    echo 'interesse';
                                }
                                ?>
                                :
                                <?php echo $sokeord ?>
                            </th>
                        </tr>
                        </thead>

                        <tbody id="tablebodySok">
                        <?php
                        foreach($resultatAlleInteresser as $brukernavnInteresser) {
                            ?>
                            <tr id="tablerow2Sok">
                                <td class="tabledefinitionSok"><?php echo $brukernavnInteresser['brukernavn']; ?></td>
                            </tr>
                            <?php
                        }
                        ?>

                        </tbody>
                    </table>
                </div>

            <?php
            }
            ?>
            <?php

            if($isPressedStu) {
                ?>
                <div id="rammeOverflowSok" style="overflow-y:auto;">
                    <table border="1" cellspacing="2" cellpadding="5" id="tableSok">
                        <thead id="tableheadSok">
                        <tr id="tablerow1Sok">
                            <th id="tableheaderSok">Alle brukernavn på søkt
                                <?php
                                if(!empty($resultatAlleStudium)) {
                                    echo 'studium';
                                }
                                ?>
                                :
                                <?php echo $sokeord ?>
                            </th>
                        </tr>
                        </thead>

                        <tbody id="tablebodySok">
                        <?php
                        foreach($resultatAlleStudium as $brukernavnStudium) {
                            ?>
                            <tr id="tablerow2Sok">
                                <td id="tabledefinitionSok"><?php echo $brukernavnStudium['brukernavn']; ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
            }
            ?>

            <?php
            if($isPressed) {
                ?>
                <div id="rammeOverflowSok" style="overflow-y:auto;">
                    <table border="1" cellspacing="2" cellpadding="5" id="tableSok">
                        <thead id="tableheadSok">
                        <tr id="tablerow1Sok">
                            <th id="tableheaderSok">Alle brukernavn på søkt
                                <?php
                                if(!empty($resultatAlleBrukernavn)){
                                    echo 'brukernavn';
                                }
                                ?>
                                :
                                <?php echo $sokeord ?>
                            </th>
                        </tr>
                        </thead>

                        <tbody id="tablebodySok">
                        <?php
                        foreach($resultatAlleBrukernavn as $brukernavnSøk) {
                            ?>
                            <tr id="tablerow2Sok">
                                <td id="tabledefinitionSok"><label><a href="profil.php"><?php echo $brukernavnSøk['brukernavn']; ?></a></label></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
            }
            ?>
        </div>
</div>

<footer>
    <?php
    include("include/footer.html");
    ?>
</footer>

<script type="text/javascript">

</script>

</body>
<!-- Denne siden er utviklet av Fredrik Ravndal, siste gang endret 09.04.2018 -->
<!-- Denne siden er kontrollert av Fredrik Ravndal, siste gang 09.04.2018 -->
</html>
