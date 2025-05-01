<?php
// Questo script aggiorna il file CSS
$cssContent = '
/* Qui tutto il CSS precedente */
';

// Controlla se esiste la cartella assets/css
if (!file_exists('assets/css')) {
    mkdir('assets/css', 0755, true);
}

// Scrivi il CSS nel file
file_put_contents('assets/css/style.css', $cssContent);
echo "File CSS aggiornato con successo!";
?>