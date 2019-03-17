<?php
session_start();

// Sjekker om session er satt med brukeren som logger inn
if (!isset($_SESSION['login_user'])){
    header("location: default.php");
}



//Denne siden er utviklet av Fredrik Hulaas sist endret 03.11.2017
//Denne siden er kontrollert av Fredrik Ravndal, siste gang 03.11.2017
