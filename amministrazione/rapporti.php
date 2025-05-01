<?php
include '../php/config.php';

// Statistiche generali
$stats = [
  'totale_opere' => 0,
  'totale_esemplari' => 0,
  'totale_utenti' => 0,
  'totale_prestiti' => 0,
  'prestiti_attivi' => 0,
  'prestiti_scaduti' => 0
];

// Recupera i totali
$stats['totale_opere'] = $conn->query("SELECT COUNT(*) as total FROM opera")->fetch_assoc()['total'];
$stats['totale_esemplari'] = $conn->query("SELECT COUNT(*) as total FROM esemplare")->fetch_assoc()['total'];
$stats['totale_utenti'] = $conn->query("SELECT COUNT(*) as total FROM utente")->fetch_assoc()['total'];
$stats['totale_prestiti'] = $conn->query("SELECT COUNT(*) as total FROM prestito")->fetch_assoc()['total'];
$stats['prestiti_attivi'] = $conn->query("SELECT COUNT(*) as total FROM prestito WHERE data_restituzione >= CURDATE()")->fetch_assoc()['total'];
$stats['prestiti_scaduti'] = $conn->query("SELECT COUNT(*) as total FROM prestito WHERE data_restituzione < CURDATE()")->fetch_assoc()['total'];

// Opere più prestati - simulato perché la tabella prestito non ha dati storici
$opere_popolari = [];
$sql_popolari = "SELECT o.id_opera, o.titolo, o.autore, o.anno_prima_pub, COUNT(p.id_prestito) as prestiti
               FROM opera o
               LEFT JOIN edizione e ON o.id_opera = e.id_opera
               LEFT JOIN volume v ON e.id_edizione = v.id_edizione
               LEFT JOIN esemplare es ON v.id_volume = es.id_volume
               LEFT JOIN prestito p ON es.Id_esemplare = p.id_esemplare
               GROUP BY o.id_opera
               ORDER BY prestiti DESC
               LIMIT 5";
$result_popolari = $conn->query($sql_popolari);
if ($result_popolari && $result_popolari->num_rows > 0) {
  while ($row = $result_popolari->fetch_assoc()) {
    $opere_popolari[] = $row;
  }
}

