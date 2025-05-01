<?php
include '../php/config.php';

// Recupera le opere
$opere = [];
$sql_opere = "SELECT * FROM opera ORDER BY titolo";
$result_opere = $conn->query($sql_opere);
if ($result_opere && $result_opere->num_rows > 0) {
  while ($row = $result_opere->fetch_assoc()) {
    $opere[] = $row;
  }
}

// Recupera le collane
$collane = [];
$sql_collane = "SELECT c.*, e.nome as editore_nome 
               FROM collana c
               INNER JOIN editore e ON c.id_editore = e.id_editore
               ORDER BY c.nome";
$result_collane = $conn->query($sql_collane);
if ($result_collane && $result_collane->num_rows > 0) {
  while ($row = $result_collane->fetch_assoc()) {
    $collane[] = $row;
  }
}

// Recupera le edizioni
$edizioni = [];
$sql_edizioni = "SELECT ed.*, o.titolo, o.autore, c.nome as collana_nome, e.nome as editore_nome 
                FROM edizione ed
                INNER JOIN opera o ON ed.id_opera = o.id_opera
                INNER JOIN collana c ON ed.id_collana = c.id_collana
                INNER JOIN editore e ON c.id_editore = e.id_editore
                ORDER BY o.titolo, ed.anno DESC";
$result_edizioni = $conn->query($sql_edizioni);
if ($result_edizioni && $result_edizioni->num_rows > 0) {
  while ($row = $result_edizioni->fetch_assoc()) {
    $edizioni[] = $row;
  }
}

// Gestione del form per nuova edizione
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
  $id_opera = (int)$_POST['id_opera'];
  $id_collana = (int)$_POST['id_collana'];
  $anno = (int)$_POST['anno'];

  $sql_insert = "INSERT INTO edizione (id_opera, id_collana, anno) VALUES ($id_opera, $id_collana, $anno)";
  if ($conn->query($sql_insert) === TRUE) {
    $message = "Edizione inserita con successo.";
    // Ricarica la pagina per aggiornare l'elenco
    header("Location: edizioni.php?success=1");
    exit;
  } else {
    $message = "Errore durante l'inserimento: " . $conn->error;
  }
}

