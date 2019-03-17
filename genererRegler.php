<?php

$dataFraFil = $pdo->prepare("SELECT regel FROM regler WHERE regel != ''");
$dataFraFil->execute();
$data = $dataFraFil->fetchALL();

if (!file_exists('regler')) {
    mkdir('regler', 0777, true);
}

/*die(var_dump($data));*/
$skriveFil = fopen("regler/regler.html", "w") or die("Kan ikke åpne filen!");
$skriv = "<!DOCTYPE HTML>" .
    "<html>" .
    "<head>" .
    "<title>Regler</title>" .
    "<meta charset=\"utf8\">".
    "<meta name=\"viewport\" content=\"width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no\">".
    "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\">".
    "<meta name=\"HandheldFriendly\" content=\"true\">".
    "<link rel='stylesheet' type='text/css' href='../css/regler/reglerMobilePortrait.css' media='screen and (min-width: 234px) and (max-width: 480px)' />".
    "<link rel='stylesheet' type='text/css' href='../css/regler/reglerMobileLandscape.css' media='screen and (min-width: 481px) and (max-width: 767px)' />".
    "<link rel='stylesheet' type='text/css' href='../css/regler/reglerTabletPortrait.css' media='screen and (min-width: 768px) and (max-width: 900px)' />".
    "<link rel='stylesheet' type='text/css' href='../css/regler/reglerTabletLandscape.css' media='screen and (min-width: 901px) and (max-width: 1280px)' />".
    "<link rel='stylesheet' type='text/css' href='../css/regler/reglerDesktop.css' media='screen and (min-width: 1281px)'>".
    "</head>".
    "<body>".
    "<div id=\"parent\">".
    "<header class=\"logosection\">".
    "<img id=\"logo\" src=\"../picture/default/logo-desktop.png\" alt=\"desktop logo image\" />".
    "</header>".
    "<nav id=\"faq\">".
    "<button onclick=\"dropdownFunction()\" class=\"dropbtn\">Meny</button>".
    "<div id=\"myDropdown\" class=\"dropdown-content\">".
    "<ul>".
    "<li><a href=\"#\" id=\"tilbake\" style=\"font-family: Arial;\">Tilbake</a></li>".
    "<li><a href=\"../minside.php\" id=\"homepage\" style=\"font-family: Arial;\">Min Side</a></li>".
    "<li><a class=\"linkAdministrerProfil\" href=\"../endreprofil.php\">Administrer min profil</a></li>".
    "<li><a href=\"../arrangement.php\">Arrangementer</a></li>".
    "<li><a href=\"../opprettChat.php\">Chat</a></li>".
    "<li><a href=\"../kontaktOss.php\">Kontakt oss</a></li>".
    "<li><a href=\"regler.php\">Regler & FAQ</a></li>".
    "<li id=\"loggUtMeny\"><a href=\"../logout.php\">Logg ut</a></li>".
    "</ul>".
    "</div>".
    "</nav>".
    "</div>".
    "<main id=\"ytre\">".
    "<article id=\"regler\">".
    "<h1 id=\"overskrift\">Regler for bruk av siden:</h1>";
fwrite($skriveFil, $skriv);
$paragrafStart = "<li>";
$paragrafSlutt = "</li>";
foreach($data as $regler) {
    $regel = $paragrafStart . $regler['regel'] . $paragrafSlutt . "<br>";
    fwrite($skriveFil, $regel);
}
$skrivIgjen = "</article>".
    "</main>".
    "<footer>".
    "<h1 id=\"footerheader\">BuddyFinder © 2018</h1>".
    "<p id=\"utvikletAv\">Denne siden er utviklet av Fredrik Ravndal, Fredrik Hulaas, Håvard Betten og Ola Bredviken.</p>".
    "</footer>".
    "<script>
    function dropdownFunction() {
        document.getElementById(\"myDropdown\").classList.toggle(\"show\");
    }
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {

            var dropdowns = document.getElementsByClassName(\"dropdown-content\");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
    </script>".
    "</body>" .
    "</html>";
fwrite($skriveFil, $skrivIgjen);
fclose($skriveFil);
?>
