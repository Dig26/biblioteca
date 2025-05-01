<?php
include 'php/config.php';

// Recupera gli utenti
$utenti = [];
$sql = "SELECT * FROM utente ORDER BY cognome, nome";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $utenti[] = $row;
    }
}

// Conta i prestiti per utente
$prestitiPerUtente = [];
$sql_prestiti = "SELECT id_utente, COUNT(*) as total FROM prestito GROUP BY id_utente";
$result_prestiti = $conn->query($sql_prestiti);
if ($result_prestiti && $result_prestiti->num_rows > 0) {
    while ($row = $result_prestiti->fetch_assoc()) {
        $prestitiPerUtente[$row['id_utente']] = $row['total'];
    }
}

// Gestione del form per nuovo utente
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nome = $conn->real_escape_string($_POST['nome']);
    $cognome = $conn->real_escape_string($_POST['cognome']);
    $telefono = $conn->real_escape_string($_POST['telefono']);

    $sql_insert = "INSERT INTO utente (nome, cognome, telefono) VALUES ('$nome', '$cognome', '$telefono')";
    if ($conn->query($sql_insert) === TRUE) {
        $message = "Utente inserito con successo.";
        // Ricarica la pagina per aggiornare l'elenco
        header("Location: utenti.php?success=1");
        exit;
    } else {
        $message = "Errore durante l'inserimento: " . $conn->error;
    }
}

