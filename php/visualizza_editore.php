<?php
include 'config.php';

$sql = "SELECT * FROM editore";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visualizza Editori</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Visualizza Editori</h1>
    </header>
    <main>
      <?php
      if ($result->num_rows > 0) {
          echo "<table>";
          echo "<tr><th>ID</th><th>Nome</th></tr>";
          while($row = $result->fetch_assoc()) {
              echo "<tr><td>" . $row["id_editore"] . "</td><td>" . $row["nome"] . "</td></tr>";
          }
          echo "</table>";
      } else {
          echo "<p>Nessun editore trovato.</p>";
      }
      ?>
      <a class="back-link" href="../index.php">Torna alla Homepage</a>
    </main>
  </div>
</body>
</html>
