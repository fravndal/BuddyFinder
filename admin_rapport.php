<?php
include("include/config.php");
include("include/session.php");

if ($_COOKIE['bruker_type'] !== '1') {
	$insertStatement = $pdo->prepare("INSERT INTO rapport values (null, :brukernavn, 'Forsøk på å komme inn på admin-side', NOW())");
	$insertStatement->bindParam(':brukernavn', $_COOKIE['user_id']);
	$insertStatement->execute();

	$_SESSION['tilbakemelding'] = "Du er ikke autoriesert for denne siden, du har blitt rapportert!";
 	$_SESSION['tidsholder'] = time();

	header("location: default.php");
}

$isPressed = False;
$isPressedBrukere = False;
$isPressedAdmin = False;

if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
    if(isset($_POST['submit-alle'])) {

        $tabell_brukere = $pdo->prepare('SELECT b.brukernavn, b.epost, r.begrunnelse AS \'rapport.begrunnelse\', ba.begrunnelse AS \'melding.begrunnelse\', bk.begrunnelse AS \'karantene.begrunnelse\'
                                        FROM bruker AS b
                                        LEFT JOIN rapport AS r ON b.brukernavn = r.brukernavn
                                        LEFT JOIN bruker_advarsel AS ba ON ba.brukernavn = b.brukernavn
                                        LEFT JOIN bruker_karantene AS bk ON bk.brukernavn = b.brukernavn
                                        GROUP BY brukernavn;');
        $tabell_brukere->execute();
        $tabell = $tabell_brukere->fetchAll(PDO::FETCH_ASSOC);
        $isPressed = True;
        $isPressedBrukere = True;
        $isPressedAdmin = False;
    }
    if(isset($_POST['submit-misbruk'])) {
        $tabell_brukere = $pdo->prepare('SELECT b.brukernavn, b.epost, r.begrunnelse AS \'rapport.begrunnelse\', ba.begrunnelse AS \'melding.begrunnelse\', bk.begrunnelse AS \'karantene.begrunnelse\'
                                        FROM bruker AS b
                                        LEFT JOIN rapport AS r ON b.brukernavn = r.brukernavn
                                        LEFT JOIN bruker_advarsel AS ba ON ba.brukernavn = b.brukernavn
                                        LEFT JOIN bruker_karantene AS bk ON bk.brukernavn = b.brukernavn
                                        WHERE ba.begrunnelse IS NOT NULL or bk.begrunnelse IS NOT NULL or r.begrunnelse IS NOT NULL
                                        GROUP BY brukernavn;');
        $tabell_brukere->execute();
        $tabell = $tabell_brukere->fetchAll(PDO::FETCH_ASSOC);
        $isPressed = True;
        $isPressedBrukere = True;
        $isPressedAdmin = False;
    }
    if(isset($_POST['submit-sok'])) {
        $brukernavn = $_POST['sok'];
        $tabell_brukere = $pdo->prepare('SELECT b.brukernavn, b.epost, r.begrunnelse AS \'rapport.begrunnelse\', ba.begrunnelse AS \'melding.begrunnelse\', bk.begrunnelse AS \'karantene.begrunnelse\'
                                        FROM bruker AS b
                                        LEFT JOIN rapport AS r ON b.brukernavn = r.brukernavn
                                        LEFT JOIN bruker_advarsel AS ba ON ba.brukernavn = b.brukernavn
                                        LEFT JOIN bruker_karantene AS bk ON bk.brukernavn = b.brukernavn
                                        WHERE b.brukernavn = :brukernavn');
        $tabell_brukere->bindParam(':brukernavn', $brukernavn);
        $tabell_brukere->execute();
        $tabell = $tabell_brukere->fetchAll(PDO::FETCH_ASSOC);
        $isPressed = True;
        $isPressedBrukere = True;
        $isPressedAdmin = False;
    }
    if(isset($_POST['submit-admin'])) {
        $tabell_brukere = $pdo->prepare('SELECT brukernavn, epost
                                        FROM bruker
                                        WHERE profil_admin="1"');
        $tabell_brukere->execute();
        $tabell = $tabell_brukere->fetchAll(PDO::FETCH_ASSOC);
        $isPressedAdmin = True;
        $isPressed = True;
        $isPressedBrukere = False;
    }
}



?>
<!DOCTYPE html>
<html>
<head>
	<title>BuddyFinder</title>
	<meta charset="utf8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">
	<!--##Device = Mobiles (Portrait)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_rapportering-MobilePortrait.css" media="screen and (min-width: 234px) and (max-width: 480px)" />
	<!--##Device = Low Resolution Tablets, Mobiles (Landscape)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_rapportering-MobileLandscape.css" media="screen and (min-width: 481px) and (max-width: 750px)" />
	<!--##Device = Tablets, Laptops, Desktops (portrait)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_rapportering-TabletPortrait.css" media="screen and (min-width: 751px) and (max-width: 900px)" />
	<!--##Device = Tablets, Laptops, Desktops (Landscape)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_rapportering-TabletLandscape.css" media="screen and (min-width: 901px) and (max-width: 1280px)" />
	<link rel="stylesheet" type="text/css" href="css/admin/admin_rapportering-Desktop.css" media="screen and (min-width: 1281px)">
</head>

<body>



    <article class="rammeBrukere">
        <form class="formBrukere" name="brukere" method="post" action="admin_rapport.php">
            <h1 class="overskrift1Brukere">Oversikt over brukere med misbruk</h1>
            <input type='text' name='sok' id='keyword' maxlength='25' placeholder="Søk på brukernavn...">
            <input type='submit' value='Søk' name="submit-sok">
            <br>
            <input type='submit' value='Oversikt over alle brukere' name="submit-alle">
            <input type='submit' value='Oversikt over brukere med misbruk eller karantene' name="submit-misbruk">
            <input type='submit' value='Oversikt over alle administratorer' name="submit-admin">
        </form>

        <div class="rammeOverflowBrukere" style="overflow-y:auto;">
            <?php
            if($isPressed) {
            ?>
            <table border="1" cellspacing="2" cellpadding="5" class="tableSok">
                <thead class="tableheadBruker">
                <?php
                if($isPressedBrukere) {
                ?>
                <tr class="tablerow1Bruker">
                    <th class="tableheaderBruker">Brukernavn</th>
                    <th class="tableheaderBruker">Epost</th>
                    <th class="tableheaderBruker">Rapportering på bruker</th>
                    <th class="tableheaderBruker">Advarsel til bruker</th>
                    <th class="tableheaderBruker">Årsak til karantene</th>
                </tr>
                    <?php
                }
                if($isPressedAdmin) {
                    ?>
                    <tr class="tablerow1Bruker">
                        <th class="tableheaderBruker">Brukernavn</th>
                        <th class="tableheaderBruker">Epost</th>
                    </tr>
                    <?php
                }
                ?>
                </thead>



                    <tbody class="tablebodySok">
                    <?php
                    if($isPressed) {
                        if($isPressedBrukere) {
                            foreach ($tabell as $tabell_bruker) {
                                ?>
                                <tr class="tablerow2Sok">
                                    <td class="tabledefinitionSok"><label><?php echo $tabell_bruker['brukernavn']; ?></label></td>
                                    <td class="tabledefinitionSok"><label><?php echo $tabell_bruker['epost']; ?></label></td>
                                    <td class="tabledefinitionSok"><label><?php echo $tabell_bruker['rapport.begrunnelse']; ?></label></td>
                                    <td class="tabledefinitionSok"><label><?php echo $tabell_bruker['melding.begrunnelse']; ?></label></td>
                                    <td class="tabledefinitionSok"><label><?php echo $tabell_bruker['karantene.begrunnelse']; ?></label></td>
                                </tr>
                                <?php
                            }
                            ?>

                            <?php
                        }
                        ?>

                    <?php
                    if($isPressedAdmin) {
                        foreach ($tabell as $tabell_bruker) {
                            ?>
                            <tr class="tablerow2Sok">
                                <td class="tabledefinitionSok"><label><?php echo $tabell_bruker['brukernavn']; ?></label></td>
                                <td class="tabledefinitionSok"><label><?php echo $tabell_bruker['epost']; ?></label></td>
                            </tr>
                            <?php
                        }
                        ?>
                        <?php
                    }
                    ?>


                    </tbody>
                    <?php
                }
                }
                ?>
            </table>
        </div>
    </article>

</body>
<!-- Denne siden er utviklet av Fredrik Ravndal, siste gang endret 09.04.2018 -->
<!-- Denne siden er kontrollert av Fredrik Ravndal, siste gang 09.04.2018 -->
</html>
