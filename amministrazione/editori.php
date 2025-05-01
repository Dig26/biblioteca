<?php
include '../php/config.php';

// Recupera gli editori
$editori = [];
$sql = "SELECT * FROM editore ORDER BY nome";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $editori[] = $row;
    }
}

// Gestione del form per nuovo editore
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nome = $conn->real_escape_string($_POST['nome']);
    
    $sql_insert = "INSERT INTO editore (nome) VALUES ('$nome')";
    if ($conn->query($sql_insert) === TRUE) {
        $message = "Editore inserito con successo.";
        // Ricarica la pagina per aggiornare l'elenco
        header("Location: editori.php?success=1");
        exit;
    } else {
        $message = "Errore durante l'inserimento: " . $conn->error;
    }
}

// Gestione del form per la modifica dell'editore
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit') {
    $id_editore = (int)$_POST['id_editore'];
    $nome = $conn->real_escape_string($_POST['nome']);

    $sql_update = "UPDATE editore SET nome = '$nome' WHERE id_editore = $id_editore";
    if ($conn->query($sql_update) === TRUE) {
        $message = "Editore aggiornato con successo.";
        // Ricarica la pagina per aggiornare l'elenco
        header("Location: editori.php?success=1");
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
  <title>BiblioTech - Gestione Editori</title>
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
        <a href="editori.php" class="nav-item active">
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
        <h1 class="title">Gestione Editori</h1>
        <div class="search-bar">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input type="text" placeholder="Cerca editore..." id="search-editori">
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <div class="tab active" data-tab="lista-editori">Lista Editori</div>
        <div class="tab" data-tab="nuovo-editore">Nuovo Editore</div>
      </div>

      <?php if ($message): ?>
        <div class="message" style="background-color: #dff0d8; color: #3c763d; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <!-- New publisher form -->
      <div class="form-section tab-content" id="nuovo-editore-content" style="display: none;">
        <h2 class="form-title">Inserisci Nuovo Editore</h2>
        <form method="post" action="editori.php">
          <input type="hidden" name="action" value="add">
          <div class="form-group">
            <label class="form-label" for="nome">Nome Editore</label>
            <input type="text" class="form-input" id="nome" name="nome" required>
          </div>

          <div class="form-actions">
            <button type="button" class="btn btn-secondary" id="annulla-editore">Annulla</button>
            <button type="submit" class="btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              Inserisci Editore
            </button>
          </div>
        </form>
      </div>

      <!-- Publishers table -->
      <div class="table-section tab-content" id="lista-editori-content">
        <div class="table-header">
          <h2 class="table-title">Editori Registrati</h2>
          <button class="btn btn-primary" id="btn-nuovo-editore">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuovo Editore
          </button>
        </div>

        <?php if (count($editori) > 0): ?>
        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Azioni</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($editori as $editore): ?>
              <tr>
                <td><?php echo $editore['id_editore']; ?></td>
                <td><?php echo htmlspecialchars($editore['nome']); ?></td>
                <td>
                  <div class="table-actions">
                    <button class="btn btn-sm btn-primary edit-editore" 
                            data-id="<?php echo $editore['id_editore']; ?>" 
                            data-nome="<?php echo htmlspecialchars($editore['nome']); ?>">Modifica</button>
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
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
          </svg>
          <div class="empty-state-title">Nessun editore registrato</div>
          <p class="empty-state-text">
            Non ci sono editori registrati al momento. Utilizza il form "Nuovo Editore" per aggiungere un editore.
          </p>
          <button class="btn btn-primary" id="show-nuovo-editore">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Aggiungi Editore
          </button>
        </div>
        <?php endif; ?>
      </div>

      <!-- Edit form (hidden initially) -->
      <div id="edit-editore-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background-color: white; padding: 2rem; border-radius: 8px; width: 100%; max-width: 600px;">
          <h2 class="form-title">Modifica Editore</h2>
          <form method="post" action="editori.php" id="edit-editore-form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id_editore" id="edit-id-editore">
            
            <div class="form-group">
              <label class="form-label" for="edit-nome">Nome Editore</label>
              <input type="text" class="form-input" id="edit-nome" name="nome" required>
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
      
      // Bottoni per mostrare il tab "nuovo-editore"
      const btnShowNuovoEditore = document.getElementById('show-nuovo-editore');
      const btnNuovoEditore = document.getElementById('btn-nuovo-editore');
      
      if (btnShowNuovoEditore) {
        btnShowNuovoEditore.addEventListener('click', function() {
          showTab('nuovo-editore');
        });
      }
      
      if (btnNuovoEditore) {
        btnNuovoEditore.addEventListener('click', function() {
          showTab('nuovo-editore');
        });
      }
      
      // Bottone per annullare il form
      const btnAnnullaEditore = document.getElementById('annulla-editore');
      if (btnAnnullaEditore) {
        btnAnnullaEditore.addEventListener('click', function() {
          showTab('lista-editori');
        });
      }
      
      // Filtraggio editori
      const searchInput = document.getElementById('search-editori');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const rows = document.querySelectorAll('tbody tr');
          
          rows.forEach(row => {
            const nome = row.cells[1].textContent.toLowerCase();
            
            if (nome.includes(searchTerm)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }
      
      // Edit modal functionality
      const editButtons = document.querySelectorAll('.edit-editore');
      const editModal = document.getElementById('edit-editore-modal');
      const cancelEdit = document.getElementById('cancel-edit');
      
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const nome = this.getAttribute('data-nome');
          
          document.getElementById('edit-id-editore').value = id;
          document.getElementById('edit-nome').value = nome;
          
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
      
      // Mostra il tab attivo all'avvio (lista-editori)
      showTab('lista-editori');
    });
  </script>
</body>