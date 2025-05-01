/**
 * BiblioTech - Script per il catalogo
 * Gestisce le funzionalità specifiche della pagina catalogo
 */

document.addEventListener('DOMContentLoaded', function() {
    // Toggle vista griglia/lista
    const setupViewToggle = () => {
      const gridViewBtn = document.getElementById('grid-view');
      const listViewBtn = document.getElementById('list-view');
      const booksContainer = document.getElementById('books-container');
      
      if (gridViewBtn && listViewBtn && booksContainer) {
        // Vista a griglia
        gridViewBtn.addEventListener('click', function() {
          booksContainer.classList.remove('list-view');
          booksContainer.classList.add('grid-view');
          gridViewBtn.classList.add('active');
          listViewBtn.classList.remove('active');
          
          // Salva preferenza
          localStorage.setItem('catalogView', 'grid');
        });
        
        // Vista a lista
        listViewBtn.addEventListener('click', function() {
          booksContainer.classList.remove('grid-view');
          booksContainer.classList.add('list-view');
          listViewBtn.classList.add('active');
          gridViewBtn.classList.remove('active');
          
          // Salva preferenza
          localStorage.setItem('catalogView', 'list');
        });
        
        // Carica preferenza salvata
        const savedView = localStorage.getItem('catalogView');
        if (savedView === 'list') {
          listViewBtn.click();
        }
      }
    };
  
    // Filtraggio in tempo reale
    const setupRealTimeSearch = () => {
      const searchInput = document.getElementById('search-input');
      const bookCards = document.querySelectorAll('.book-card');
      
      if (searchInput && bookCards.length) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          
          bookCards.forEach(card => {
            const title = card.querySelector('.book-title').textContent.toLowerCase();
            const author = card.querySelector('.book-author').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || author.includes(searchTerm)) {
              card.style.display = '';
            } else {
              card.style.display = 'none';
            }
          });
        });
      }
    };
  
    // Reset filtri
    const setupFilterReset = () => {
      const resetBtn = document.getElementById('reset-filters');
      const filterForm = document.getElementById('filter-form');
      
      if (resetBtn && filterForm) {
        resetBtn.addEventListener('click', function() {
          filterForm.reset();
          // Opzionale: invia il form vuoto per aggiornare i risultati
          // filterForm.submit();
        });
      }
    };
  
    // Inizializza tutte le funzioni
    setupViewToggle();
    setupRealTimeSearch();
    setupFilterReset();
  });