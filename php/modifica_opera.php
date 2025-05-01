<?php
include 'config.php';

$id_opera = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$opera = null;
$message = "";

// Recupera i dati dell'opera
if ($id_opera > 0) {
    $sql = "SELECT * FROM opera WHERE id_opera = $id_opera";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $opera = $result->fetch_assoc();
    } else {
        // Opera non trovata
        header("Location: ../index.php");
        exit;
    }
} else {
    // ID non valido
    header("Location: ../index.php");
    exit;
}

// Gestione del form di modifica
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titolo = $conn->real_escape_string($_POST['titolo']);
    $autore = $conn->real_escape_string($_POST['autore']);
    $anno_prima_pub = (int) $_POST['anno_prima_pub'];
    
    $sql = "UPDATE opera SET titolo = '$titolo', autore = '$autore', anno_prima_pub = $anno_prima_pub WHERE id_opera = $id_opera";
    if ($conn->query($sql) === TRUE) {
        $message = "Opera aggiornata con successo.";
        $opera['titolo'] = $titolo;
        $opera['autore'] = $autore;
        $opera['anno_prima_pub'] = $anno_prima_pub;
        
        // Se la modifica è avvenuta dalla pagina amministrazione/edizioni.php, ritorna a quella pagina
        if (isset($_POST['from_edizioni']) && $_POST['from_edizioni'] == 1) {
            header("Location: ../amministrazione/edizioni.php?success=1");
            exit;
        }
    } else {
        $message = "Errore durante l'aggiornamento: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BiblioTech - Modifica Opera</title>
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
        <a href="../amministrazione/editori.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
          </svg>
          Editori
        </a>
        <a href="../amministrazione/collane.php" class="nav-item">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"></path>
            <line x1="16" y1="8" x2="2" y2="22"></line>
            <line x1="17.5" y1="15" x2="9" y2="15"></line>
          </svg>
          Collane
        </a>
        <a href="../amministrazione/edizioni.php" class="nav-item active">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
          </svg>
          Edizioni
        </a>
        <a href="../amministrazione/rapporti.php" class="nav-item">
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
        <h1 class="title">Modifica Opera</h1>
      </div>

      <?php if ($message): ?>
        <div class="message" style="background-color: #dff0d8; color: #3c763d; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
          <?php echo $message; ?>
        </div>
      <?php endif; ?>

      <div class="section">
        <div class="form-section">
          <form method="post" action="modifica_opera.php?id=<?php echo $id_opera; ?>">
            <input type="hidden" name="from_edizioni" value="<?php echo isset($_GET['from_edizioni']) ? '1' : '0'; ?>">
            
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label" for="titolo">Titolo</label>
                <input type="text" class="form-input" id="titolo" name="titolo" value="<?php echo htmlspecialchars($opera['titolo']); ?>" required>
              </div>
              
              <div class="form-group">
                <label class="form-label" for="autore">Autore</label>
                <input type="text" class="form-input" id="autore" name="autore" value="<?php echo htmlspecialchars($opera['autore']); ?>" required>
              </div>
              
              <div class="form-group">
                <label class="form-label" for="anno_prima_pub">Anno Prima Pubblicazione</label>
                <input type="number" class="form-input" id="anno_prima_pub" name="anno_prima_pub" value="<?php echo $opera['anno_prima_pub']; ?>" min="1000" max="<?php echo date('Y'); ?>" required>
              </div>
            </div>
            
            <div class="form-actions">
              <a href="<?php echo isset($_GET['from_edizioni']) ? '../amministrazione/edizioni.php' : '../opera.php?id=' . $id_opera; ?>" class="btn btn-secondary">
                Annulla
              </a>
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
</body>
</html>