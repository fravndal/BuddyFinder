<?php
include("include/config.php");
include("include/session.php");
if($_SERVER["REQUEST_METHOD"] == "POST") {

  $queryStatement = $pdo->prepare("SELECT * FROM chat WHERE brukernavn_fra = :brukernavn AND brukernavn_til = :tilBruker");
  $queryStatement->bindParam(':brukernavn', $_COOKIE['user_id']);
  $queryStatement->bindParam(':brukernavn',$_POST['brukernavn_id']);
  $queryStatement->execute();

  $result = $queryStatement->fetch(PDO::FETCH_ASSOC);

  // User already has a chat with this person
  if (!empty($result)) {
    header('Location: chat.php?chatId=' . $result['chat_id']);
  } else {
      $insertStatement = $pdo->prepare("INSERT INTO chat (brukernavn_fra, brukernavn_til) VALUES(:brukernavn_fra, :brukernavn_til)");
      $insertStatement->bindParam(':brukernavn_fra', $_COOKIE['user_id']);
      $insertStatement->bindParam(':brukernavn_til', $_POST['brukernavn_id']);
      $insertStatement->execute();

      header('Location: chat.php?chatId=' . $pdo->lastInsertId());
  }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>BuddyFinderChat</title>
    <link rel="stylesheet" type="text/css" href="css/chat/chat_createMobile.css" media="screen and (min-width: 234px) and (max-width: 800px)">
    <!--##Device = Laptops, Desktops (Landscape)-->
	  <link rel="stylesheet" type="text/css" href="css/chat/chat_createDesktop.css" media="screen and (min-width: 801px)">
    <style type="text/css">
    </style>
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
      <form action="chat_create.php" method="post">
      <?php
      $result = $pdo->prepare("SELECT brukernavn FROM bruker WHERE brukernavn NOT LIKE :currentUser");
      $result->bindParam(':currentUser', $_COOKIE['user_id']);
      $result->execute();
      $resultatAlleBrukere = $result->fetchAll(PDO::FETCH_ASSOC);
      // die(var_dump($resultatAlleBrukere));
      ?>
      <div id="content">
      <p>Velg brukeren du vil chatte med!</p>
      <select class="rullGardin" name="brukernavn_id" style="width: 100px;">
          <?php foreach($resultatAlleBrukere as $bruker) { ?>
              <option value='<?php echo $bruker["brukernavn"] ?>'><?php echo $bruker['brukernavn']; ?></option>
              <?php
          }
          ?> </select>
        <input type="submit" value="Velg bruker" class="submit" name="submit_chat">
      </form>
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
