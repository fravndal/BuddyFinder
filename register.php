<?php
include_once("include/config.php");
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // sjekker om posten register er satt
    if (isset($_POST['register'])) { //user registering
        require 'registerDatabase.php';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrering</title>
  	<meta charset="utf8">
  	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  	<meta name="HandheldFriendly" content="true">

    <!--##Device = Mobiles (Portrait)-->
  	<link rel="stylesheet" type="text/css" href="css/register/registerMobilePortrait.css" media="screen and (min-width: 234px) and (max-width: 480px)" />
  	<!--##Device = Low Resolution Tablets, Mobiles (Landscape)-->
  	<link rel="stylesheet" type="text/css" href="css/register/registerMobileLandscape.css" media="screen and (min-width: 481px) and (max-width: 767px)" />
  	<!--##Device = Tablets, Laptops, Desktops (portrait)-->
    <link rel="stylesheet" type="text/css" href="css/register/registerTabletPortrait.css" media="screen and (min-width: 768px)" />
    <!--##Device = Tablets, Laptops, Desktops (Landscape)-->
</head>

<body>
<div id="kolonneFordeling">
    <header id="container">
        <?php
        include("include/logo.html");
        ?>
    </header>



    <article class="form">
        <?php
        include("include/registerForm.html");
        ?>
    </article>



    <nav id="faq">
        <div id="myDropdown" class="dropdown-content">
            <a href="default.php" style="font-family: Arial;">Startsiden</a>
            <a href="kontaktOss.php" style="font-family: Arial;">Kontakt oss</a>
            <a href="regler.php" style="font-family: Arial;">Regler & FAQ</a>
        </div>
    </nav>

</div>

<footer id="footer">
    <?php
    include("include/footer.html");
    ?>
</footer>

</body>
<!-- Denne siden er utviklet av Håvard Betten og Fredrik Hulaas, siste gang endret 03.11.2017 -->
<!-- Denne siden er kontrollert av Ola Bredviken og Fredrik Ravndal, siste gang 03.11.2017 -->
</html>
