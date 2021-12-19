<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
 <head>
  <title>Biologer kviz</title>
 </head>
 <body>

 <?php
 if (isset($_GET['username'])) {
   $username = $_GET['username'];
   }
   else {
      $username = "";
   }
  ?>
 <center> <table style="width:700px"> <tr> <td style="text-align: center; vertical-align: middle;">
 <img src="images/header.png", alt="Header image">
 <h1>Koliko poznajete leptire?</h1>
 <form action="quest.php" method="GET">
 
    <input type="text" id="username" name="username" placeholder="Unesite vaše ime" autofocus value="<?php echo($username); ?>"><br><br>

    <label><input type="radio" id="level_easy" name="level" value="easy">Lako</label>
    <label><input type="radio" id="level_medium" name="level" value="medium" checked>Srednje</label>
    <label><input type="radio" id="level_hard" name="level" value="hard">Teško</label>
    <br>

    <label><input type="radio" id="q10" name="no_questions" value=10>10 pitanja</label>
    <label><input type="radio" id="q25" name="no_questions" value=25 checked>25 pitanja</label>
    <label><input type="radio" id="q50" name="no_questions" value=50>50 pitanja</label>
    <br><br>

    <input type="submit" value="Započni kviz!">
 </form>

 <br><br>
 <img src="images/biologer_logo.png", alt="Biologer logo", width="150", style="margin:10px">
 <img src="images/rsg_logo.jpg", alt="Rufford Fundation logo", width="150", style="margin:10px">
 <img src="images/bddsp.png", alt="BDDSP logo", width="150", style="margin:10px">
 <p style="color:gray;font-size:70%">Ovaj kviz je sacinjen u okviru projekta broj 30495-1 finansiranog od strane The Rufford fondacije, u saradnji sa Biologer bazom podataka. © Miloš Popović, 2021, CC BY-SA.</p>
 <img src="images/footer.png", alt="Header image">
 </td> </tr> </table> </center>

 </body>
 </html>
