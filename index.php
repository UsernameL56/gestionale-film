<?php
require "db.php";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // echo "Connected successfully";
  
  $statement = $conn->prepare("SELECT * FROM filmDB_Films");
  $statement->execute();

 $data = $statement->fetchAll();
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>

    <div class="container">
        <h1 class="text-center">FILM</h1>
    <?php
        foreach ($data as $row) {
          //visualizza risultati (array usato con indice o come array associativo) 
          echo "<h3>Titolo: ".$row["Titolo"]."<br>Data di uscita: ".$row["Data_Uscita"]. "<br>Descrizione: ". $row["Descrizione"]."</h3><hr>";
        }
      } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
      }
    ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>