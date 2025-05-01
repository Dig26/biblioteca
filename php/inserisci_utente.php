<?php
include 'config.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $conn->real_escape_string($_POST['nome']);
    $cognome = $conn->real_escape_string($_POST['cognome']);
    $telefono = $conn->real_escape_string($_POST['telefono']);

    $sql = "INSERT INTO utente (nome, cognome, telefono) 
            VALUES ('$nome', '$cognome', '$telefono')";
    if ($conn->query($sql) === TRUE) {
        $message = "Utente inserito con successo.";
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
  <title>Inserisci Utente</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Inserisci Utente</h1>
    </header>
    <main>
      <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
      <?php endif; ?>
      <form method="post" action="inserisci_utente.php">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" required>
        
        <label for="cognome">Cognome:</label>
        <input type="text" name="cognome" id="cognome" required>

        <label for="telefono">Telefono:</label>
        <input type="text" name="telefono" id="telefono" required>

        <button type="submit">Inserisci</button>
      </form>
      <a class="back-link" href="../index.php">Torna alla Homepage</a>
    </main>
  </div>
</body>
</html>
