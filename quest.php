 <html>
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <head>
  <title>Biologer kviz</title>
 </head>
 <body>
 <center>

 <?php
 # Get the user name and other user options
  session_start();
  if (isset($_GET['username'])) {
    $username = $_GET['username'];
  }
  if (isset($_GET['level'])) {
      $level = $_GET['level'];
  }
  if (isset($_GET['no_questions'])) {
    $no_questions = $_GET['no_questions'];
  }


  # Function that return random numbers from the vector of numbers
  function randomSpecies($table, $level) {
    $quantity = 4;
    $min = 1;
    $max = sizeof($table) - 1;
    $numbers = range($min, $max);
    shuffle($numbers);

    # For easy level, just get 4 random species
    if ($level == "easy") {
      $random4species = array_slice($numbers, 0, $quantity);
      $species = [];
      for ($x = 0; $x <= 3; $x++) {
        $species[$x] = $table[$random4species[$x]];
      }
      return $species;
    }

    # For medium level get 4 species, but from the same group
    if ($level == "medium") {
      $random1species = array_slice($numbers, 0, 1);
      
      # Subset to one group only!
      $group = $table[$random1species[0]]['group'];
      #echo("The group is " . $group . ".<br>");
      $temp_table = array_filter($table, function ($var) use ($group) {
        #echo($var['group'] . "<br>");
        return $var['group'] == $group;
      });
      $temp_table = array_values($temp_table);
      
      $temp_max = sizeof($temp_table) - 1;
      $temp_numbers = range($min, $temp_max);
      shuffle($temp_numbers);
      $random4species = array_slice($temp_numbers, 0, $quantity);
      $species = [];
      $fillin_table = $table;
      for ($x = 0; $x <= 3; $x++) {
        $species[$x] = $temp_table[$random4species[$x]];
        $fillin_table = array_filter($fillin_table, function ($var) use ($species) {
          return $var['id'] != $species[$x]['id'];
        });
        $fillin_table = array_values($fillin_table);
      }
      # In some groups there are no 4 species, so we need to agg a species out of the group
      $temp_table = array_filter($table, function ($var) use ($group) {
        return $var['group'] == $group;
      });
      while ($temp_max <= 3) {
        $temp1_max = sizeof($fillin_table) - 1;
        $temp_numbers = range($min, $temp1_max);
        shuffle($temp_numbers);
        $species[$temp_max] = $fillin_table[array_slice($temp_numbers, 0, 1)[0]];
        $temp_max++;
      }
      return $species;
    }
    
    # For hard level get 4 species, from the same group
    if ($level == "hard") {
      $random1species = array_slice($numbers, 0, 1);
      
      # Subset to this grup.
      $group = $table[$random1species[0]]['group_hard'];
      $temp_table = array_filter($table, function ($var) use ($group) {
        return $var['group_hard'] == $group;
      });
      $temp_table = array_values($temp_table);
      
      $temp_max = sizeof($temp_table) - 1;
      $temp_numbers = range($min, $temp_max);
      shuffle($temp_numbers);
      $random4species = array_slice($temp_numbers, 0, $quantity);
      $species = [];
      $fillin_table = $table;
      for ($x = 0; $x <= 3; $x++) {
        $species[$x] = $temp_table[$random4species[$x]];
        $fillin_table = array_filter($fillin_table, function ($var) use ($species) {
          return $var['id'] != $species[$x]['id'];
        });
        $fillin_table = array_values($fillin_table);
      }
      # In some groups there are no 4 species, so we need to agg a species out of the group
      $temp_table = array_filter($table, function ($var) use ($group) {
        return $var['group_hard'] == $group;
      });
      while ($temp_max <= 3) {
        $temp1_max = sizeof($fillin_table) - 1;
        $temp_numbers = range($min, $temp1_max);
        shuffle($temp_numbers);
        $species[$temp_max] = $fillin_table[array_slice($temp_numbers, 0, 1)[0]];
        $temp_max++;
      }
      return $species;
    }

  }
   
  # Function gor getting the image from Biologer server
  function imageForTaxon($taxon_id, $level) {
    $url = "https://biologer.org/api/taxa/" . $taxon_id . "/public-photos?excludeDead=true";
    $content = file_get_contents($url);
    if($content == false) {
      echo("<br>Biologer server is not accessable for some reason!<br>");
      return false;
    } else {
      $json_ca = json_decode($content, true);   
      $data = $json_ca['data'];
      
      # If the level is not set to hard we will use only images of adult butterflies
      if ($level != "hard") {
        foreach($data as $key => $value) {
          if ($value['stage'] != NULL) {
            if ($value['stage']['name'] != "adult") {
              unset($data[$key]);
            }
          }
        }
        $data = array_values($data);
      }
      
      $no_images = count($data) - 1;
      #echo "There are " . $no_images . " images for taxon " . $taxon_id . ".<br>";
      $random_image = rand(0, $no_images);
      #echo("Random image No. " . $random_image);
      return($data[$random_image]);
    }
  }

  ######################################
  # If running for the first time
  ######################################
  if (!isset($_POST['answered'])) {
    function csv2array($filename) {
      $species = fopen($filename, "r");
      $tablica = []; # Define the array to save CSV
      while (($data = fgetcsv($species)) !== FALSE) {
          $tablica[$i]['id'] = $data[0];
          $tablica[$i]['level'] = $data[1]; 
          $tablica[$i]['lat_name'] = $data[2];
          $tablica[$i]['group_hard'] = $data[3]; 
          $tablica[$i]['group'] = $data[4]; 
          $tablica[$i]['link'] = $data[5];
          $i++;
       }
      fclose($species);  
      return($tablica);
    }
    
    # This are all species
    $species_table = csv2array("species.csv");
    # For easy level subset species to easily recognisable ones
    if($level == 'easy') {
      $species_table = array_filter($species_table, function ($var) {
        return ($var['level'] == 'easy');
      });
      # Reindex array, so that ids go from 0 to n by incriment of 1...
      $species_table = array_values($species_table);
    }
    # And subset for medium level
    if($level == 'medium') {
      $species_table = array_filter($species_table, function ($var) {
        return ($var['level'] == 'easy' || $var['level'] == 'medium');
      });
      $species_table = array_values($species_table);
    }
    $_SESSION['species_table'] = $species_table;
    $score = 0;
    $question = 1;

    # Get the variables
    $random4species = randomSpecies($species_table, $level);
    $selected_species = rand(0, 3);
    $image_species = imageForTaxon($random4species[$selected_species]['id'], $level);

    # Save the variables to be used if page is refreshed
    $_SESSION['random4species']=$random4species;
    $_SESSION['selected_species']=$selected_species;
    $_SESSION['image_species']=$image_species;
    $_SESSION['no_questions']=$no_questions;
    $_SESSION['question'] = $question;

    ?>
    <table style="width:700px"> <tr> <td style="text-align: center; vertical-align: middle;">
    <img src="images/header.png", alt="Header image">
    <h2>Hajde da vidimo šta znate!</h2>
    <?php
  }
 
  ######################################
  # Upon answering the question
  # (i.e. if there is a POST response)
  ######################################
  if (isset($_POST['answered'])) {
    $species_table = $_SESSION['species_table'];
    $level = $_POST['level'];
    $score = $_POST['score'];
    $username = $_POST['username'];
    $question = $_POST['question'] + 1;
    $no_questions = $_POST['no_questions'];

    # Variant 1
    # Execute this on page refresh
    if ($_POST['randcheck'] != $_SESSION['rand']) {
      $random4species = $_SESSION['random4species'];
      $selected_species = $_SESSION['selected_species'];
      $image_species = $_SESSION['image_species'];
      $score = $_SESSION['score'];
      $question = $_SESSION['question'];
      $no_questions = $_SESSION['no_questions'];
    }

    # Variant 2
    # Execute this if user subbmited and answer
    else {?>
      <table style="width:700px"> <tr> <td style="text-align: center; vertical-align: middle;">
      <img src="images/small_header.png", alt="Header image">
      <?php
      $answer = $_POST['answer'];
      $correct = $_POST['correct_answer'];
      if ($answer == $correct) {
        $score = $score + 1;
        $_SESSION['score'] = $score;?>
        <h2 style="color:green">Sjajno <?php echo($username)?>! Ovo je tačan odgovor.</h2>
      <?php } else {?>
        <h2 style="color:red">Pogrešan odgovor. To je bila vrsta <a href="
        <?php echo($_SESSION['random4species'][$correct]['link']);?>" target="_blank">
        <?php echo($_SESSION['random4species'][$correct]['lat_name']);
        ?></a></h2>
      <?php }

    # Get the variables
    $random4species = randomSpecies($species_table, $level);
    $selected_species = rand(0, 3);
    $image_species = imageForTaxon($random4species[$selected_species]['id'], $level);

    # Save the variables to be used if page is refreshed
    $_SESSION['random4species']=$random4species;
    $_SESSION['selected_species']=$selected_species;
    $_SESSION['image_species']=$image_species;
    $_SESSION['no_questions']=$no_questions;
    $_SESSION['question'] = $question;
  }
}

