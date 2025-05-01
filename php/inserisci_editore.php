<?php
include 'config.php';

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $conn->real_escape_string($_POST['nome']);
    $sql = "INSERT INTO editore (nome) VALUES ('$nome')";
    if ($conn->query($sql) === TRUE) {
        $message = "Editore inserito con successo.";
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
  <title>Inserisci Editore</title>
  <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Inserisci Editore</h1>
    </header>
    <main>
      <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
      <?php endif; ?>
      <form method="post" action="inserisci_editore.php">
        <label for="nome">Nome Editore:</label>
        <input type="text" name="nome" id="nome" required>
        <button type="submit">Inserisci</button>
      </form>
      <a class="back-link" href="../index.php">Torna alla Homepage</a>
    </main>
  </div>
</body>
</html>
