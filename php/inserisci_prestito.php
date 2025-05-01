<?php
include 'config.php';

$message = "";

// Recuperiamo la lista di utenti
$utenti = [];
$sql_utenti = "SELECT id_utente, nome, cognome FROM utente ORDER BY cognome, nome";
$result_utenti = $conn->query($sql_utenti);
if ($result_utenti && $result_utenti->num_rows > 0) {
    while ($row = $result_utenti->fetch_assoc()) {
        $utenti[] = $row;
    }
}

// Recuperiamo la lista di esemplari
$esemplari = [];
$sql_esemplari = "SELECT Id_esemplare FROM esemplare ORDER BY Id_esemplare";
$result_esemplari = $conn->query($sql_esemplari);
if ($result_esemplari && $result_esemplari->num_rows > 0) {
    while ($row = $result_esemplari->fetch_assoc()) {
        $esemplari[] = $row;
    }
}

// Se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_utente = (int) $_POST['id_utente'];
    $id_esemplare = (int) $_POST['id_esemplare'];
    $data_restituzione = $conn->real_escape_string($_POST['data_restituzione']);

    $sql_insert = "INSERT INTO prestito (id_utente, id_esemplare, data_restituzione) 
                   VALUES ($id_utente, $id_esemplare, '$data_restituzione')";
    if ($conn->query($sql_insert) === TRUE) {
        $message = "Prestito registrato con successo.";
    } else {
        $message = "Errore durante l'inserimento: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registra Prestito</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Registra Prestito</h1>
    </header>
    <main>
      <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
      <?php endif; ?>
      <form method="post" action="inserisci_prestito.php">
        
        <label for="id_utente">Utente:</label>
        <select name="id_utente" id="id_utente" required>
          <option value="">-- Seleziona Utente --</option>
          <?php foreach ($utenti as $utente): ?>
            <option value="<?php echo $utente['id_utente']; ?>">
              <?php echo $utente['cognome'] . ' ' . $utente['nome']; ?>
            </option>
          <?php endforeach; ?>
        </select>
        
        <label for="id_esemplare">Esemplare (Numero Inventario):</label>
        <select name="id_esemplare" id="id_esemplare" required>
          <option value="">-- Seleziona Esemplare --</option>
          <?php foreach ($esemplari as $esemplare): ?>
            <option value="<?php echo $esemplare['Id_esemplare']; ?>">
              <?php echo 'Inventario n. ' . $esemplare['Id_esemplare']; ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label for="data_restituzione">Data Prevista Restituzione:</label>
        <input type="date" name="data_restituzione" id="data_restituzione" required>

        <button type="submit">Registra Prestito</button>
      </form>
      <a class="back-link" href="../index.php">Torna alla Homepage</a>
    </main>
  </div>
</body>
</html>
