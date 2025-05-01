<?php
include 'config.php';

/* Se consideriamo "attivi" i prestiti
   la cui data_restituzione >= oggi: */
$sql = "SELECT p.id_prestito,
               p.data_restituzione,
               u.nome,
               u.cognome,
               e.Id_esemplare AS inventario
        FROM prestito p
        INNER JOIN utente u ON p.id_utente = u.id_utente
        INNER JOIN esemplare e ON p.id_esemplare = e.Id_esemplare
        WHERE p.data_restituzione >= CURDATE()
        ORDER BY p.data_restituzione";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestiti Attivi</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Prestiti Attivi</h1>
        </header>
        <main>
            <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th>ID Prestito</th>
                        <th>Nome Utente</th>
                        <th>Cognome Utente</th>
                        <th>Inventario Esemplare</th>
                        <th>Data Restituzione Prevista</th>
                        <th>Azioni</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row["id_prestito"]; ?></td>
                            <td><?php echo $row["nome"]; ?></td>
                            <td><?php echo $row["cognome"]; ?></td>
                            <td><?php echo $row["inventario"]; ?></td>
                            <td><?php echo $row["data_restituzione"]; ?></td>
                            <td>
                                <form action="restituisci_prestito.php" method="get" style="display:inline;">
                                    <input type="hidden" name="id_prestito" value="<?php echo $row['id_prestito']; ?>">
                                    <button type="submit" class="btn-secondary">Restituisci</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                </table>
            <?php else: ?>
                <p>Nessun prestito attivo.</p>
            <?php endif; ?>
            <a class="back-link" href="../index.php">Torna alla Homepage</a>
        </main>
    </div>
</body>

</html>