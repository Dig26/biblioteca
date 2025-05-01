<?php
include 'php/config.php';

// Definire il numero di elementi per pagina
$per_page = 12;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $per_page;

// Inizializza le variabili di filtro
$where_clauses = [];
$params = [];

// Filtro per autore
if (isset($_GET['autore']) && !empty($_GET['autore'])) {
  $autore = $conn->real_escape_string($_GET['autore']);
  $where_clauses[] = "o.autore = '$autore'";
}

// Filtro per anno
if (isset($_GET['anno']) && !empty($_GET['anno'])) {
  $anno = $_GET['anno'];
  switch ($anno) {
    case '2020-2025':
      $where_clauses[] = "o.anno_prima_pub BETWEEN 2020 AND 2025";
      break;
    case '2010-2019':
      $where_clauses[] = "o.anno_prima_pub BETWEEN 2010 AND 2019";
      break;
    case '2000-2009':
      $where_clauses[] = "o.anno_prima_pub BETWEEN 2000 AND 2009";
      break;
    case '1990-1999':
      $where_clauses[] = "o.anno_prima_pub BETWEEN 1990 AND 1999";
      break;
    case '1980-1989':
      $where_clauses[] = "o.anno_prima_pub BETWEEN 1980 AND 1989";
      break;
    case '1970-1979':
      $where_clauses[] = "o.anno_prima_pub BETWEEN 1970 AND 1979";
      break;
    case 'pre-1970':
      $where_clauses[] = "o.anno_prima_pub < 1970";
      break;
  }
}

// Filtro per editore
if (isset($_GET['editore']) && !empty($_GET['editore'])) {
  $id_editore = (int)$_GET['editore'];
  $where_clauses[] = "c.id_editore = $id_editore";
}

// Filtro per disponibilità
if (isset($_GET['disponibilita']) && !empty($_GET['disponibilita'])) {
  if ($_GET['disponibilita'] == 'available') {
    $where_clauses[] = "p.id_prestito IS NULL";
  } elseif ($_GET['disponibilita'] == 'borrowed') {
    $where_clauses[] = "p.id_prestito IS NOT NULL";
  }
}

// Costruisci la condizione WHERE
$where_condition = "";
if (!empty($where_clauses)) {
  $where_condition = "WHERE " . implode(" AND ", $where_clauses);
}

// Query per recuperare tutte le opere con filtri
$sql = "SELECT o.id_opera, o.titolo, o.autore, o.anno_prima_pub, e.nome as editore
        FROM opera o
        LEFT JOIN edizione ed ON o.id_opera = ed.id_opera
        LEFT JOIN collana c ON ed.id_collana = c.id_collana
        LEFT JOIN editore e ON c.id_editore = e.id_editore
        LEFT JOIN volume v ON ed.id_edizione = v.id_edizione
        LEFT JOIN esemplare es ON v.id_volume = es.id_volume
        LEFT JOIN prestito p ON es.Id_esemplare = p.id_esemplare
        $where_condition
        GROUP BY o.id_opera
        ORDER BY o.titolo
        LIMIT $per_page OFFSET $offset";

$result = $conn->query($sql);
$opere = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $opere[] = $row;
  }
}

// Conteggio totale per paginazione
$sql_count = "SELECT COUNT(DISTINCT o.id_opera) as total
              FROM opera o
              LEFT JOIN edizione ed ON o.id_opera = ed.id_opera
              LEFT JOIN collana c ON ed.id_collana = c.id_collana
              LEFT JOIN editore e ON c.id_editore = e.id_editore
              LEFT JOIN volume v ON ed.id_edizione = v.id_edizione
              LEFT JOIN esemplare es ON v.id_volume = es.id_volume
              LEFT JOIN prestito p ON es.Id_esemplare = p.id_esemplare
              $where_condition";
$result_count = $conn->query($sql_count);
$total_opere = ($result_count) ? $result_count->fetch_assoc()['total'] : 0;
$total_pages = ceil($total_opere / $per_page);

// Query per contare totali
$total_esemplari = $conn->query("SELECT COUNT(*) as total FROM esemplare")->fetch_assoc()['total'];

// Preparare elenco autori per filtro
$autori = [];
$sql_autori = "SELECT DISTINCT autore FROM opera ORDER BY autore";
$result_autori = $conn->query($sql_autori);
if ($result_autori && $result_autori->num_rows > 0) {
  while ($row = $result_autori->fetch_assoc()) {
    $autori[] = $row['autore'];
  }
}

