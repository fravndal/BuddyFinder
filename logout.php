<?php
session_start();

// unseter session ved utlogging
unset($_SESSION['login_user']);
unset($_COOKIE['bruker_id']);
unset($_COOKIE['user_id']);
unset($_COOKIE['profil_id']);
setcookie('user_id', null, -1, '/');
setcookie('bruker_id', null, -1, '/');
setcookie('profil_id', null, -1, '/');
$_SESSION['tilbakemelding'] = "Du er logget ut!";
$_SESSION['tidsholder'] = time();
header("location: default.php");

//Denne siden er utviklet av Fredrik Ravndal og Fredrik Hulaas, siste gang endret 09.04.2018 -->
//Denne siden er kontrollert av Fredrik Hulaas, siste gang 09.04.2018 -->
?>
