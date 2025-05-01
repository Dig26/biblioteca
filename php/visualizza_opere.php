<?php
include 'config.php';

$sql = "SELECT * FROM opera";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Visualizza Opere</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Visualizza Opere</h1>
    </header>
    <main>
      <?php
      if ($result->num_rows > 0) {
          echo "<table>";
          echo "<tr>
                  <th>ID</th>
                  <th>Titolo</th>
                  <th>Autore</th>
                  <th>Anno Prima Pub.</th>
                </tr>";
          while($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>" . $row["id_opera"] . "</td>
                      <td>" . $row["titolo"] . "</td>
                      <td>" . $row["autore"] . "</td>
                      <td>" . $row["anno_prima_pub"] . "</td>
                    </tr>";
          }
          echo "</table>";
      } else {
          echo "<p>Nessuna opera trovata.</p>";
      }
      ?>
      <a class="back-link" href="../index.php">Torna alla Homepage</a>
    </main>
  </div>
</body>
</html>
