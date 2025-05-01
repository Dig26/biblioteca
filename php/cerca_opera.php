<?php
include 'config.php';

$searchQuery = "";
$results = [];
if (isset($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM opera WHERE titolo LIKE '%$searchQuery%' OR autore LIKE '%$searchQuery%'";
    $queryResult = $conn->query($sql);
    if ($queryResult->num_rows > 0) {
        while ($row = $queryResult->fetch_assoc()) {
            $results[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cerca Opera</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Cerca Opera</h1>
    </header>
    <main>
      <form method="get" action="cerca_opera.php">
        <label for="search">Cerca per titolo o autore:</label>
        <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($searchQuery); ?>" required>
        <button type="submit">Cerca</button>
      </form>
      <?php if (isset($_GET['search'])): ?>
        <h2>Risultati della ricerca:</h2>
        <?php if (count($results) > 0): ?>
          <table>
            <tr>
              <th>ID</th>
              <th>Titolo</th>
              <th>Autore</th>
              <th>Anno Prima Pub.</th>
            </tr>
            <?php foreach ($results as $opera): ?>
              <tr>
                <td><?php echo $opera["id_opera"]; ?></td>
                <td><?php echo $opera["titolo"]; ?></td>
                <td><?php echo $opera["autore"]; ?></td>
                <td><?php echo $opera["anno_prima_pub"]; ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php else: ?>
          <p>Nessun risultato trovato.</p>
        <?php endif; ?>
      <?php endif; ?>
      <a class="back-link" href="../index.php">Torna alla Homepage</a>
    </main>
  </div>
</body>
</html>
