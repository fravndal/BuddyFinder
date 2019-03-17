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

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  if (isset($_POST['submit_bann_bruker'])) {
    $bannBruker = $pdo->prepare("INSERT INTO bruker_karantene (brukernavn, admin, begrunnelse, fra_dato_ban, til_dato_ban) VALUES(:brukernavn, :admin, :begrunnelse, NOW(), NULL)");
    $bannBruker->bindParam(':brukernavn', $_POST['bann_bruker']);
    $bannBruker->bindParam(':admin', $_COOKIE['user_id']);
    $bannBruker->bindParam(':begrunnelse', $_POST['begrunnelse']);
    $bannBruker->execute();
  }

  if (isset($_POST['submit_advar_bruker'])) {
    $bannBruker = $pdo->prepare("INSERT INTO bruker_advarsel (brukernavn, admin, begrunnelse, dato, til_dato) VALUES(:brukernavn, :admin, :begrunnelse, NOW(), NULL)");
    $bannBruker->bindParam(':brukernavn', $_POST['advar_bruker']);
    $bannBruker->bindParam(':admin', $_COOKIE['user_id']);
    $bannBruker->bindParam(':begrunnelse', $_POST['begrunnelse-adv']);
    $bannBruker->execute();
  }

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
	<!--##Device = Mobiles (Portrait)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_adm_brukere-MobilePortrait.css" media="screen and (min-width: 234px) and (max-width: 480px)" />
	<!--##Device = Low Resolution Tablets, Mobiles (Landscape)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_adm_brukere-MobileLandscape.css" media="screen and (min-width: 481px) and (max-width: 750px)" />
	<!--##Device = Tablets, Laptops, Desktops (portrait)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_adm_brukere-TabletPortrait.css" media="screen and (min-width: 751px) and (max-width: 900px)" />
	<!--##Device = Tablets, Laptops, Desktops (Landscape)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_adm_brukere-TabletLandscape.css" media="screen and (min-width: 901px) and (max-width: 1280px)" />
	<link rel="stylesheet" type="text/css" href="css/admin/admin_adm_brukere-Desktop.css" media="screen and (min-width: 1281px)">
</head>
<body>
  <aside>
    <form action="admin_adm_brukere.php" method="post">
      <h2>Bann bruker</h2>
      <label for="begrunnelse">Skriv begrunnelse for karantene </label>
      <input type="text" value="" name="begrunnelse" style="display: block;">
      <select name="bann_bruker">
          <?php
          $dataFraFil = $pdo->prepare("SELECT brukernavn FROM bruker WHERE brukernavn != '' AND brukernavn NOT IN (SELECT brukernavn FROM bruker_karantene)");
          $dataFraFil->execute();

          for($i = 0; $rader = $dataFraFil->fetch(); $i++) {
              ?>
              <option value="<?php echo $rader['brukernavn']?>"><?php echo $rader['brukernavn']?></option>
              <?php
          }
          ?>
      </select>
      <input type="submit" value="Bann bruker" name="submit_bann_bruker">

      <h2>Gi advarsel til bruker</h2>
      <label for="begrunnelse-adv">Skriv begrunnelse for advarsel </label>
      <input type="text" value="" name="begrunnelse-adv" style="display: block;">
      <select name="advar_bruker">
          <?php
          $dataFraFil = $pdo->prepare("SELECT brukernavn FROM bruker WHERE brukernavn != '' AND brukernavn NOT IN (SELECT brukernavn FROM bruker_advarsel)");
          $dataFraFil->execute();

          for($i = 0; $rader = $dataFraFil->fetch(); $i++) {
              ?>
              <option value="<?php echo $rader['brukernavn']?>"><?php echo $rader['brukernavn']?></option>
              <?php
          }
          ?>
      </select>
      <input type="submit" value="Advar bruker" name="submit_advar_bruker">
    </form>
  </aside>


</body>
<!-- Denne siden er laget av Fredrik Hulaas og Ola Bredviken siste gang endret 08.04.2018. -->
<!-- Denne siden er kontrollert av Håvard Bredviken og Fredrik Ravndal -->
</html>
