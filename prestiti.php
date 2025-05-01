<?php
include 'php/config.php';

// Recupera utenti per il form
$utenti = [];
$sql_utenti = "SELECT id_utente, nome, cognome FROM utente ORDER BY cognome, nome";
$result_utenti = $conn->query($sql_utenti);
if ($result_utenti && $result_utenti->num_rows > 0) {
  while ($row = $result_utenti->fetch_assoc()) {
    $utenti[] = $row;
  }
}

// Recupera esemplari per il form
$esemplari = [];
$sql_esemplari = "SELECT e.Id_esemplare, o.titolo, o.autore 
                  FROM esemplare e
                  INNER JOIN volume v ON e.id_volume = v.id_volume
                  INNER JOIN edizione ed ON v.id_edizione = ed.id_edizione
                  INNER JOIN opera o ON ed.id_opera = o.id_opera
                  LEFT JOIN prestito p ON e.Id_esemplare = p.id_esemplare
                  WHERE p.id_prestito IS NULL
                  ORDER BY o.titolo";
$result_esemplari = $conn->query($sql_esemplari);
if ($result_esemplari && $result_esemplari->num_rows > 0) {
  while ($row = $result_esemplari->fetch_assoc()) {
    $esemplari[] = $row;
  }
}

// Recupera i prestiti attivi
$prestiti = [];
$sql_prestiti = "SELECT p.id_prestito, p.data_restituzione, 
                u.nome, u.cognome, o.titolo, o.autore,
                e.Id_esemplare, CURDATE() as oggi
                FROM prestito p
                INNER JOIN utente u ON p.id_utente = u.id_utente
                INNER JOIN esemplare e ON p.id_esemplare = e.Id_esemplare
                INNER JOIN volume v ON e.id_volume = v.id_volume
                INNER JOIN edizione ed ON v.id_edizione = ed.id_edizione
                INNER JOIN opera o ON ed.id_opera = o.id_opera
                ORDER BY p.data_restituzione";
$result_prestiti = $conn->query($sql_prestiti);
if ($result_prestiti && $result_prestiti->num_rows > 0) {
  while ($row = $result_prestiti->fetch_assoc()) {
    $prestiti[] = $row;
  }
}

// Gestione del form di nuovo prestito
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id_utente = (int) $_POST['id_utente'];
  $id_esemplare = (int) $_POST['id_esemplare'];
  $data_restituzione = $conn->real_escape_string($_POST['data_restituzione']);

  $sql_insert = "INSERT INTO prestito (id_utente, id_esemplare, data_restituzione) 
                 VALUES ($id_utente, $id_esemplare, '$data_restituzione')";
  if ($conn->query($sql_insert) === TRUE) {
    $message = "Prestito registrato con successo.";
    // Ricarica la pagina per aggiornare i dati
    header("Location: prestiti.php?success=1");
    exit;
  } else {
    $message = "Errore durante l'inserimento: " . $conn->error;
  }
}

