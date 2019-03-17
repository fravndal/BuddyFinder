<?php
include("include/config.php");
$queryStatement = $pdo->prepare("SELECT * FROM chat WHERE brukernavn_fra = :brukernavn OR brukernavn_til = :brukernavn");
$queryStatement->bindParam(':brukernavn', $_COOKIE['user_id']);
$queryStatement->execute();

$results = $queryStatement->fetchAll(PDO::FETCH_ASSOC);

// die(var_dump($result));
?>
<!DOCTYPE html>
<html>
  <head>
    <title>BuddyFinderChat</title>
    <!--##Device = Mobiles (Portrait)-->
    <link rel="stylesheet" type="text/css" href="css/chat/chat_indexMobile.css" media="screen and (min-width: 234px) and (max-width: 800px)">
    <!--##Device = Laptops, Desktops (Landscape)-->
	  <link rel="stylesheet" type="text/css" href="css/chat/chat_indexDesktop.css" media="screen and (min-width: 801px)">
  </head>
    <body>
      <!-- Header for logo -->
      <header class="header">
          <div class="section">
              <?php
              include("include/logo.html");
              include("include/menu.html");
              ?>
          </div>
      </header>
      <mark>
          <?php
          include("error.php");
          ?>
      </mark>

      <div id="container">
        <div id="content">
        <p id="paragraf">Dine eksisterende samtaler: </p>
        <?php if (!empty($results)) {
          foreach ($results as $result) {
          ?><a href="chat.php?chatId=<?php echo $result["chat_id"] ?>" id="nav3"><?php echo $result["brukernavn_fra"] ?>-><?php echo $result["brukernavn_til"] ?></a><?php
          }
        } else {
            ?> <a href="chat_create.php" id="nav1">Ønsker du å starte en samtale?</a> <?php
        } ?>
        <p id="paragraf">Trykk lenken under for å starte en ny samtale! </p><a href="chat_create.php" id="nav2">Start ny samtale</a>
  </div>
</div>
    <footer id="footer">
        <?php
        include("include/footer.html");
        ?>
    </footer>
    </body>
    <!-- Denne siden er utviklet av Håvard Betten og Fredrik Hulaas, siste gang endret 01.06.2018 -->
    <!-- Denne siden er kontrollert av Ola Bredviken og Fredrik Ravndal, siste gang 01.06.2018 -->
</html>
