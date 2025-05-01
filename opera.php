<?php
include 'php/config.php';

$id_opera = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_opera == 0) {
  header("Location: catalogo.php");
  exit;
}

// Recupera i dettagli dell'opera
$opera = null;
$sql = "SELECT * FROM opera WHERE id_opera = $id_opera";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  $opera = $result->fetch_assoc();
} else {
  // Opera non trovata, reindirizza al catalogo
  header("Location: catalogo.php");
  exit;
}

// Recupera le edizioni dell'opera
$edizioni = [];
$sql_edizioni = "SELECT e.*, c.nome as collana, ed.nome as editore
                FROM edizione e
                INNER JOIN collana c ON e.id_collana = c.id_collana
                INNER JOIN editore ed ON c.id_editore = ed.id_editore
                WHERE e.id_opera = $id_opera
                ORDER BY e.anno DESC";
$result_edizioni = $conn->query($sql_edizioni);
if ($result_edizioni && $result_edizioni->num_rows > 0) {
  while ($row = $result_edizioni->fetch_assoc()) {
    $edizioni[] = $row;
  }
}

// Recupera gli esemplari disponibili di quest'opera
$esemplari = [];
$sql_esemplari = "SELECT es.Id_esemplare, v.n_volume, e.anno, c.nome as collana, ed.nome as editore
                 FROM esemplare es
                 INNER JOIN volume v ON es.id_volume = v.id_volume
                 INNER JOIN edizione e ON v.id_edizione = e.id_edizione
                 INNER JOIN collana c ON e.id_collana = c.id_collana
                 INNER JOIN editore ed ON c.id_editore = ed.id_editore
                 LEFT JOIN prestito p ON es.Id_esemplare = p.id_esemplare
                 WHERE e.id_opera = $id_opera AND p.id_prestito IS NULL
                 ORDER BY es.Id_esemplare";
$result_esemplari = $conn->query($sql_esemplari);
if ($result_esemplari && $result_esemplari->num_rows > 0) {
  while ($row = $result_esemplari->fetch_assoc()) {
    $esemplari[] = $row;
  }
}

// Altre opere dello stesso autore
$altre_opere = [];
$autore = $conn->real_escape_string($opera['autore']);
$sql_altre = "SELECT * FROM opera 
             WHERE autore = '$autore' AND id_opera != $id_opera
             ORDER BY anno_prima_pub DESC
             LIMIT 4";
$result_altre = $conn->query($sql_altre);
if ($result_altre && $result_altre->num_rows > 0) {
  while ($row = $result_altre->fetch_assoc()) {
    $altre_opere[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BiblioTech - <?php echo htmlspecialchars($opera['titolo']); ?></title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="assets/css/guide.css">
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
        <a href="index.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          Dashboard
        </a>
        <a href="catalogo.php" class="nav-item active">
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
        <div>
          <a href="catalogo.php" class="btn btn-secondary" style="margin-bottom: 1rem;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
              <line x1="19" y1="12" x2="5" y2="12"></line>
              <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Torna al Catalogo
          </a>
          <h1 class="title"><?php echo htmlspecialchars($opera['titolo']); ?></h1>
        </div>
      </div>

      <!-- Book details -->
      <div class="section" style="display: flex; gap: 2rem; flex-wrap: wrap;">
        <div style="flex: 0 0 300px;">
          <div style="height: 400px; background-color: var(--gray-200); display: flex; align-items: center; justify-content: center; border-radius: var(--radius);">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="width: 100px; height: 100px;">
              <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
            </svg>
          </div>

          <?php if (count($esemplari) > 0): ?>
            <div style="margin-top: 1rem;">
              <a href="prestiti.php?opera_id=<?php echo $id_opera; ?>" class="btn btn-primary" style="width: 100%;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 6.1H3"></path>
                  <path d="M21 12.1H3"></path>
                  <path d="M15.1 18H3"></path>
                </svg>
                Presta un esemplare
              </a>
            </div>
          <?php endif; ?>
        </div>

        <div style="flex: 1; min-width: 300px;">
          <div style="margin-bottom: 2rem;">
            <h2 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Informazioni sull'Opera</h2>
            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 0.5rem; margin-top: 1rem;">
              <div style="font-weight: 500;">Autore:</div>
              <div><?php echo htmlspecialchars($opera['autore']); ?></div>

              <div style="font-weight: 500;">Anno prima pubblicazione:</div>
              <div><?php echo $opera['anno_prima_pub']; ?></div>

              <div style="font-weight: 500;">Esemplari disponibili:</div>
              <div>
                <?php if (count($esemplari) > 0): ?>
                  <span class="status status-available"><?php echo count($esemplari); ?> disponibili</span>
                <?php else: ?>
                  <span class="status status-borrowed">Nessun esemplare disponibile</span>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div>
            <h2 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Edizioni disponibili</h2>
            <?php if (count($edizioni) > 0): ?>
              <table>
                <thead>
                  <tr>
                    <th>Anno</th>
                    <th>Collana</th>
                    <th>Editore</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($edizioni as $edizione): ?>
                    <tr>
                      <td><?php echo $edizione['anno']; ?></td>
                      <td><?php echo htmlspecialchars($edizione['collana']); ?></td>
                      <td><?php echo htmlspecialchars($edizione['editore']); ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <p>Nessuna edizione registrata per quest'opera.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Esemplari disponibili -->
      <?php if (count($esemplari) > 0): ?>
        <div class="section">
          <div class="section-header">
            <h2 class="section-title">Esemplari Disponibili</h2>
          </div>

          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>N° Inventario</th>
                  <th>N° Volume</th>
                  <th>Anno Edizione</th>
                  <th>Collana</th>
                  <th>Editore</th>
                  <th>Azioni</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($esemplari as $esemplare): ?>
                  <tr>
                    <td><?php echo $esemplare['Id_esemplare']; ?></td>
                    <td><?php echo $esemplare['n_volume']; ?></td>
                    <td><?php echo $esemplare['anno']; ?></td>
                    <td><?php echo htmlspecialchars($esemplare['collana']); ?></td>
                    <td><?php echo htmlspecialchars($esemplare['editore']); ?></td>
                    <td>
                      <a href="prestiti.php?esemplare_id=<?php echo $esemplare['Id_esemplare']; ?>" class="btn btn-sm btn-primary">Presta</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>

      <!-- Altre opere dell'autore -->
      <?php if (count($altre_opere) > 0): ?>
        <div class="section">
          <div class="section-header">
            <h2 class="section-title">Altre opere di <?php echo htmlspecialchars($opera['autore']); ?></h2>
          </div>

          <div class="card-grid">
            <?php foreach ($altre_opere as $altra_opera): ?>
              <div class="book-card">
                <div class="book-cover">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="width: 80px; height: 80px;">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                  </svg>
                </div>
                <div class="book-info">
                  <div class="book-title"><?php echo htmlspecialchars($altra_opera['titolo']); ?></div>
                  <div class="book-author"><?php echo htmlspecialchars($altra_opera['autore']); ?></div>
                  <div class="book-meta">
                    <span><?php echo $altra_opera['anno_prima_pub']; ?></span>
                  </div>
                  <div class="book-actions">
                    <a href="opera.php?id=<?php echo $altra_opera['id_opera']; ?>" class="book-btn">
                      Dettagli
                    </a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </main>
  </div>

  <script src="assets/js/main.js"></script>
  <script src="assets/js/guide.js"></script>
</body>

</html>