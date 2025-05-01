<?php
include 'config.php';

$sql = "SELECT 
          opera.titolo AS opera_titolo, 
          opera.autore AS opera_autore, 
          opera.anno_prima_pub,
          edizione.anno AS anno_edizione,
          collana.nome AS collana_nome,
          editore.nome AS editore_nome,
          esemplare.Id_esemplare AS numero_inventario
        FROM esemplare
        INNER JOIN volume ON esemplare.id_volume = volume.id_volume
        INNER JOIN edizione ON volume.id_edizione = edizione.id_edizione
        INNER JOIN opera ON edizione.id_opera = opera.id_opera
        INNER JOIN collana ON edizione.id_collana = collana.id_collana
        INNER JOIN editore ON collana.id_editore = editore.id_editore
        ORDER BY opera.titolo, edizione.anno, esemplare.Id_esemplare";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vista Completa degli Esemplari</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Vista Completa degli Esemplari</h1>
    </header>
    <main>
      <?php
      if ($result && $result->num_rows > 0) {
          echo "<table>";
          echo "<tr>
                  <th>Opera</th>
                  <th>Autore</th>
                  <th>Anno Prima Pub.</th>
                  <th>Anno Edizione</th>
                  <th>Collana</th>
                  <th>Editore</th>
                  <th>Numero Inventario</th>
                </tr>";
          while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>" . $row["opera_titolo"] . "</td>
                      <td>" . $row["opera_autore"] . "</td>
                      <td>" . $row["anno_prima_pub"] . "</td>
                      <td>" . $row["anno_edizione"] . "</td>
                      <td>" . $row["collana_nome"] . "</td>
                      <td>" . $row["editore_nome"] . "</td>
                      <td>" . $row["numero_inventario"] . "</td>
                    </tr>";
          }
          echo "</table>";
      } else {
          echo "<p>Nessun esemplare disponibile.</p>";
      }
      ?>
      <a class="back-link" href="../index.php">Torna alla Homepage</a>
    </main>
  </div>
</body>
</html>