// Messaggio di successo
if (isset($_GET['success']) && $_GET['success'] == 1) {
  $message = "Operazione completata con successo.";
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BiblioTech - Gestione Prestiti</title>
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
        <a href="catalogo.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
          </svg>
          Catalogo
        </a>
        <a href="prestiti.php" class="nav-item active">
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
        <h1 class="title">Gestione Prestiti</h1>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <div class="tab active" data-tab="prestiti-attivi">Prestiti Attivi</div>
        <div class="tab" data-tab="nuovo-prestito">Nuovo Prestito</div>
      </div>

      <?php if ($message): ?>
        <div class="message" style="background-color: #dff0d8; color: #3c763d; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <!-- Search -->
      <div class="search-filters">
        <div class="search-bar">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input type="text" placeholder="Cerca per utente o libro..." id="search-prestiti">
        </div>
      </div>

      <!-- New loan form - content shown based on active tab -->
      <div class="form-section tab-content" id="nuovo-prestito-content" style="display: none;">
        <h2 class="form-title">Registra Nuovo Prestito</h2>
        <form method="post" action="prestiti.php">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="id_utente">Utente</label>
              <select class="form-select" id="id_utente" name="id_utente" required>
                <option value="">Seleziona utente</option>
                <?php foreach ($utenti as $utente): ?>
                  <option value="<?php echo $utente['id_utente']; ?>">
                    <?php echo htmlspecialchars($utente['cognome'] . ' ' . $utente['nome']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="id_esemplare">Esemplare</label>
              <select class="form-select" id="id_esemplare" name="id_esemplare" required>
                <option value="">Seleziona esemplare</option>
                <?php foreach ($esemplari as $esemplare): ?>
                  <option value="<?php echo $esemplare['Id_esemplare']; ?>">
                    Inv. <?php echo $esemplare['Id_esemplare']; ?> - <?php echo htmlspecialchars($esemplare['titolo']); ?> (<?php echo htmlspecialchars($esemplare['autore']); ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="data_prestito">Data Prestito</label>
              <input class="form-input" type="date" id="data_prestito" name="data_prestito" value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="data_restituzione">Data Restituzione Prevista</label>
              <input class="form-input" type="date" id="data_restituzione" name="data_restituzione" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="annulla-prestito">Annulla</button>
            <button type="submit" class="btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 6.1H3"></path>
                <path d="M21 12.1H3"></path>
                <path d="M15.1 18H3"></path>
              </svg>
              Registra Prestito
            </button>
          </div>
        </form>
      </div>

      <!-- Active loans table -->
      <div class="table-section tab-content" id="prestiti-attivi-content">
        <div class="table-header">
          <h2 class="table-title">Prestiti Attivi</h2>
          <button class="btn btn-primary" id="btn-nuovo-prestito">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuovo Prestito
          </button>
        </div>

        <?php if (count($prestiti) > 0): ?>
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Utente</th>
                  <th>Libro</th>
                  <th>Esemplare</th>
                  <th>Data Restituzione</th>
                  <th>Stato</th>
                  <th>Azioni</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($prestiti as $prestito):
                  // Calcola lo stato del prestito
                  $data_restituzione = strtotime($prestito['data_restituzione']);
                  $oggi = strtotime($prestito['oggi']);
                  $differenza_giorni = ($data_restituzione - $oggi) / (60 * 60 * 24);

                  if ($differenza_giorni < 0) {
                    $stato = "status-overdue";
                    $stato_text = "Prestito attivo";
                  } else {
                    $stato = "status-active";
                    $stato_text = "Prestito attivo";
                  }
                ?>
                  <tr>
                    <td><?php echo $prestito['id_prestito']; ?></td>
                    <td><?php echo htmlspecialchars($prestito['nome'] . ' ' . $prestito['cognome']); ?></td>
                    <td><?php echo htmlspecialchars($prestito['titolo']); ?></td>
                    <td><?php echo $prestito['Id_esemplare']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($prestito['data_restituzione'])); ?></td>
                    <td><span class="status <?php echo $stato; ?>"><?php echo $stato_text; ?></span></td>
                    <td>
                      <div class="table-actions">
                        <a href="opera.php?id=<?php echo $prestito['id_prestito']; ?>" class="btn btn-sm btn-secondary">Dettagli</a>
                        <a href="php/restituisci_prestito.php?id_prestito=<?php echo $prestito['id_prestito']; ?>" class="btn btn-sm btn-primary">Restituisci</a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <!-- Empty state -->
          <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
              <path d="M17 6.1H3"></path>
              <path d="M21 12.1H3"></path>
              <path d="M15.1 18H3"></path>
            </svg>
            <div class="empty-state-title">Nessun prestito attivo</div>
            <p class="empty-state-text">
              Non ci sono prestiti attivi al momento. Utilizza il form "Nuovo Prestito" per registrare un nuovo prestito.
            </p>
            <button class="btn btn-primary" id="show-nuovo-prestito">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              Registra Nuovo Prestito
            </button>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <script src="assets/js/main.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tabs = document.querySelectorAll('.tab');
      const tabContents = document.querySelectorAll('.tab-content');

      // Funzione per mostrare il tab selezionato
      function showTab(tabId) {
        tabContents.forEach(content => {
          content.style.display = 'none';
        });

        tabs.forEach(tab => {
          tab.classList.remove('active');
        });

        document.getElementById(tabId + '-content').style.display = 'block';
        document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
      }

      // Assegna eventi click ai tab
      tabs.forEach(tab => {
        tab.addEventListener('click', function() {
          showTab(this.getAttribute('data-tab'));
        });
      });

      // Bottoni per mostrare il tab "nuovo-prestito"
      const btnShowNuovoPrestito = document.getElementById('show-nuovo-prestito');
      const btnNuovoPrestito = document.getElementById('btn-nuovo-prestito');

      if (btnShowNuovoPrestito) {
        btnShowNuovoPrestito.addEventListener('click', function() {
          showTab('nuovo-prestito');
        });
      }

      if (btnNuovoPrestito) {
        btnNuovoPrestito.addEventListener('click', function() {
          showTab('nuovo-prestito');
        });
      }

      // Bottone per annullare il form di prestito
      const btnAnnullaPrestito = document.getElementById('annulla-prestito');
      if (btnAnnullaPrestito) {
        btnAnnullaPrestito.addEventListener('click', function() {
          showTab('prestiti-attivi');
        });
      }

      // Ricerca prestiti
      const searchInput = document.getElementById('search-prestiti');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const rows = document.querySelectorAll('tbody tr');

          rows.forEach(row => {
            const utente = row.cells[1]?.textContent.toLowerCase() || '';
            const libro = row.cells[2]?.textContent.toLowerCase() || '';

            if (utente.includes(searchTerm) || libro.includes(searchTerm)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }

      // Data prestito e data restituzione
      const dataPrestito = document.getElementById('data_prestito');
      const dataRestituzione = document.getElementById('data_restituzione');

      if (dataPrestito && dataRestituzione) {
        dataPrestito.addEventListener('change', function() {
          // Calcola la data di restituzione (30 giorni dopo)
          const loanDate = new Date(this.value);
          const returnDate = new Date(loanDate);
          returnDate.setDate(returnDate.getDate() + 30);

          // Formatta la data nel formato yyyy-mm-dd
          const yyyy = returnDate.getFullYear();
          const mm = String(returnDate.getMonth() + 1).padStart(2, '0');
          const dd = String(returnDate.getDate()).padStart(2, '0');

          dataRestituzione.value = `${yyyy}-${mm}-${dd}`;
        });
      }

      // Mostra il tab attivo all'avvio (prestiti-attivi)
      showTab('prestiti-attivi');
    });
  </script>
</body>

</html>