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
?>
<!DOCTYPE html>
<html>
<head>
	<title>BuddyFinder</title>
	<meta charset="utf8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">
  <!-- Mobil  -->
  <link rel="stylesheet" type="text/css" href="css/admin/admin-mobile.css" media="only screen and (min-width: 268px) and (max-width: 768px)" />

  <!-- desktop  -->
  <link rel="stylesheet" type="text/css" href="css/admin/admin-desktop.css" media="screen and (min-width: 750px)" />
</head>

<body>
<nav id="meny">
    <div id="drop-meny" class="dropdown-meny">
        <ul>
            <li><a href="admin_adm_brukere.php" id="administrerer" target="iframe">Administrer brukere</a></li>
            <li><a href="admin_rapport.php" id="rapporting" target="iframe" id="homepage" style="font-family: Arial;">Rapporter</a></li>
            <li><a href="admin_regler.php" id="regleradm" target="iframe">Administrer Regler</a></li>
            <li><a href="autorisering.php" id="auto" target="iframe">Autorisering</a></li>
            <li><a href="minside.php" id="mins">Tilbake til front-end</a></li>
            <li><a href="logout.php" id="logg-ut">Logg ut</a></li>
        </ul>
    </div>
</nav>

<p>Velkommen til back-end delen av applikasjon. Her kan du som administrator administrere diverse aspekter ved applikasjonen.</p>
<article id="innhold">
    <iframe src="admin_rapport.php" name="iframe" style="height: 500px; width: 99.4%; margin-left: auto; margin-right: auto; margin: 0; padding: 0;"></iframe>
</article>

<footer id="footer">
    <?php
    include("include/footer.html");
    ?>
</footer>
</body>
<!-- Denne siden er laget av Fredrik Hulaas siste gang endret 08.04.2018. -->
<!-- Denne siden er kontrollert av Ola Bredviken -->
</html>