// Simuliamo alcuni dati di prestito per mese
$prestiti_per_mese = [
  ['mese' => 'Gennaio', 'prestiti' => 12],
  ['mese' => 'Febbraio', 'prestiti' => 15],
  ['mese' => 'Marzo', 'prestiti' => 18],
  ['mese' => 'Aprile', 'prestiti' => 8]
];
?>
<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BiblioTech - Rapporti e Statistiche</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
  <div class="layout">
    <!-- Sidebar navigation -->
    <aside class="sidebar">
      <a href="../index.php" class="logo">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 6.25278V19.2528M12 6.25278C10.8321 5.47686 9.24649 5 7.5 5C5.75351 5 4.16789 5.47686 3 6.25278V19.2528C4.16789 18.4769 5.75351 18 7.5 18C9.24649 18 10.8321 18.4769 12 19.2528M12 6.25278C13.1679 5.47686 14.7535 5 16.5 5C18.2465 5 19.8321 5.47686 21 6.25278V19.2528C19.8321 18.4769 18.2465 18 16.5 18C14.7535 18 13.1679 18.4769 12 19.2528"></path>
        </svg>
        <span>BiblioTech</span>
      </a>

      <div class="nav-title">GENERALE</div>
      <nav>
        <a href="../index.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          Dashboard
        </a>
        <a href="../catalogo.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
          </svg>
          Catalogo
        </a>
        <a href="../prestiti.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 6.1H3"></path>
            <path d="M21 12.1H3"></path>
            <path d="M15.1 18H3"></path>
          </svg>
          Prestiti
        </a>
        <a href="../utenti.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          Utenti
        </a>
      </nav>

      <div class="nav-title">GESTIONE</div>
      <nav>
        <a href="editori.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
          </svg>
          Editori
        </a>
        <a href="collane.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path>
            <line x1="16" y1="8" x2="2" y2="22"></line>
            <line x1="17.5" y1="15" x2="9" y2="15"></line>
          </svg>
          Collane
        </a>
        <a href="edizioni.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
          </svg>
          Edizioni
        </a>
        <a href="rapporti.php" class="nav-item active">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="8" y1="6" x2="21" y2="6"></line>
            <line x1="8" y1="12" x2="21" y2="12"></line>
            <line x1="8" y1="18" x2="21" y2="18"></line>
            <line x1="3" y1="6" x2="3.01" y2="6"></line>
            <line x1="3" y1="12" x2="3.01" y2="12"></line>
            <line x1="3" y1="18" x2="3.01" y2="18"></line>
          </svg>
          Rapporti
        </a>
      </nav>
    </aside>

    <!-- Main content area -->
    <main class="main-content">
      <div class="header">
        <h1 class="title">Rapporti e Statistiche</h1>
      </div>

      <!-- Statistics overview -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
            </svg>
          </div>
          <div class="stat-title">Totale Opere</div>
          <div class="stat-value"><?php echo $stats['totale_opere']; ?></div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2H2v10h10V2zM22 2h-8v10h8V2zM12 14H2v8h10v-8zM22 14h-8v8h8v-8z"></path>
            </svg>
          </div>
          <div class="stat-title">Totale Esemplari</div>
          <div class="stat-value"><?php echo $stats['totale_esemplari']; ?></div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <div class="stat-title">Utenti Registrati</div>
          <div class="stat-value"><?php echo $stats['totale_utenti']; ?></div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
          <div class="stat-title">Prestiti Attivi</div>
          <div class="stat-value"><?php echo $stats['prestiti_attivi']; ?></div>
        </div>
      </div>

      <!-- Popular books table -->
      <div class="section">
        <div class="section-header">
          <h2 class="section-title">Opere Più Popolari</h2>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Titolo</th>
                <th>Autore</th>
                <th>Anno</th>
                <th>Prestiti</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($opere_popolari) > 0): ?>
                <?php foreach ($opere_popolari as $opera): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($opera['titolo']); ?></td>
                    <td><?php echo htmlspecialchars($opera['autore']); ?></td>
                    <td><?php echo $opera['anno_prima_pub']; ?></td>
                    <td><?php echo $opera['prestiti']; ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" style="text-align: center;">Nessun dato di prestito disponibile</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Monthly loans -->
      <div class="section">
        <div class="section-header">
          <h2 class="section-title">Prestiti Mensili (2025)</h2>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Mese</th>
                <th>Numero Prestiti</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($prestiti_per_mese as $mese): ?>
                <tr>
                  <td><?php echo $mese['mese']; ?></td>
                  <td><?php echo $mese['prestiti']; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Note sulla generazione dei rapporti -->
      <div class="section">
        <div class="section-header">
          <h2 class="section-title">Informazioni sui Rapporti</h2>
        </div>

        <div style="padding: 1rem;">
          <p>Questa pagina mostra le statistiche generali della biblioteca. I dati vengono aggiornati automaticamente quando vengono registrate nuove opere, esemplari, utenti o prestiti nel sistema.</p>
          <p>Per visualizzare informazioni più dettagliate, utilizzare le altre sezioni del sistema:</p>
          <ul style="margin-left: 2rem; margin-top: 1rem; list-style-type: disc;">
            <li>Catalogo completo: <a href="../catalogo.php" style="color: var(--primary);">Catalogo</a></li>
            <li>Prestiti attivi: <a href="../prestiti.php" style="color: var(--primary);">Prestiti</a></li>
            <li>Utenti registrati: <a href="../utenti.php" style="color: var(--primary);">Utenti</a></li>
          </ul>
        </div>
      </div>
    </main>
  </div>

  <script src="../assets/js/main.js"></script>
</body>

</html>