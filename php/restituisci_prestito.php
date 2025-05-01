<?php
include 'config.php';

if (isset($_GET['id_prestito'])) {
    $id_prestito = (int)$_GET['id_prestito'];

    // Elimino la riga corrispondente dalla tabella prestito
    $sql = "DELETE FROM prestito WHERE id_prestito = $id_prestito";
    $conn->query($sql);
}

// Torno alla pagina dei prestiti attivi
header("Location: ../prestiti.php?success=1");
exit;
?>