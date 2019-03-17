<?php
/* Displays all error messages */
?>
<!DOCTYPE html>
<html>
<head>
  <title>Error</title>
</head>
<body>
<div class="errorForm">
    <p id="errorParagraf">
    <?php



    $sidenBlirOppdatert = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

    if(isset($_SESSION['tilbakemelding']) AND !empty($_SESSION['tilbakemelding']) ) {
        echo $_SESSION['tilbakemelding'];

        if(isset($_SESSION['tidsholder']) and time() - $_SESSION['tidsholder'] > 2 || $sidenBlirOppdatert) {
            unset($_SESSION["tilbakemelding"]);
            unset($_SESSION['tidsholder']);
        }
    }

    if(isset($_SESSION['tilbakemeldingBilde']) AND !empty($_SESSION['tilbakemeldingBilde']) ) {
        echo $_SESSION['tilbakemeldingBilde'];



    }



    ?>
    </p>
</div>
</body>
</html>
