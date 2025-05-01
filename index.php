<?php
include 'php/config.php';

// Conteggio delle opere
$sql_opere = "SELECT COUNT(*) as total FROM opera";
$result_opere = $conn->query($sql_opere);
$count_opere = ($result_opere) ? $result_opere->fetch_assoc()['total'] : 0;

// Conteggio degli esemplari
$sql_esemplari = "SELECT COUNT(*) as total FROM esemplare";
$result_esemplari = $conn->query($sql_esemplari);
$count_esemplari = ($result_esemplari) ? $result_esemplari->fetch_assoc()['total'] : 0;

// Conteggio degli utenti
$sql_utenti = "SELECT COUNT(*) as total FROM utente";
$result_utenti = $conn->query($sql_utenti);
$count_utenti = ($result_utenti) ? $result_utenti->fetch_assoc()['total'] : 0;

// Conteggio dei prestiti attivi
$sql_prestiti = "SELECT COUNT(*) as total FROM prestito WHERE data_restituzione >= CURDATE()";
$result_prestiti = $conn->query($sql_prestiti);
$count_prestiti = ($result_prestiti) ? $result_prestiti->fetch_assoc()['total'] : 0;

// Ultime opere aggiunte
$sql_ultime_opere = "SELECT id_opera, titolo, autore, anno_prima_pub FROM opera ORDER BY id_opera DESC LIMIT 3";
$result_ultime_opere = $conn->query($sql_ultime_opere);
$ultime_opere = [];
if ($result_ultime_opere && $result_ultime_opere->num_rows > 0) {
  while ($row = $result_ultime_opere->fetch_assoc()) {
    $ultime_opere[] = $row;
  }
}

// Opere per il catalogo preview
$sql_catalogo = "SELECT id_opera, titolo, autore, anno_prima_pub FROM opera ORDER BY titolo LIMIT 4";
$result_catalogo = $conn->query($sql_catalogo);
$catalogo_opere = [];
if ($result_catalogo && $result_catalogo->num_rows > 0) {
  while ($row = $result_catalogo->fetch_assoc()) {
    $catalogo_opere[] = $row;
  }
}

