<?php

// legger inn saltet så det ikke skal være mulig å se det i kildekoden da config blir included.
$salt = 'IT2_2018';

define('DB_SERVER', '158.36.139.21');
define('DB_USERNAME', 'venn_user_2');
define('DB_PASSWORD', 'Uservenn@2');
define('DB_DATABASE', 'venn_2');

// bruker PDO da denne har bredere støtte for flere type databaser
try {
    $pdo = new PDO(
        "mysql:charset=utf8;dbname=" . DB_DATABASE . ";host=" . DB_SERVER,
        DB_USERNAME,
        DB_PASSWORD
    );
} catch (PDOException $e) {
    $_SESSION['tilbakemelding'] = "Får ikke kontakt med databasen!...";
    $_SESSION['tidsholder'] = time();
    header("location: default.php");
}
//Denne siden er utviklet av Fredrik Ravndal, siste gang endret 26.01.2018
//Denne siden er kontrollert av Fredrik Ravndal, siste gang 26.01.2018
?>
