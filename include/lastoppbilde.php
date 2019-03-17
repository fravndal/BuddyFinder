<!-- Denne siden er laget av Fredrik Ravndal, med deler tatt fra
https://www.w3schools.com/php/php_file_upload.asp , sist endret 22.01.2018-->
<!-- Denne siden er kontrollert av Håvard Betten, siste gang 26.01.2018 -->

<?php
include("../include/config.php");

$brukernavn = $_COOKIE['user_id'];
$target_dir = "../uploads/";
$target_dir_database = "uploads/";


$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;

$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$random_name = rand(1000, 100000);
$target_full_name_folder = $target_dir.$random_name.".".$imageFileType;
$target_full_name_database = $target_dir_database.$random_name.".".$imageFileType;

/*$target_name = rand(1000, 100000).".".$imageFileType;*/
// Sjekker om filen er et bilde eller ikke.
if(isset($_POST["submit"])) {

    // Sjekker om filen finnes fra før
    if (file_exists($target_file)) {
        $_SESSION['tilbakemeldingBilde'] = "Beklager, bilde finnes fra før.";
        $_SESSION['tidsholder'] = time();
        $uploadOk = 0;
    }

    // Sjekker størrelesen av filen
    if ($_FILES["fileToUpload"]["size"] == 0) {
        $_SESSION['tilbakemeldingBilde'] = "Beklager, filen er for stor.";
        $_SESSION['tidsholder'] = time();
        $uploadOk = 0;

    }
    // Tillat filtyper
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
        $_SESSION['tilbakemeldingBilde'] = "Beklager, bare JPG, JPEG, PNG & GIF filer er tillat.";
        $_SESSION['tidsholder'] = time();
        $uploadOk = 0;
    }

    // Sjekker om $uploadOk er satt til 1 for å legge inn bildet.
    $bruker_sitt_bilde = $pdo->prepare("SELECT id_bruker
                                                FROM bruker_bilde
                                                WHERE id_bruker = :id_bruker");
    $bruker_sitt_bilde->bindParam(':id_bruker', $_COOKIE['bruker_id']);
    $bruker_sitt_bilde->execute();
    $sjekk = $bruker_sitt_bilde->fetch();


    if ($uploadOk == 1 and empty($sjekk)) {
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_full_name_folder);
        $imgInsert = $pdo->prepare("INSERT INTO bruker_bilde (id_bruker, bilde) VALUES(:id_bruker, :bilde)");
        $imgInsert->bindParam(':id_bruker', $_COOKIE['bruker_id']);
        $imgInsert->bindParam(':bilde', $target_full_name_database);

        $imgInsert->execute();
        $_SESSION['tilbakemeldingBilde'] = "Filen ". basename( $_FILES["fileToUpload"]["name"]). " er lastet opp.";
        $_SESSION['tidsholder'] = time();
    } else{

        if ($uploadOk == 1 and !empty($sjekk)) {
            move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_full_name_folder);
            $imgInsert = $pdo->prepare("UPDATE bruker_bilde SET bilde = :bilde WHERE :id_bruker = id_bruker");
            $imgInsert->bindParam(':id_bruker', $_COOKIE['bruker_id']);
            $imgInsert->bindParam(':bilde', $target_full_name_database);
            $imgInsert->execute();
            $_SESSION['tilbakemeldingBilde'] = "Filen ". basename( $_FILES["fileToUpload"]["name"]). " er lastet opp.";
            $_SESSION['tidsholder'] = time();
        }
    }
    header("location: ../endreprofil.php");
}




?>
