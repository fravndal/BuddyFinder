<?php
include("include/config.php");

if ($_COOKIE['bruker_type'] !== '1') {
	$insertStatement = $pdo->prepare("INSERT INTO rapport values (null, :brukernavn, 'Forsøk på å komme inn på admin-side', NOW())");
	$insertStatement->bindParam(':brukernavn', $_COOKIE['user_id']);
	$insertStatement->execute();

	$_SESSION['tilbakemelding'] = "Du er ikke autoriesert for denne siden, du har blitt rapportert!";
 	$_SESSION['tidsholder'] = time();

	header("location: default.php");
}

if(isset($_POST['submit_autorisering'])) {
    $autoriserBruker = $pdo->prepare('UPDATE bruker SET profil_admin="1" WHERE brukernavn = :brukernavn');
    $autoriserBruker->bindParam(':brukernavn', $_POST['autorisering_bruker']);
    $autoriserBruker->execute();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Regler</title>
	<meta charset="utf8">
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">
	<link rel="stylesheet" type="text/css" href="css/admin/admin_autorisering-MobilePortrait.css" media="screen and (min-width: 234px) and (max-width: 480px)" />
	<!--##Device = Low Resolution Tablets, Mobiles (Landscape)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_autorisering-MobileLandscape.css" media="screen and (min-width: 481px) and (max-width: 750px)" />
	<!--##Device = Tablets, Laptops, Desktops (portrait)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_autorisering-TabletPortrait.css" media="screen and (min-width: 751px) and (max-width: 900px)" />
	<!--##Device = Tablets, Laptops, Desktops (Landscape)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_autorisering-TabletLandscape.css" media="screen and (min-width: 901px) and (max-width: 1280px)" />
	<link rel="stylesheet" type="text/css" href="css/admin/admin_autorisering-Desktop.css" media="screen and (min-width: 1281px)">
</head>
<body>
  <aside>
    <form action="autorisering.php" method="post">
      <h2>Autoriser bruker</h2>
      <label for="begrunnelse">Velg bruker du vil kvalifisere til admin</label> <br>
      <select name="autorisering_bruker">
          <?php
          $dataFraFil = $pdo->prepare("SELECT brukernavn FROM bruker WHERE profil_admin='0'");
          $dataFraFil->execute();


          for($i = 0; $rader = $dataFraFil->fetch(); $i++) {
              ?>
              <option value="<?php echo $rader['brukernavn']?>"><?php echo $rader['brukernavn']?></option>
              <?php
          }
          ?>
      </select>
      <input type="submit" value="Gjør til admin" name="submit_autorisering">
    </form>
  </aside>

</body>
<!-- Denne siden er laget av Håvard Betten-->
<!-- Denne siden er kontrollert av Fredrik Ravndal sist 09.04.2018 -->
</html>
