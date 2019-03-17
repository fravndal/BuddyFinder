<?php
include("include/config.php");
include("include/session.php");

$minePaameldteArrangementer = $pdo->prepare("SELECT a.id, a.arrangement_navn, a.arrangement_beskrivelse, a.fra_dato, a.til_dato
                                                                FROM arrangement as a left join arrangement_paameldte as ap 
                                                                ON a.id = ap.id 
                                                                WHERE ap.id_bruker = :id_bruker && a.til_dato > CURDATE()");
$minePaameldteArrangementer->bindParam(':id_bruker', $_COOKIE['bruker_id']);
$minePaameldteArrangementer->execute();
$listMinePaameldteArrangementer = $minePaameldteArrangementer->fetchAll(PDO::FETCH_ASSOC);


$arrangementer = $pdo->prepare('SELECT id, arrangement_navn, arrangement_beskrivelse, fra_dato, til_dato
                                                                      FROM arrangement
                                                                      WHERE til_dato > CURDATE()');
$arrangementer->execute();
$listArrangementer = $arrangementer->fetchAll(PDO::FETCH_ASSOC);






if(isset($_POST['submit']))  {

	$insertStatement = $pdo->prepare("INSERT INTO arrangement (id_bruker, arrangement_navn, arrangement_beskrivelse, fra_dato, til_dato) VALUES(:id_bruker, :arrangement_navn, :arrangement_beskrivelse, :fra_dato, :til_dato)");
    $insertStatement->bindParam(':id_bruker', $_COOKIE['bruker_id']);
	$insertStatement->bindParam(':arrangement_navn', $_POST['arrangement_navn']);
	$insertStatement->bindParam(':arrangement_beskrivelse', $_POST['arrangement_beskrivelse']);
	$insertStatement->bindParam(':fra_dato', $_POST['fra_dato']);
	$insertStatement->bindParam(':til_dato', $_POST['til_dato']);

	$insertStatement->execute();

    $selectStatement = $pdo->prepare("SELECT id FROM arrangement WHERE id_bruker = :id_bruker && arrangement_navn = :arrangement_navn && arrangement_beskrivelse = :arrangement_beskrivelse && fra_dato = :fra_dato && til_dato = :til_dato");
    $selectStatement->bindParam(':id_bruker', $_COOKIE['bruker_id']);
    $selectStatement->bindParam(':arrangement_navn', $_POST['arrangement_navn']);
    $selectStatement->bindParam(':arrangement_beskrivelse', $_POST['arrangement_beskrivelse']);
    $selectStatement->bindParam(':fra_dato', $_POST['fra_dato']);
    $selectStatement->bindParam(':til_dato', $_POST['til_dato']);
    $selectStatement->execute();
    $arrangement_id = $selectStatement->fetch();


	$insertUser = $pdo->prepare("INSERT INTO arrangement_paameldte (id, id_bruker) VALUES(:id, :id_bruker)");
	$insertUser->bindParam(':id', $arrangement_id[0]);
	$insertUser->bindParam(':id_bruker',$_COOKIE['bruker_id']);
    $insertUser->execute();
    header("location: arrangement.php");


}
if(isset($_POST['submit_paamelding'])) {
    $selectStatement = $pdo->prepare("SELECT id_bruker FROM bruker WHERE brukerNavn = :brukerNavn");
    $selectStatement->bindParam(':brukerNavn', $_SESSION['login_user']);
    $selectStatement->execute();
    $user_id = $selectStatement->fetch();

    $selectStatement = $pdo->prepare("SELECT id FROM arrangement WHERE arrangement_navn = :arrangement_navn");
    $selectStatement->bindParam(':arrangement_navn', $_POST['arrangement_id']);
    $selectStatement->execute();

    $insertStatement = $pdo->prepare("INSERT INTO arrangement_paameldte (id, id_bruker) VALUES(:id, :id_bruker)");
    $insertStatement->bindParam(':id',$_POST['arrangement_id']);
    $insertStatement->bindParam(':id_bruker', $_COOKIE['bruker_id']);
    $insertStatement->execute();
    header("location: arrangement.php");

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
    <link rel="stylesheet" type="text/css" href="css/arrangement/arrangementMobilePortrait.css" media="screen and (min-width: 234px) and (max-width: 480px)" />
    <!--##Device = Low Resolution Tablets, Mobiles (Landscape)-->
    <link rel="stylesheet" type="text/css" href="css/arrangement/arrangementMobileLandscape.css" media="screen and (min-width: 481px) and (max-width: 767px)" />
    <!--##Device = Tablets, Laptops, Desktops (portrait)-->
    <link rel="stylesheet" type="text/css" href="css/arrangement/arrangementTabletPortrait.css" media="screen and (min-width: 768px) and (max-width: 900px)" />
    <!--##Device = Tablets, Laptops, Desktops (Landscape)-->
    <link rel="stylesheet" type="text/css" href="css/arrangement/arrangementTabletLandscape.css" media="screen and (min-width: 901px) and (max-width: 1280px)" />
    <!--##Device = Laptops, Desktops (Landscape)-->
	<link rel="stylesheet" type="text/css" href="css/arrangement/arrangementDesktop.css" media="screen and (min-width: 1281px)">
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

<article id="ytre">

    <div id="kolonne1">
        <aside id="kolonne1Element1">
            <form action="arrangement.php" method="post">
                <h1>Meld deg på arrangementer</h1>
                <?php
                $result = $pdo->prepare("SELECT *
                                        FROM arrangement_paameldte, arrangement
                                        WHERE arrangement_paameldte.id = arrangement.id and til_dato > CURDATE()
                                        GROUP BY arrangement_paameldte.id
                                        HAVING sum(arrangement_paameldte.id_bruker = :id_bruker) = 0;");
                $result->bindParam(':id_bruker', $_COOKIE['bruker_id']);
                $result->execute();
                $resultatAlleArrangementer = $result->fetchAll(PDO::FETCH_ASSOC);

                ?>

                <select class="rullGardin" name="arrangement_id">
                    <?php foreach($resultatAlleArrangementer as $arrangement) { ?>
                        <option value='<?php echo $arrangement["id"]; ?>'><?php echo $arrangement['arrangement_navn']; ?></option>
                        <?php
                    }
                    ?> </select>
                <?php
                ?>
                <button id="buttonMeldPaa" class="button" name="submit_paamelding" type="submit">Meld deg på arrangement</button>


                <h1 id="headerOpprett">Opprett et nytt arrangement</h1>
                <div id="form">
                <p class="arrangement"><label for="arrangement_navn">Navn på arrangementet</label></p>
                <input id="arrangementNavn" name="arrangement_navn" placeholder="Navn på arrangementet" type="text">
                <p class="arrangement"><label for="arrangement_beskrivelse">Beskrivelse på arrangementet</label></p>
                <input id="arrangementBeskrivelse" name="arrangement_beskrivelse" placeholder="Beskrivelse på arrangementet" type="text">
                <p class="arrangement"><label for="fra_dato">Velg dato og tid for start av arrangementet</label></p>
                    <label for="arrangementFra"></label><input id="arrangementFra" type="datetime-local" name="fra_dato">
                <p class="arrangement"><label for="til_dato">Velg dato og tid for slutt av arrangementet</label></p>
                    <label for="arrangementTil"></label><input id="arrangementTil" type="datetime-local" name="til_dato">
                <br>
                <button id="buttonOpprett" class="button" name="submit" type="submit">Opprett arrangement </button>
                </div>
            </form>
        </aside>
    </div>
    <div id="kolonne2">
        <aside>
            <h1>Mine påmeldte arrangementer</h1>
            <?php
            if(!empty($listMinePaameldteArrangementer)) {


                ?>
                <table border="1" cellspacing="2" cellpadding="5" id="tableMinePaameldteArrangementer">
                    <thead id="tableheadMinePaameldteArrangementer">
                    <tr id="tablerow1MinePaameldteArrangementer">
                        <th id="tableheader1MinePaameldteArrangementer">Arrangementnavn</th>
                        <th id="tableheader2MinePaameldteArrangementer">Arrangementbeskrivelse</th>
                        <th id="tableheader3MinePaameldteArrangementer">Start</th>
                        <th id="tableheader4MinePaameldteArrangementer">Slutt</th>
                        <th id="tableheader5MinePaameldteArrangementer">Deltakere</th>
                    </tr>
                    </thead>

                    <tbody id="tablebodyMinePaameldteArrangementer">
                    <?php
                    foreach($listMinePaameldteArrangementer as $arrangement){
                        $deltakere = $pdo->prepare('SELECT COUNT(ap.id) AS Deltakere FROM arrangement_paameldte AS ap LEFT JOIN arrangement as a ON a.id = ap.id WHERE ap.id=:id and a.til_dato > curdate()');
                        $deltakere->bindParam(':id', $arrangement['id']);
                        $deltakere->execute();
                        $nummer = $deltakere->fetch();



                        ?>
                        <tr id="tablerow2MinePaameldteArrangementer">
                            <td id="tabledefinition1MinePaameldteArrangementer"><label><?php echo $arrangement['arrangement_navn']; ?></label></td>
                            <td id="tabledefinition2MinePaameldteArrangementer"><label><?php echo $arrangement['arrangement_beskrivelse']; ?></label></td>
                            <td id="tabledefinition3MinePaameldteArrangementer"><label><?php echo $arrangement['fra_dato']; ?></label></td>
                            <td id="tabledefinition4MinePaameldteArrangementer"><label><?php echo $arrangement['til_dato']; ?></label></td>
                            <td id="tabledefinition5MinePaameldteArrangementer"><label><?php echo $nummer[0]; ?></label></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            } else print "<p>Du er ikke meldt på noen arrangementer.</p>"
            ?>
        </aside>
        <aside id="rammeAktiveArrangementer">
            <h1>Aktive arrangementer</h1>
            <?php
            if(!empty($listArrangementer)) {
                ?>
                <table border="1" cellspacing="2" cellpadding="5" id="tableAktiveArrangementer">
                    <thead id="tableheadAktiveArrangementer">
                    <tr id="tablerow1AktiveArrangementer">
                        <th id="tableheader1AktiveArrangementer">Arrangementnavn</th>
                        <th id="tableheader2AktiveArrangementer">Arrangementbeskrivelse</th>
                        <th id="tableheader3AktiveArrangementer">Start</th>
                        <th id="tableheader4AktiveArrangementer">Slutt</th>
                        <th id="tableheader5AktiveArrangementer">Deltakere</th>
                    </tr>
                    </thead>

                    <tbody id="tablebodyAktiveArrangementer">
                    <?php
                    foreach($listArrangementer as $arrangement){
                        $deltakere = $pdo->prepare('SELECT COUNT(ap.id) AS Deltakere FROM arrangement_paameldte AS ap LEFT JOIN arrangement as a ON a.id = ap.id WHERE ap.id=:id and a.til_dato > curdate()');
                        $deltakere->bindParam(':id', $arrangement['id']);
                        $deltakere->execute();
                        $nummer = $deltakere->fetch();
                        ?>
                        <tr id="tablerow2AktiveArrangementer">
                            <td id="tabledefinition1AktiveArrangementer"><label><?php echo $arrangement['arrangement_navn']; ?></label></td>
                            <td id="tabledefinition2AktiveArrangementer"><label><?php echo $arrangement['arrangement_beskrivelse']; ?></label></td>
                            <td id="tabledefinition3AktiveArrangementer"><label><?php echo $arrangement['fra_dato']; ?></label></td>
                            <td id="tabledefinition4AktiveArrangementer"><label><?php echo $arrangement['til_dato']; ?></label></td>
                            <td id="tabledefinition5AktiveArrangementer"><label><?php echo $nummer[0]; ?></label></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            } else print "<p>Ingen aktive arrangementer for øyeblikket.</p>"
            ?>
        </aside>
    </div>



</article>

<footer id="footer">
    <?php
    include("include/footer.html");
    ?>
</footer>

</body>
<!-- Denne siden er utviklet av fredrik ravndal -->
<!-- Denne siden er kontrollert av fredrik hulaas -->
</html>
