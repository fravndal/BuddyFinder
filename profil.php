<?php
include("include/config.php");
include("include/session.php");

$synligBruker = $pdo->prepare("SELECT profil_synlig FROM bruker WHERE :brukernavn = brukernavn");
$synligBruker->bindParam(':brukernavn', $_COOKIE['profil_id']);
$synligBruker->execute();
$synlig = $synligBruker->fetch();

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
    <!--##Device = Mobiles (Portrait)-->
    <link rel="stylesheet" type="text/css" href="css/profil/profilMobilePortrait.css" media="screen and (min-width: 234px) and (max-width: 480px)" />
    <!--##Device = Low Resolution Tablets, Mobiles (Landscape)-->
    <link rel="stylesheet" type="text/css" href="css/profil/profilMobileLandscape.css" media="screen and (min-width: 481px) and (max-width: 767px)" />
    <!--##Device = Tablets, Laptops, Desktops (portrait)-->
    <link rel="stylesheet" type="text/css" href="css/profil/profilTabletPortrait.css" media="screen and (min-width: 768px) and (max-width: 900px)" />
    <!--##Device = Tablets, Laptops, Desktops (Landscape)-->
    <link rel="stylesheet" type="text/css" href="css/profil/profilTabletLandscape.css" media="screen and (min-width: 901px) and (max-width: 1280px)" />
    <!--##Device = Laptops, Desktops (Landscape)-->
  <link rel="stylesheet" type="text/css" href="css/profil/profilDesktop.css" media="screen and (min-width: 1281px)">
</head>

<body>

  <div id="parent">
      <header id="child1">
          <?php
          include("include/logo.html");
          ?>
      </header>

      <nav id="child2">
          <?php
          include("include/menu.html");
          ?>
      </nav>
  </div>

<main id="ytre">
  <article id="kolonne1">
      <?php
      if($synlig[0] == 1) {
      ?>
        <a href="rapport.php" id="rapporterer">Rapporter bruker!</a>
        <aside class="rammeBilde">
        <article class="rammeBrukernavn">
            <h1 class="overskrift1Bruker"> <?php echo $_COOKIE['profil_id'] ?> </h1>
        </article>
    <?php
    $picturepathstatement = $pdo->prepare(
        "SELECT br.id_bruker, brukernavn, bilde
                              FROM bruker AS br LEFT JOIN bruker_bilde AS bb
                              ON br.id_bruker=bb.id_bruker
                              WHERE brukernavn = :brukernavn"
    );
    $picturepathstatement->bindParam(':brukernavn', $_COOKIE['profil_id']);
    $picturepathstatement->execute();
    $emptypicture = "picture/minside/profilepicture.jpg";
    $choose = 0;
    for($i=0; $r = $picturepathstatement->fetch(); $i++){
        if ($r['bilde']) {
            $picturepath = $r['bilde'];
            $choose = 1;
        }
    }
    ?>
    <img class="bildeProfil" src="<?php if($choose == 1){echo $picturepath;}else{echo $emptypicture;} ?>" alt="Profilbilde" />
    </aside>

    <div id="rammeBeskrivelse">
      <div id="rammeBeskrivelseIndre">
        <h1 id="overskrift1Beskrivelse">Beskrivelse</h1>
        <?php
        //Spørring for å få beskrivelse av bruker
        $brukerBeskrivelse = $pdo->prepare("SELECT br.id_bruker, brukernavn, beskrivelse
                                                      FROM bruker AS br LEFT JOIN bruker_beskrivelse AS bb
                                                      ON br.id_bruker=bb.id_bruker
                                                      WHERE brukernavn = :brukernavn");
        $brukerBeskrivelse->bindParam(':brukernavn', $_COOKIE['user_id']);
        $brukerBeskrivelse->execute();

        for($i=0; $row = $brukerBeskrivelse->fetch(); $i++){
            ?>
            <textarea id="textBeskrivelse" rows="4" cols="50" readonly="readonly"><?php echo $row['beskrivelse']; ?></textarea>
        <?php
        }
        ?>
      </div>
    </div>
  </article>
  <article id="kolonne2">
    <div id="rammeInteresser" style="overflow-y:auto;">
      <div id="rammeInteresserIndre">
          <table border="1" cellspacing="2" cellpadding="5" class="tableInteresser">
              <thead class="tableheadInteresser">
              <tr class="tablerow1Interesser">
                  <th class="tableheaderInteresser">Interesser</th>
              </tr>
              </thead>

              <tbody class="tablebodyInteresser">
              <?php
              // Spørring for å få alle interesser på brukeren som er logget inn
              $alleBrukersInteresser = $pdo->prepare('SELECT br.id_bruker, brukernavn, interesse
                                                                FROM bruker AS br LEFT JOIN bruker_interesser AS bi
                                                                ON br.id_bruker=bi.id_bruker
                                                                WHERE brukernavn = :brukernavn');
              $alleBrukersInteresser->bindParam(':brukernavn', $_COOKIE['profil_id']);
              $alleBrukersInteresser->execute();

              for($i=0; $brukerInteresse = $alleBrukersInteresser->fetch(); $i++){
                  ?>
                  <tr class="tablerow2Interesser">
                      <td class="tabledefinitionInteresser"><label><?php echo $brukerInteresse['interesse']; ?></label></td>
                  </tr>
                  <?php
              }
              ?>
              </tbody>
          </table>
      </div>
    </div>

    <div class="rammeStudium" style="overflow-y:auto;">
      <div id="rammeStudiumIndre">
          <table border="1" cellspacing="2" cellpadding="5" class="tableStudium">
              <thead class="tableheadStudium">
              <tr class="tablerow1Studium">
                  <th class="tableheaderStudium">Studium</th>
              </tr>
              </thead>

              <tbody class="tablebodyStudium">
              <?php
              // Spørring for å få studium på brukeren som er logget inn
              $brukersStudium = $pdo->prepare('SELECT br.id_bruker, brukernavn, studium
                                                                FROM bruker AS br LEFT JOIN bruker_studium AS bs
                                                                ON br.id_bruker=bs.id_bruker
                                                                WHERE brukernavn = :brukernavn');
              $brukersStudium->bindParam(':brukernavn', $_COOKIE['profil_id']);
              $brukersStudium->execute();
              for($i=0; $brukerStudium = $brukersStudium->fetch(); $i++){
                  ?>
                  <tr class="tablerow2Studium">
                      <td class="tabledefinitionStudium"><label><?php echo $brukerStudium['studium']; ?></label></td>
                  </tr>
                  <?php
              }
              ?>
              </tbody>
          </table>
      </div>
    </div>
  </article>
</main>
  <?php
  }
  else {
      print "<h1>Profilen er privat</h1>";
      print "<a href=\"rapport.php\">Rapporter bruker</a>";
  }
  ?>





<footer>
    <?php
    include("include/footer.html");
    ?>
</footer>


</body>
<!-- Denne siden er utviklet av Fredrik Ravndal, siste gang endret 09.04.2018 -->
<!-- Denne siden er kontrollert av Putten Bredviken, siste gang 09.04.2018 -->
</html>
