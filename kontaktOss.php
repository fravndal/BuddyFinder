<?php
$isLoggedIn = false;
if(isset($_COOKIE['bruker_id'])) {
    $isLoggedIn = true;
}

?>

<!DOCTYPE html>
<html>

<head>
	<title>Kontakt oss</title>
	<meta charset="utf8">
	<meta name="viewport" content="width=device-width,initial-scale=1,
	maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">
    <?php
    if(!$isLoggedIn){
        print "<link rel='stylesheet' type='text/css' href='css/kontaktOss/ikkeloggetinn/kontaktOssMobilePortrait.css' media='screen and (min-width: 234px) and (max-width: 480px)' />";
        print "<link rel='stylesheet' type='text/css' href='css/kontaktOss/ikkeloggetinn/kontaktOssMobileLandscape.css' media='screen and (min-width: 481px) and (max-width: 767px)' />";
        //##Device = Low Resolution Tablets, Mobiles (Landscape)-->
        print "<link rel='stylesheet' type='text/css' href='css/kontaktOss/ikkeloggetinn/kontaktOssTabletPortrait.css' media='screen and (min-width: 768px) and (max-width: 900px)' />";
        print "<link rel='stylesheet' type='text/css' href='css/kontaktOss/ikkeloggetinn/kontaktOssTabletLandscape.css' media='screen and (min-width: 901px) and (max-width: 1280px)' />";
        //##Device = Tablets, Laptops, Desktops (portrait)-->
        print "<link rel='stylesheet' type='text/css' href='css/kontaktOss/ikkeloggetinn/kontaktOssDesktop.css' media='screen and (min-width: 1281px)'>";
    }
    else {
        print "<link rel='stylesheet' type='text/css' href='css/kontaktOss/loggetinn/kontaktOssMobilePortrait.css' media='screen and (min-width: 234px) and (max-width: 480px)' />";
        print "<link rel='stylesheet' type='text/css' href='css/kontaktOss/loggetinn/kontaktOssMobileLandscape.css' media='screen and (min-width: 481px) and (max-width: 767px)' />";
        //##Device = Low Resolution Tablets, Mobiles (Landscape)-->
        print "<link rel='stylesheet' type='text/css' href='css/kontaktOss/loggetinn/kontaktOssTabletPortrait.css' media='screen and (min-width: 768px) and (max-width: 900px)' />";
        print "<link rel='stylesheet' type='text/css' href='css/kontaktOss/loggetinn/kontaktOssTabletLandscape.css' media='screen and (min-width: 901px) and (max-width: 1280px)' />";
        //##Device = Tablets, Laptops, Desktops (portrait)-->
        print "<link rel='stylesheet' type='text/css' href='css/kontaktOss/loggetinn/kontaktOssDesktop.css' media='screen and (min-width: 1281px)'>";
    }
    ?>
</head>

<body>
<div id="parent">
    <header id="child1">
        <?php
        include("include/logo.html");
        ?>
    </header>

    <div class="header">
        <div class="section">
            <nav id="child2">
                <?php
                include("include/menu.html");
                ?>
            </nav>
            <nav class="menuIkkeLoggetInn">
                <div class="menu">
                    <ul>
                        <li><a href="default.php" style="font-family: Arial;">Hjemmeside</a></li>
                        <li><a href="kontaktOss.php">Kontakt oss</a></li>
                    </ul>
                </div>
            </nav>
            <!-- Loginform -->
            <aside id="loggInnForm">
                <?php
                include("include/loginform.html");
                ?>
            </aside>
        </div>
    </div>
</div>

<main>
    <article id="kontaktinfo">
        <h1>Om BuddyFinder AS</h1>
        <p>Vi i BuddyFinder AS er fire menn med brennende lidenskap for andre mennesker.
            Vi går på Høgskolen i Sør-øst Norge, og studerer IT og Informasjonssystemer.</p>

        <p>Vi møttes på første skoledag, og har siden det
            vært med hverandre og funnet på ting. Vi er nå
            en veldig sammensveiset gruppe, som finner på
            ting selv om det er utenfor skolen.</p>

        <h1>Hvorfor lage BuddyFinder?</h1>
        <p>Budskapet våres via denne applikasjonen
            er å hjelpe nye studenter til å få en
            god opplevelse gjennom sin utdanning.
            Med denne applikasjonen kan man finne
            medstudenter, og starte nye venneforhold.</p><br>

        <h1>Kontakt-informasjon</h1>
        <p>Nedenfor vil du finne forskjellige måter for
            å kunne ta kontakt med oss på.
            Vi svarer som regel innen noen timer.</p>
        <p>E-post -- <a href="mailto:fredrik--hulaas@hotmail.com" target="_top">
                buddyfinder@hotmail.com</a></p>
        <p>Adresse -- Hofsfossveien 1</p>
        <p>Telefon -- 40404040</p>
        <p>Sted -- Hønefoss</p>
        <p>Postnr -- 3517</p>
    </article>
</main>

<footer>
    <?php
    include("include/footer.html");
    ?>
</footer>

</body>
<!-- Denne siden er laget av Ola bredviken, siste gang endret 28.09.2017. -->
<!-- Denne siden er kontrollert av Håvard Betten, siste gang 28.09.2017. -->
</html>