#Saving random number to system and posting its value.
#This is used to get info if page is refreshed by the user
#or the user clicked on the „Next“ button. -->
$rand=rand();
$_SESSION['rand']=$rand;

?>

<?php
# The code after last question is answered
if ($question  > $no_questions) {
  ?>
  <p>Uspešno ste rešili ovaj kviz!</p>
  <form action="results.php" method="POST">
    <input type="hidden" name="username" value=<?php echo($username); ?>>
    <input type="hidden" name="score" value=<?php echo($score); ?>>
    <input type="hidden" name="no_questions" value=<?php echo($no_questions); ?>>
    <input type="hidden" name="level" value=<?php echo($level); ?>>
    <input type="submit" name="completed" value="Pogledaj rezultat">
  </form>
  <form action="index.php" method="GET">
    <input type="hidden" name="username" value=<?php echo($username); ?>>
    <input type="submit" name="submit" value="Novi kviz">
  </form>

<?php }
# If there are more questions run this...
if ($question  <= $no_questions) {
?>
   <img src="<?php echo $image_species['url']; ?>" width="500" heigth="300"></img>
   <p style="color:gray;size:80%">Autor fotografije: <?php echo($image_species['author']); ?></p>
<br>
  <form action="quest.php" method="POST">
  <center>
   <table>
    <tr>
     <td>
      <input type="radio" id="answer1" name="answer" value="0">
      <label for="answer1"><?php echo($random4species[0]['lat_name']);?></label></td>
     <td>
      <input type="radio" id="answer2" name="answer" value="1">
      <label for="answer2"><?php echo($random4species[1]['lat_name']);?></label></td>
    </tr>
    <tr>
     <td>
      <input type="radio" id="answer3" name="answer" value="2">
      <label for="answer3"><?php echo($random4species[2]['lat_name']);?></label></td>
     <td>
      <input type="radio" id="answer4" name="answer" value="3">
      <label for="answer4"><?php echo($random4species[3]['lat_name']);?></label></td>
    </tr>
   </table>
  </center>
 <br>

 <input type="hidden" value="<?php echo $rand; ?>" name="randcheck" />
 <input type="hidden" name="correct_answer" value=<?php echo($selected_species); ?>>
 <input type="hidden" name="correct_name" value=<?php echo($random4species[$selected_species]['lat_name']); ?>>
 <input type="hidden" name="species_table" value=<?php echo($species_table); ?>>
 <input type="hidden" name="username" value=<?php echo($username); ?>>
 <input type="hidden" name="level" value=<?php echo($level); ?>>
 <input type="hidden" name="question" value=<?php echo($question); ?>>
 <input type="hidden" name="no_questions" value=<?php echo($no_questions); ?>>
 <input type="hidden" name="score" value=<?php echo($score); ?>>
 <input type="submit" name="answered" value="Naredno pitanje">

 </form>
 <p style="color:gray;font-size:90%">Pitanje br. <?php echo($question)?> od <?php echo($no_questions)?>, ukupno tačnih odgovora: <?php echo($score);?>.</p>
 <p style="color:gray;font-size:90%"><a href="index.php">Nazad na početnu stranicu…</а></p>
<?php 
}
?>
 
 <img src="images/footer.png", alt="Header image">
 </td> </tr> </table> </center>
 <br clear="all"><br>
 
 </body>
</html>
