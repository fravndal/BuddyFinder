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
  if (isset($_POST['submit_regel'])) {
    $insertStatement = $pdo->prepare("INSERT INTO regler(regel) values (:regel)");
    $insertStatement->bindParam(':regel', $_POST['input_regel']);
    $insertStatement->execute();
  }

	if (isset($_POST['submit_regelEndre'])) {
    $updateStatement = $pdo->prepare("UPDATE regler SET regel=:regel WHERE regel=:regel2;");
		$updateStatement->bindParam(':regel', $_POST['endre_regel']);
    $updateStatement->bindParam(':regel2', $_POST['slett_regel']);
    $updateStatement->execute();
  }

  if (isset($_POST['submit_slett_regel'])) {
    $slettRegel = $pdo->prepare("DELETE FROM regler WHERE regel = :regel");
    $slettRegel->bindParam(':regel', $_POST['slett_regel']);
    $slettRegel->execute();
  }
}
$dataFraFil = $pdo->prepare("SELECT regel FROM regler WHERE regel != ''");
$dataFraFil->execute();
$data = $dataFraFil->fetchALL();

/*die(var_dump($data));*/
$skriveFil = fopen("testfil.html", "w") or die("Kan ikke åpne filen!");
foreach($data as $regler) {
  $skriv = $regler['regel'] . "<br>";
  fwrite($skriveFil, $skriv);
}
fclose($skriveFil);

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
	<link rel="stylesheet" type="text/css" href="css/admin/admin_regler-MobilePortrait.css" media="screen and (min-width: 234px) and (max-width: 480px)" />
	<!--##Device = Low Resolution Tablets, Mobiles (Landscape)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_regler-MobileLandscape.css" media="screen and (min-width: 481px) and (max-width: 750px)" />
	<!--##Device = Tablets, Laptops, Desktops (portrait)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_regler-TabletPortrait.css" media="screen and (min-width: 751px) and (max-width: 900px)" />
	<!--##Device = Tablets, Laptops, Desktops (Landscape)-->
	<link rel="stylesheet" type="text/css" href="css/admin/admin_regler-TabletLandscape.css" media="screen and (min-width: 901px) and (max-width: 1280px)" />
	<link rel="stylesheet" type="text/css" href="css/admin/admin_regler-Desktop.css" media="screen and (min-width: 1281px)">
</head>

<body>

<article id="ramme">
    <article id="content">
        <h1>Regler for bruk av siden:</h1>
        <table border="1" cellspacing="2" cellpadding="5" class="tableSok">
            <thead class="tableheadSok">
            <tr class="tablerow1Sok">
                <th class="tableheaderSok">Alle regler
                </th>
            </tr>
            </thead>

            <tbody class="tablebodySok">
              <?php
              foreach($data  as $regler) {
                  ?>
                  <tr class="tablerow2Sok">
                      <td class="tabledefinitionSok"><label><?php echo $regler['regel']; ?></label></td>
                  </tr>
                  <?php
              }
              ?>
            </tbody>
        </table>
	</article>
</article>

<aside>
  <form action="admin_regler.php" method="post">
    Ny regel: <input type="text" name="input_regel"><br>
    <input type="submit" value="Legg til ny regel" name="submit_regel">

    <h2>Slett/endre regel</h2>
    <select name="slett_regel">
        <?php
        $dataFraFil = $pdo->prepare("SELECT regel FROM regler WHERE regel != ''");
        $dataFraFil->execute();

        for($i = 0; $rader = $dataFraFil->fetch(); $i++) {
            ?>
            <option value="<?php echo $rader['regel']?>"><?php echo $rader['regel']?></option>
            <?php
        }
        ?>
    </select>
		<input type="text" name="endre_regel"><br>
    <input id="slett" type="submit" value="Slett en interesse" name="submit_slett_regel">
		<input id="endre" type="submit" value="Endre regel" name="submit_regelEndre">
  </form>
</aside>
</body>
<!-- Denne siden er laget av Fredrik Hulaas og Ola Bredviken siste gang endret 09.04.2018. -->
<!-- Denne siden er kontrollert av Håvard Bredviken, siste gang 09.04.2018. -->
</html>
