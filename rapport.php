
<?php
include("include/config.php");
include("include/session.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST')  {
    if(isset($_POST['submit-rapport'])) {
        $rapporterBruker = $pdo->prepare("Insert into rapport (brukernavn, begrunnelse, dato) VALUES (:brukernavn, :begrunnelse, NOW())");
        $rapporterBruker->bindParam(':brukernavn', $_POST['rapport_bruker']);
        $rapporterBruker->bindParam(':begrunnelse', $_POST['rapport_begrunnelse']);
        $rapporterBruker->execute();
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
	<link rel="stylesheet" type="text/css" href="css/endreprofil/endreprofilDesktop.css" media="screen and (min-width: 1281px)">
	<script src="js/w3.js"></script>
</head>

<body>
<header>
    <?php
    include("include/logo.html");
    ?>
</header>

<nav>
    <?php
    include("include/menu.html");
    ?>
</nav>

<aside id="rapportForm">
    <form action="rapport.php" method="post">
        <h1 id="headerRapport">Rapporter en bruker</h1>
        <p>Skriv inn brukernavn på den du vil rapportere og hvorfor</p>
        <input class="rapport_form" type="rapport" name="rapport_bruker" placeholder="" required="" tabindex="2" value="<?php echo $_COOKIE['profil_id']?>" > <br>
        <input class="rapport_form" type="rapport" name="rapport_begrunnelse" placeholder="Skriv begrunnelse..." required="" tabindex="2">
        <input class="rapporteringsKnapp" type="submit" value="Send" name="submit-rapport">
    </form>
</aside>


<footer id="footer">
    <?php
    include("include/footer.html");
    ?>
</footer>

</body>
<!-- Denne siden er utviklet av Fredrik Ravndal, Håvard Betten, Ola Bredviken og Fredrik Hulaas, siste gang endret 26.01.2018 -->
<!--Fredrik Ravndal har gjort php, Håvard Betten har gjort sql, Fredrik Hulaas har gjort php og Ola Bredviken har gjort CSS og HTML -->
<!-- Denne siden er kontrollert av Fredrik Ravndal, siste gang 26.01.2018 -->
</html>
