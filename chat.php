<?php
include("include/session.php");
include("include/config.php");

$currentlyLoggedInn = $_COOKIE['user_id'];
$chat_id = (int)$_GET['chatId'];

$queryStatement = $pdo->prepare("SELECT * FROM chat_meldinger WHERE chat_id = :chat_id ORDER BY tidspunkt_sendt ASC");
$queryStatement->bindParam(':chat_id', $chat_id);
$queryStatement->execute();

$chatMessages = $queryStatement->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER["REQUEST_METHOD"] == "POST") {
  $insertStatement = $pdo->prepare("INSERT INTO chat_meldinger (meldings_id, chat_id, melding, brukernavn, tidspunkt_sendt) VALUES(NULL, :chat_id , :melding, :brukernavn, NOW())");
  $insertStatement->bindParam('chat_id', $chat_id);
  $insertStatement->bindParam(':melding', $_POST['message']);
  $insertStatement->bindParam(':brukernavn', $currentlyLoggedInn);
  $insertStatement->execute();

  $queryStatement = $pdo->prepare("SELECT * FROM chat_meldinger WHERE chat_id = :chat_id ORDER BY tidspunkt_sendt ASC");
  $queryStatement->bindParam(':chat_id', $chat_id);
  $queryStatement->execute();

  $chatMessages = $queryStatement->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title>BuddyFinderChat</title>
    <link rel="stylesheet" type="text/css" href="css/chat/chatMobile.css" media="screen and (min-width: 234px) and (max-width: 800px)">
    <!--##Device = Laptops, Desktops (Landscape)-->
	  <link rel="stylesheet" type="text/css" href="css/chat/chatDesktop.css" media="screen and (min-width: 801px)">
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
      <h1 id="h1">BuddyFinder Chat!</h1>
      <div id="chatContainer">
        <?php foreach ($chatMessages as $message) {
          if ($currentlyLoggedInn == $message['brukernavn']) {
            ?><div class="right-message">
              <?php echo $message['melding'] ?>
            </div><?php
          } else {
              ?><div class="left-message">
              <?php echo $message['melding'] ?>
              </div><?php
          }
        } ?>
        <form method="post" id="chatform">
          <input type="text" name="message" id="message" style="width: 350px">
          <input type="submit" value="Send melding" class="submit" name="submit_message">
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