// Gestione della modifica edizione
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
  $id_edizione = (int)$_POST['id_edizione'];
  $id_opera = (int)$_POST['id_opera'];
  $id_collana = (int)$_POST['id_collana'];
  $anno = (int)$_POST['anno'];

  $sql_update = "UPDATE edizione SET id_opera = $id_opera, id_collana = $id_collana, anno = $anno WHERE id_edizione = $id_edizione";
  if ($conn->query($sql_update) === TRUE) {
    $message = "Edizione aggiornata con successo.";
    header("Location: edizioni.php?success=1");
    exit;
  } else {
    $message = "Errore durante l'aggiornamento: " . $conn->error;
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
  <title>BiblioTech - Gestione Edizioni</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/guide.css">
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
        <a href="edizioni.php" class="nav-item active">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
          </svg>
          Edizioni
        </a>
        <a href="rapporti.php" class="nav-item">
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
        <h1 class="title">Gestione Edizioni</h1>
        <div class="search-bar">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input type="text" placeholder="Cerca edizione..." id="search-edizioni">
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <div class="tab active" data-tab="lista-edizioni">Lista Edizioni</div>
        <div class="tab" data-tab="nuova-edizione">Nuova Edizione</div>
        <div class="tab" data-tab="gestione-opere">Gestione Opere</div>
      </div>

      <?php if ($message): ?>
        <div class="message" style="background-color: #dff0d8; color: #3c763d; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <!-- New edition form -->
      <div class="form-section tab-content" id="nuova-edizione-content" style="display: none;">
        <h2 class="form-title">Inserisci Nuova Edizione</h2>
        <form method="post" action="edizioni.php">
          <input type="hidden" name="action" value="add">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="id_opera">Opera</label>
              <select class="form-select" id="id_opera" name="id_opera" required>
                <option value="">Seleziona Opera</option>
                <?php foreach ($opere as $opera): ?>
                  <option value="<?php echo $opera['id_opera']; ?>"><?php echo htmlspecialchars($opera['titolo']); ?> (<?php echo htmlspecialchars($opera['autore']); ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="id_collana">Collana</label>
              <select class="form-select" id="id_collana" name="id_collana" required>
                <option value="">Seleziona Collana</option>
                <?php foreach ($collane as $collana): ?>
                  <option value="<?php echo $collana['id_collana']; ?>"><?php echo htmlspecialchars($collana['nome']); ?> (<?php echo htmlspecialchars($collana['editore_nome']); ?>)</option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="anno">Anno</label>
              <input type="number" class="form-input" id="anno" name="anno" min="1000" max="<?php echo date('Y'); ?>" required>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="annulla-edizione">Annulla</button>
            <button type="submit" class="btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              Inserisci Edizione
            </button>
          </div>
        </form>
      </div>

      <!-- Editions table -->
      <div class="table-section tab-content" id="lista-edizioni-content">
        <div class="table-header">
          <h2 class="table-title">Edizioni Registrate</h2>
          <button class="btn btn-primary" id="btn-nuova-edizione">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuova Edizione
          </button>
        </div>

        <?php if (count($edizioni) > 0): ?>
          <div class="table-container">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Opera</th>
                  <th>Autore</th>
                  <th>Collana</th>
                  <th>Editore</th>
                  <th>Anno</th>
                  <th>Azioni</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($edizioni as $edizione): ?>
                  <tr>
                    <td><?php echo $edizione['id_edizione']; ?></td>
                    <td><?php echo htmlspecialchars($edizione['titolo']); ?></td>
                    <td><?php echo htmlspecialchars($edizione['autore']); ?></td>
                    <td><?php echo htmlspecialchars($edizione['collana_nome']); ?></td>
                    <td><?php echo htmlspecialchars($edizione['editore_nome']); ?></td>
                    <td><?php echo $edizione['anno']; ?></td>
                    <td>
                      <div class="table-actions">
                        <button class="btn btn-sm btn-primary edit-edizione"
                          data-id="<?php echo $edizione['id_edizione']; ?>"
                          data-opera="<?php echo $edizione['id_opera']; ?>"
                          data-collana="<?php echo $edizione['id_collana']; ?>"
                          data-anno="<?php echo $edizione['anno']; ?>">
                          Modifica
                        </button>
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
            </svg>
            <div class="empty-state-title">Nessuna edizione registrata</div>
            <p class="empty-state-text">
              Non ci sono edizioni registrate al momento. Utilizza il form "Nuova Edizione" per aggiungere un'edizione.
            </p>
            <button class="btn btn-primary" id="show-nuova-edizione">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              Aggiungi Edizione
            </button>
          </div>
        <?php endif; ?>
      </div>

      <!-- Edit form (hidden initially) -->
      <div id="edit-edizione-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background-color: white; padding: 2rem; border-radius: 8px; width: 100%; max-width: 600px;">
          <h2 class="form-title">Modifica Edizione</h2>
          <form method="post" action="edizioni.php" id="edit-edizione-form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_edizione" id="edit-id-edizione">

            <div class="form-grid">
              <div class="form-group">
                <label class="form-label" for="edit-id-opera">Opera</label>
                <select class="form-select" id="edit-id-opera" name="id_opera" required>
                  <option value="">Seleziona Opera</option>
                  <?php foreach ($opere as $opera): ?>
                    <option value="<?php echo $opera['id_opera']; ?>"><?php echo htmlspecialchars($opera['titolo']); ?> (<?php echo htmlspecialchars($opera['autore']); ?>)</option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label" for="edit-id-collana">Collana</label>
                <select class="form-select" id="edit-id-collana" name="id_collana" required>
                  <option value="">Seleziona Collana</option>
                  <?php foreach ($collane as $collana): ?>
                    <option value="<?php echo $collana['id_collana']; ?>"><?php echo htmlspecialchars($collana['nome']); ?> (<?php echo htmlspecialchars($collana['editore_nome']); ?>)</option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label" for="edit-anno">Anno</label>
                <input type="number" class="form-input" id="edit-anno" name="anno" min="1000" max="<?php echo date('Y'); ?>" required>
              </div>
            </div>

            <div class="form-actions">
              <button type="button" class="btn btn-secondary" id="cancel-edit">Annulla</button>
              <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                  <polyline points="17 21 17 13 7 13 7 21"></polyline>
                  <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Salva Modifiche
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Gestione Opere tab content -->
      <div class="form-section tab-content" id="gestione-opere-content" style="display: none;">
        <h2 class="form-title">Gestione Opere</h2>

        <!-- Add new opera form -->
        <div style="margin-bottom: 2rem;">
          <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Aggiungi Nuova Opera</h3>
          <form method="post" action="../php/inserisci_opera.php?from_edizioni=1">
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label" for="titolo">Titolo</label>
                <input type="text" class="form-input" id="titolo" name="titolo" required>
              </div>

              <div class="form-group">
                <label class="form-label" for="autore">Autore</label>
                <input type="text" class="form-input" id="autore" name="autore" required>
              </div>

              <div class="form-group">
                <label class="form-label" for="anno_prima_pub">Anno Prima Pubblicazione</label>
                <input type="number" class="form-input" id="anno_prima_pub" name="anno_prima_pub" min="1000" max="<?php echo date('Y'); ?>" required>
              </div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Aggiungi Opera
              </button>
            </div>
          </form>
        </div>

        <!-- List opere -->
        <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Opere Esistenti</h3>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Titolo</th>
                <th>Autore</th>
                <th>Anno Prima Pub.</th>
                <th>Azioni</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($opere as $opera): ?>
                <tr>
                  <td><?php echo $opera['id_opera']; ?></td>
                  <td><?php echo htmlspecialchars($opera['titolo']); ?></td>
                  <td><?php echo htmlspecialchars($opera['autore']); ?></td>
                  <td><?php echo $opera['anno_prima_pub']; ?></td>
                  <td>
                    <a href="../opera.php?id=<?php echo $opera['id_opera']; ?>" class="btn btn-sm btn-secondary">Visualizza</a>
                    <a href="../php/modifica_opera.php?id=<?php echo $opera['id_opera']; ?>&from_edizioni=1" class="btn btn-sm btn-primary">Modifica</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <script src="../assets/js/main.js"></script>
  <script src="../assets/js/guide.js"></script>
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

      // Bottoni per mostrare il tab "nuova-edizione"
      const btnShowNuovaEdizione = document.getElementById('show-nuova-edizione');
      const btnNuovaEdizione = document.getElementById('btn-nuova-edizione');

      if (btnShowNuovaEdizione) {
        btnShowNuovaEdizione.addEventListener('click', function() {
          showTab('nuova-edizione');
        });
      }

      if (btnNuovaEdizione) {
        btnNuovaEdizione.addEventListener('click', function() {
          showTab('nuova-edizione');
        });
      }

      // Bottone per annullare il form
      const btnAnnullaEdizione = document.getElementById('annulla-edizione');
      if (btnAnnullaEdizione) {
        btnAnnullaEdizione.addEventListener('click', function() {
          showTab('lista-edizioni');
        });
      }

      // Filtraggio edizioni
      const searchInput = document.getElementById('search-edizioni');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const rows = document.querySelectorAll('tbody tr');

          rows.forEach(row => {
            if (row.cells.length < 6) return; // Skip if not enough cells

            const opera = row.cells[1].textContent.toLowerCase();
            const autore = row.cells[2].textContent.toLowerCase();
            const collana = row.cells[3].textContent.toLowerCase();
            const editore = row.cells[4].textContent.toLowerCase();

            if (opera.includes(searchTerm) || autore.includes(searchTerm) ||
              collana.includes(searchTerm) || editore.includes(searchTerm)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }

      // Edit modal functionality
      const editButtons = document.querySelectorAll('.edit-edizione');
      const editModal = document.getElementById('edit-edizione-modal');
      const cancelEdit = document.getElementById('cancel-edit');

      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const opera = this.getAttribute('data-opera');
          const collana = this.getAttribute('data-collana');
          const anno = this.getAttribute('data-anno');

          document.getElementById('edit-id-edizione').value = id;
          document.getElementById('edit-id-opera').value = opera;
          document.getElementById('edit-id-collana').value = collana;
          document.getElementById('edit-anno').value = anno;

          editModal.style.display = 'flex';
        });
      });

      if (cancelEdit) {
        cancelEdit.addEventListener('click', function() {
          editModal.style.display = 'none';
        });
      }

      // If we click outside the modal, close it
      window.addEventListener('click', function(event) {
        if (event.target === editModal) {
          editModal.style.display = 'none';
        }
      });

      // Mostra il tab attivo all'avvio (lista-edizioni)
      showTab('lista-edizioni');
    });
  </script>
</body>

</html>