// Gestione del form per la modifica dell'utente
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id_utente = (int)$_POST['id_utente'];
    $nome = $conn->real_escape_string($_POST['nome']);
    $cognome = $conn->real_escape_string($_POST['cognome']);
    $telefono = $conn->real_escape_string($_POST['telefono']);

    $sql_update = "UPDATE utente SET nome = '$nome', cognome = '$cognome', telefono = '$telefono' WHERE id_utente = $id_utente";
    if ($conn->query($sql_update) === TRUE) {
        $message = "Utente aggiornato con successo.";
        // Ricarica la pagina per aggiornare l'elenco
        header("Location: utenti.php?success=1");
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
  <title>BiblioTech - Gestione Utenti</title>
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
        <a href="prestiti.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 6.1H3"></path>
            <path d="M21 12.1H3"></path>
            <path d="M15.1 18H3"></path>
          </svg>
          Prestiti
        </a>
        <a href="utenti.php" class="nav-item active">
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
        <h1 class="title">Gestione Utenti</h1>
        <div class="search-bar">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input type="text" placeholder="Cerca utente..." id="search-utenti">
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <div class="tab active" data-tab="lista-utenti">Lista Utenti</div>
        <div class="tab" data-tab="nuovo-utente">Nuovo Utente</div>
      </div>

      <?php if ($message): ?>
        <div class="message" style="background-color: #dff0d8; color: #3c763d; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <!-- New user form -->
      <div class="form-section tab-content" id="nuovo-utente-content" style="display: none;">
        <h2 class="form-title">Inserisci Nuovo Utente</h2>
        <form method="post" action="utenti.php">
          <input type="hidden" name="action" value="add">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="nome">Nome</label>
              <input type="text" class="form-input" id="nome" name="nome" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="cognome">Cognome</label>
              <input type="text" class="form-input" id="cognome" name="cognome" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="telefono">Telefono</label>
              <input type="text" class="form-input" id="telefono" name="telefono" required>
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="annulla-utente">Annulla</button>
            <button type="submit" class="btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              Inserisci Utente
            </button>
          </div>
        </form>
      </div>

      <!-- Users table -->
      <div class="table-section tab-content" id="lista-utenti-content">
        <div class="table-header">
          <h2 class="table-title">Utenti Registrati</h2>
          <button class="btn btn-primary" id="btn-nuovo-utente">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuovo Utente
          </button>
        </div>

        <?php if (count($utenti) > 0): ?>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Cognome</th>
                <th>Nome</th>
                <th>Telefono</th>
                <th>Prestiti</th>
                <th>Azioni</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($utenti as $utente): ?>
              <tr>
                <td><?php echo $utente['id_utente']; ?></td>
                <td><?php echo htmlspecialchars($utente['cognome']); ?></td>
                <td><?php echo htmlspecialchars($utente['nome']); ?></td>
                <td><?php echo htmlspecialchars($utente['telefono']); ?></td>
                <td><?php echo isset($prestitiPerUtente[$utente['id_utente']]) ? $prestitiPerUtente[$utente['id_utente']] : 0; ?></td>
                <td>
                  <div class="table-actions">
                    <a href="prestiti.php?utente_id=<?php echo $utente['id_utente']; ?>" class="btn btn-sm btn-secondary">Prestiti</a>
                    <button class="btn btn-sm btn-primary edit-utente" 
                            data-id="<?php echo $utente['id_utente']; ?>"
                            data-nome="<?php echo htmlspecialchars($utente['nome']); ?>"
                            data-cognome="<?php echo htmlspecialchars($utente['cognome']); ?>"
                            data-telefono="<?php echo htmlspecialchars($utente['telefono']); ?>">
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
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          <div class="empty-state-title">Nessun utente registrato</div>
          <p class="empty-state-text">
            Non ci sono utenti registrati al momento. Utilizza il form "Nuovo Utente" per aggiungere utenti.
          </p>
          <button class="btn btn-primary" id="show-nuovo-utente">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Aggiungi Utente
          </button>
        </div>
        <?php endif; ?>
      </div>

      <!-- Edit form (hidden initially) -->
      <div id="edit-utente-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background-color: white; padding: 2rem; border-radius: 8px; width: 100%; max-width: 600px;">
          <h2 class="form-title">Modifica Utente</h2>
          <form method="post" action="utenti.php" id="edit-utente-form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_utente" id="edit-id-utente">
            
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label" for="edit-nome">Nome</label>
                <input type="text" class="form-input" id="edit-nome" name="nome" required>
              </div>

              <div class="form-group">
                <label class="form-label" for="edit-cognome">Cognome</label>
                <input type="text" class="form-input" id="edit-cognome" name="cognome" required>
              </div>

              <div class="form-group">
                <label class="form-label" for="edit-telefono">Telefono</label>
                <input type="text" class="form-input" id="edit-telefono" name="telefono" required>
              </div>
            </div>

            <div class="form-actions">
              <button type="button" class="btn btn-secondary" id="cancel-edit-utente">Annulla</button>
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
      
      // Bottoni per mostrare il tab "nuovo-utente"
      const btnShowNuovoUtente = document.getElementById('show-nuovo-utente');
      const btnNuovoUtente = document.getElementById('btn-nuovo-utente');
      
      if (btnShowNuovoUtente) {
        btnShowNuovoUtente.addEventListener('click', function() {
          showTab('nuovo-utente');
        });
      }
      
      if (btnNuovoUtente) {
        btnNuovoUtente.addEventListener('click', function() {
          showTab('nuovo-utente');
        });
      }
      
      // Bottone per annullare il form
      const btnAnnullaUtente = document.getElementById('annulla-utente');
      if (btnAnnullaUtente) {
        btnAnnullaUtente.addEventListener('click', function() {
          showTab('lista-utenti');
        });
      }
      
      // Filtraggio utenti
      const searchInput = document.getElementById('search-utenti');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const rows = document.querySelectorAll('tbody tr');
          
          rows.forEach(row => {
            const nome = row.cells[2].textContent.toLowerCase();
            const cognome = row.cells[1].textContent.toLowerCase();
            const telefono = row.cells[3].textContent.toLowerCase();
            
            if (nome.includes(searchTerm) || cognome.includes(searchTerm) || telefono.includes(searchTerm)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }
      
      // Edit modal functionality
      const editButtons = document.querySelectorAll('.edit-utente');
      const editModal = document.getElementById('edit-utente-modal');
      const cancelEdit = document.getElementById('cancel-edit-utente');
      
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const nome = this.getAttribute('data-nome');
          const cognome = this.getAttribute('data-cognome');
          const telefono = this.getAttribute('data-telefono');
          
          document.getElementById('edit-id-utente').value = id;
          document.getElementById('edit-nome').value = nome;
          document.getElementById('edit-cognome').value = cognome;
          document.getElementById('edit-telefono').value = telefono;
          
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
      
      // Mostra il tab attivo all'avvio (lista-utenti)
      showTab('lista-utenti');
    });
  </script>
</body>
</html>