// Preparare elenco editori per filtro
$editori = [];
$sql_editori = "SELECT * FROM editore ORDER BY nome";
$result_editori = $conn->query($sql_editori);
if ($result_editori && $result_editori->num_rows > 0) {
  while ($row = $result_editori->fetch_assoc()) {
    $editori[] = $row;
  }
}

// Costruisci URL per la paginazione (mantenendo i filtri)
function buildPageUrl($page)
{
  $params = $_GET;
  $params['page'] = $page;
  return 'catalogo.php?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BiblioTech - Catalogo</title>
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
        <h1 class="title">Catalogo Biblioteca</h1>
        <div class="search-bar">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input type="text" placeholder="Cerca titolo, autore..." id="search-input">
        </div>
      </div>

      <!-- Filters section -->
      <form method="get" action="catalogo.php" id="filter-form">
        <div class="filter-section">
          <h2 class="filter-title">Filtra Risultati</h2>
          <div class="filters">
            <div class="filter-group">
              <label class="filter-label" for="author-filter">Autore</label>
              <select class="filter-select" id="author-filter" name="autore">
                <option value="">Tutti gli autori</option>
                <?php foreach ($autori as $autore): ?>
                  <option value="<?php echo htmlspecialchars($autore); ?>" <?php echo (isset($_GET['autore']) && $_GET['autore'] == $autore) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($autore); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="filter-group">
              <label class="filter-label" for="year-filter">Anno di pubblicazione</label>
              <select class="filter-select" id="year-filter" name="anno">
                <option value="">Tutti gli anni</option>
                <option value="2020-2025" <?php echo (isset($_GET['anno']) && $_GET['anno'] == '2020-2025') ? 'selected' : ''; ?>>2020-2025</option>
                <option value="2010-2019" <?php echo (isset($_GET['anno']) && $_GET['anno'] == '2010-2019') ? 'selected' : ''; ?>>2010-2019</option>
                <option value="2000-2009" <?php echo (isset($_GET['anno']) && $_GET['anno'] == '2000-2009') ? 'selected' : ''; ?>>2000-2009</option>
                <option value="1990-1999" <?php echo (isset($_GET['anno']) && $_GET['anno'] == '1990-1999') ? 'selected' : ''; ?>>1990-1999</option>
                <option value="1980-1989" <?php echo (isset($_GET['anno']) && $_GET['anno'] == '1980-1989') ? 'selected' : ''; ?>>1980-1989</option>
                <option value="1970-1979" <?php echo (isset($_GET['anno']) && $_GET['anno'] == '1970-1979') ? 'selected' : ''; ?>>1970-1979</option>
                <option value="pre-1970" <?php echo (isset($_GET['anno']) && $_GET['anno'] == 'pre-1970') ? 'selected' : ''; ?>>Prima del 1970</option>
              </select>
            </div>

            <div class="filter-group">
              <label class="filter-label" for="publisher-filter">Editore</label>
              <select class="filter-select" id="publisher-filter" name="editore">
                <option value="">Tutti gli editori</option>
                <?php foreach ($editori as $editore): ?>
                  <option value="<?php echo $editore['id_editore']; ?>" <?php echo (isset($_GET['editore']) && $_GET['editore'] == $editore['id_editore']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($editore['nome']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="filter-group">
              <label class="filter-label" for="availability-filter">Disponibilità</label>
              <select class="filter-select" id="availability-filter" name="disponibilita">
                <option value="">Tutte le disponibilità</option>
                <option value="available" <?php echo (isset($_GET['disponibilita']) && $_GET['disponibilita'] == 'available') ? 'selected' : ''; ?>>Disponibili</option>
                <option value="borrowed" <?php echo (isset($_GET['disponibilita']) && $_GET['disponibilita'] == 'borrowed') ? 'selected' : ''; ?>>In prestito</option>
              </select>
            </div>

            <div class="filter-group" style="display: flex; align-items: flex-end;">
              <button type="submit" class="btn btn-primary">Filtra</button>
              <button type="button" class="btn btn-secondary" style="margin-left: 10px;" id="reset-filters">Reset</button>
            </div>
          </div>
        </div>
      </form>

      <!-- View controls -->
      <div class="header" style="margin-bottom: 1rem;">
        <div>
          <span style="font-weight: 500;"><?php echo $total_opere; ?> Opere • <?php echo $total_esemplari; ?> Esemplari</span>
        </div>

        <div class="view-toggle">
          <button class="view-btn active" id="grid-view">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
          </button>
          <button class="view-btn" id="list-view">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="21" y1="6" x2="3" y2="6"></line>
              <line x1="21" y1="12" x2="3" y2="12"></line>
              <line x1="21" y1="18" x2="3" y2="18"></line>
            </svg>
          </button>
        </div>
      </div>

      <!-- Books grid -->
      <div class="book-grid" id="books-container">
        <?php if (count($opere) > 0): ?>
          <?php foreach ($opere as $opera):
            // Verifico se ci sono esemplari disponibili per questa opera
            $sql_disponibile = "SELECT COUNT(es.Id_esemplare) as disponibili
                              FROM esemplare es
                              JOIN volume v ON es.id_volume = v.id_volume
                              JOIN edizione ed ON v.id_edizione = ed.id_edizione
                              WHERE ed.id_opera = {$opera['id_opera']} 
                              AND es.Id_esemplare NOT IN (
                                  SELECT id_esemplare FROM prestito WHERE id_prestito IS NOT NULL
                              )";
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
                <div class="status-badge" style="background-color: <?php echo $disponibile ? 'var(--secondary)' : 'var(--danger)'; ?>">
                  <?php echo $disponibile ? 'Disponibile' : 'In prestito'; ?>
                </div>
              </div>
              <div class="book-info">
                <div class="book-title"><?php echo htmlspecialchars($opera['titolo']); ?></div>
                <div class="book-author"><?php echo htmlspecialchars($opera['autore']); ?></div>
                <div class="book-meta">
                  <span><?php echo $opera['anno_prima_pub']; ?></span>
                  <span><?php echo htmlspecialchars($opera['editore'] ?? 'Non specificato'); ?></span>
                </div>
                <div class="book-actions">
                  <a href="opera.php?id=<?php echo $opera['id_opera']; ?>" class="book-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Dettagli
                  </a>
                  <?php if ($disponibile): ?>
                    <a href="prestiti.php?opera_id=<?php echo $opera['id_opera']; ?>" class="book-btn" style="margin-left: 0.5rem;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 6.1H3"></path>
                        <path d="M21 12.1H3"></path>
                        <path d="M15.1 18H3"></path>
                      </svg>
                      Presta
                    </a>
                  <?php else: ?>
                    <span class="book-btn" style="margin-left: 0.5rem; background-color: var(--gray-200); color: var(--gray-500); cursor: not-allowed;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 6.1H3"></path>
                        <path d="M21 12.1H3"></path>
                        <path d="M15.1 18H3"></path>
                      </svg>
                      Non disponibile
                    </span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="grid-column: 1 / -1; text-align: center; padding: 2rem;">
            <p>Nessuna opera trovata con i criteri di ricerca specificati.</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Pagination -->
      <?php if ($total_pages > 1): ?>
        <div class="paginator">
          <?php if ($current_page > 1): ?>
            <a href="<?php echo buildPageUrl($current_page - 1); ?>" class="page-btn">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                <polyline points="15 18 9 12 15 6"></polyline>
              </svg>
            </a>
          <?php endif; ?>

          <?php
          // Determine range of page numbers to show
          $range = 2; // Show 2 pages before and after current
          $start_page = max(1, $current_page - $range);
          $end_page = min($total_pages, $current_page + $range);

          // Show first page if not in range
          if ($start_page > 1) {
            echo '<a href="' . buildPageUrl(1) . '" class="page-btn">1</a>';
            if ($start_page > 2) {
              echo '<span class="page-btn">...</span>';
            }
          }

          // Show page numbers in range
          for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $current_page) {
              echo '<span class="page-btn active">' . $i . '</span>';
            } else {
              echo '<a href="' . buildPageUrl($i) . '" class="page-btn">' . $i . '</a>';
            }
          }

          // Show last page if not in range
          if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
              echo '<span class="page-btn">...</span>';
            }
            echo '<a href="' . buildPageUrl($total_pages) . '" class="page-btn">' . $total_pages . '</a>';
          }
          ?>

          <?php if ($current_page < $total_pages): ?>
            <a href="<?php echo buildPageUrl($current_page + 1); ?>" class="page-btn">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>

  <script src="assets/js/main.js"></script>
  <script src="assets/js/catalogo.js"></script>
  <script>
    // Gestione del reset dei filtri
    document.getElementById('reset-filters').addEventListener('click', function() {
      window.location.href = 'catalogo.php';
    });
  </script>
</body>

</html>