// Data ultima aggiunta
$ultima_data = date("d M Y");
?>
<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BiblioTech - Sistema di Gestione Biblioteca</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
  <div class="layout">
    <!-- Sidebar navigation -->
    <aside class="sidebar">
      <a href="index.php" class="logo">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 6.25278V19.2528M12 6.25278C10.8321 5.47686 9.24649 5 7.5 5C5.75351 5 4.16789 5.47686 3 6.25278V19.2528C4.16789 18.4769 5.75351 18 7.5 18C9.24649 18 10.8321 18.4769 12 19.2528M12 6.25278C13.1679 5.47686 14.7535 5 16.5 5C18.2465 5 19.8321 5.47686 21 6.25278V19.2528C19.8321 18.4769 18.2465 18 16.5 18C14.7535 18 13.1679 18.4769 12 19.2528"></path>
        </svg>
        <span>BiblioTech</span>
      </a>

      <div class="nav-title">GENERALE</div>
      <nav>
        <a href="index.php" class="nav-item active">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          Dashboard
        </a>
        <a href="catalogo.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
          </svg>
          Catalogo
        </a>
        <a href="prestiti.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 6.1H3"></path>
            <path d="M21 12.1H3"></path>
            <path d="M15.1 18H3"></path>
          </svg>
          Prestiti
        </a>
        <a href="utenti.php" class="nav-item">
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
        <a href="amministrazione/editori.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
          </svg>
          Editori
        </a>
        <a href="amministrazione/collane.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path>
            <line x1="16" y1="8" x2="2" y2="22"></line>
            <line x1="17.5" y1="15" x2="9" y2="15"></line>
          </svg>
          Collane
        </a>
        <a href="amministrazione/edizioni.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
          </svg>
          Edizioni
        </a>
        <a href="amministrazione/rapporti.php" class="nav-item">
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
        <h1 class="title">Dashboard</h1>
        <div class="search-bar">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <form action="catalogo.php" method="get">
            <input type="text" name="search" placeholder="Cerca libri, autori, utenti...">
          </form>
        </div>
      </div>
      
      <!-- Stats overview -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
            </svg>
          </div>
          <div class="stat-title">Totale Opere</div>
          <div class="stat-value"><?php echo $count_opere; ?></div>
          <div class="stat-footer">Ultima aggiunta: <?php echo $ultima_data; ?></div>
        </div>

        <div class="stat-card">
          <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2H2v10h10V2zM22 2h-8v10h8V2zM12 14H2v8h10v-8zM22 14h-8v8h8v-8z"></path>
            </svg>
          </div>
          <div class="stat-title">Totale Esemplari</div>
          <div class="stat-value"><?php echo $count_esemplari; ?></div>
          <div class="stat-footer">Disponibili: <?php echo $count_esemplari - $count_prestiti; ?></div>
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
          <div class="stat-value"><?php echo $count_utenti; ?></div>
          <div class="stat-footer">Attivi: <?php echo $count_utenti; ?></div>
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
          <div class="stat-value"><?php echo $count_prestiti; ?></div>
          <div class="stat-footer">In scadenza oggi: 0</div>
        </div>
      </div>

      <!-- Recent activity -->
      <div class="section">
        <div class="section-header">
          <h2 class="section-title">Ultime Opere Aggiunte</h2>
          <a href="php/inserisci_opera.php" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Aggiungi Opera
          </a>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>Titolo</th>
                <th>Autore</th>
                <th>Anno</th>
                <th>Disponibilità</th>
                <th>Azioni</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($ultime_opere as $opera):
                // Verifico se ci sono esemplari disponibili per questa opera
                $sql_disponibile = "SELECT COUNT(*) as disponibili
                   FROM opera o
                   JOIN edizione ed ON o.id_opera = ed.id_opera
                   JOIN volume v ON ed.id_edizione = v.id_edizione
                   JOIN esemplare es ON v.id_volume = es.id_volume
                   LEFT JOIN prestito p ON es.Id_esemplare = p.id_esemplare
                   WHERE o.id_opera = {$opera['id_opera']} 
                   AND p.id_prestito IS NULL";
                $result_disponibile = $conn->query($sql_disponibile);
                $esemplari_disponibili = ($result_disponibile && $result_disponibile->num_rows > 0) ? $result_disponibile->fetch_assoc()['disponibili'] : 0;

                // Determina lo stato
                $disponibile = $esemplari_disponibili > 0;
              ?>
                <tr>
                  <td><?php echo htmlspecialchars($opera['titolo']); ?></td>
                  <td><?php echo htmlspecialchars($opera['autore']); ?></td>
                  <td><?php echo $opera['anno_prima_pub']; ?></td>
                  <td><span class="status <?php echo $disponibile ? 'status-available' : 'status-borrowed'; ?>"><?php echo $disponibile ? 'Disponibile' : 'In prestito'; ?></span></td>
                  <td>
                    <div class="table-actions">
                      <a href="opera.php?id=<?php echo $opera['id_opera']; ?>" class="action-btn" title="Visualizza">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                          <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                      </a>
                      <a href="php/modifica_opera.php?id=<?php echo $opera['id_opera']; ?>" class="action-btn" title="Modifica">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($ultime_opere)): ?>
                <tr>
                  <td colspan="5" style="text-align: center;">Nessuna opera presente.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Prestiti section -->
      <div class="section">
        <div class="section-header">
          <h2 class="section-title">Prestiti Recenti</h2>
          <a href="prestiti.php" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuovo Prestito
          </a>
        </div>

        <div class="table-container">
          <?php if ($count_prestiti > 0): ?>
            <table>
              <thead>
                <tr>
                  <th>Utente</th>
                  <th>Opera</th>
                  <th>Data Restituzione</th>
                  <th>Stato</th>
                  <th>Azioni</th>
                </tr>
              </thead>
              <tbody>
                <!-- Per ora non abbiamo dati reali qui -->
                <tr>
                  <td colspan="5" style="text-align: center;">Caricamento prestiti in corso...</td>
                </tr>
              </tbody>
            </table>
          <?php else: ?>
            <p style="text-align: center; padding: 2rem; color: var(--gray-500);">Nessun prestito registrato al momento.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Catalog preview -->
      <div class="section">
        <div class="section-header">
          <h2 class="section-title">Anteprima Catalogo</h2>
          <a href="catalogo.php" class="btn btn-secondary">
            Vedi tutto il catalogo
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </a>
        </div>

        <div class="card-grid">
          <?php foreach ($catalogo_opere as $opera):
            // Verifico se ci sono esemplari disponibili per questa opera
            $sql_disponibile = "SELECT COUNT(*) as disponibili
                               FROM opera o
                               JOIN edizione ed ON o.id_opera = ed.id_opera
                               JOIN volume v ON ed.id_edizione = v.id_edizione
                               JOIN esemplare es ON v.id_volume = es.id_volume
                               LEFT JOIN prestito p ON es.Id_esemplare = p.id_esemplare
                               WHERE o.id_opera = {$opera['id_opera']} 
                               AND p.id_prestito IS NULL";
            $result_disponibile = $conn->query($sql_disponibile);
            $esemplari_disponibili = ($result_disponibile && $result_disponibile->num_rows > 0) ? $result_disponibile->fetch_assoc()['disponibili'] : 0;

            // Determina lo stato
            $disponibile = $esemplari_disponibili > 0;
          ?>
            <div class="book-card">
              <div class="book-cover">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="width: 80px; height: 80px;">
                  <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                </svg>
                <?php if ($disponibile): ?>
                  <div class="status-badge" style="background-color: var(--secondary);">Disponibile</div>
                <?php else: ?>
                  <div class="status-badge" style="background-color: var(--danger);">In prestito</div>
                <?php endif; ?>
              </div>
              <div class="book-info">
                <div class="book-title"><?php echo htmlspecialchars($opera['titolo']); ?></div>
                <div class="book-author"><?php echo htmlspecialchars($opera['autore']); ?></div>
                <div class="book-meta">
                  <span><?php echo $opera['anno_prima_pub']; ?></span>
                  <span>
                    <?php
                    // Recupera l'editore
                    $sql_editore = "SELECT e.nome FROM editore e 
                                JOIN collana c ON e.id_editore = c.id_editore 
                                JOIN edizione ed ON c.id_collana = ed.id_collana 
                                JOIN opera o ON ed.id_opera = o.id_opera 
                                WHERE o.id_opera = " . $opera['id_opera'] . " LIMIT 1";
                    $result_editore = $conn->query($sql_editore);
                    if ($result_editore && $result_editore->num_rows > 0) {
                      echo $result_editore->fetch_assoc()['nome'];
                    } else {
                      echo "N/D";
                    }
                    ?>
                  </span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          <?php if (empty($catalogo_opere)): ?>
            <p style="text-align: center; padding: 2rem; color: var(--gray-500); grid-column: 1 / -1;">Nessuna opera presente nel catalogo.</p>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
  <script src="assets/js/main.js"></script>
</body>

</html>