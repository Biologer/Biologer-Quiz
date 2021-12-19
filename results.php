<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
 <head>
  <title>Biologer kviz</title>
 </head>
 <body>
 <center>
 <?php
 if (isset($_POST['completed'])) {
    $username = $_POST['username'];
    $score = $_POST['score'];
    $no_questions = $_POST['no_questions'];
    $level = $_POST['level'];
 }
?>
 <table style="width:700px"> <tr> <td style="text-align: center; vertical-align: middle;">
 <img src="images/header.png", alt="Header image">
 <h1>Bravo <?php echo($username) ?></h1>
 <p>Odgovorili ste tačno na <?php echo($score) ?> od <?php echo($no_questions) ?> pitanja u 
 <?php 
 if($level == 'easy') {
     echo("najlakšoj");
 }
 if ($level == "medium") {
     echo("srednje teškoj");
 }
 if ($level == "hard") {
    echo("najtežoj");
}
 ?> kategoriji.<p>

<form action="index.php" method="GET">
  <input type="hidden" name="username" value=<?php echo($username); ?>>
  <input type="submit" name="submit" value="Novi kviz?">
</form>

<br><br>
 <img src="images/biologer_logo.png", alt="Biologer logo", width="150", style="margin:10px">
 <img src="images/rsg_logo.jpg", alt="Rufford Fundation logo", width="150", style="margin:10px">
 <img src="images/bddsp.png", alt="BDDSP logo", width="150", style="margin:10px">
 <p style="color:gray;font-size:70%">Ovaj kviz je sacinjen u okviru projekta broj 30495-1 finansiranog od strane The Rufford fondacije, u saradnji sa Biologer bazom podataka. © Miloš Popović, 2021, CC BY-SA.</p>
 <img src="images/footer.png", alt="Header image">
 </td>
 </tr>
 </table>
 </center>
 </body>
 </html>
