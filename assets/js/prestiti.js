/**
 * BiblioTech - Script per i prestiti
 * Gestisce le funzionalità specifiche della pagina prestiti
 */

document.addEventListener('DOMContentLoaded', function() {
    // Calcolo automatico della data di restituzione
    const setupDateCalculation = () => {
      const loanDateInput = document.getElementById('data_prestito');
      const returnDateInput = document.getElementById('data_restituzione');
      
      if (loanDateInput && returnDateInput) {
        loanDateInput.addEventListener('change', function() {
          // Calcola la data di restituzione (30 giorni dopo)
          const loanDate = new Date(this.value);
          const returnDate = new Date(loanDate);
          returnDate.setDate(returnDate.getDate() + 30);
          
          // Formatta la data nel formato yyyy-mm-dd
          const yyyy = returnDate.getFullYear();
          const mm = String(returnDate.getMonth() + 1).padStart(2, '0');
          const dd = String(returnDate.getDate()).padStart(2, '0');
          
          returnDateInput.value = `${yyyy}-${mm}-${dd}`;
        });
      }
    };
  
    // Conferma restituzione
    const setupReturnConfirmation = () => {
      const returnButtons = document.querySelectorAll('a[href^="php/restituisci_prestito.php"]');
      
      returnButtons.forEach(button => {
        button.addEventListener('click', function(e) {
          if (!confirm('Confermare la restituzione di questo libro?')) {
            e.preventDefault();
          }
        });
      });
    };
  
    // Filtraggio per stato
    const setupStatusFilter = () => {
      const statusFilter = document.getElementById('filter-stato');
      const rows = document.querySelectorAll('tbody tr');
      
      if (statusFilter && rows.length) {
        statusFilter.addEventListener('change', function() {
          const selectedStatus = this.value;
          
          rows.forEach(row => {
            const statusCell = row.querySelector('.status');
            
            if (!statusCell) return;
            
            if (selectedStatus === 'all' || 
                (selectedStatus === 'active' && statusCell.classList.contains('status-active')) ||
                (selectedStatus === 'due-soon' && statusCell.classList.contains('status-due-soon')) ||
                (selectedStatus === 'overdue' && statusCell.classList.contains('status-overdue'))) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }
    };
  
    // Ricerca prestiti
    const setupLoanSearch = () => {
      const searchInput = document.getElementById('search-prestiti');
      const rows = document.querySelectorAll('tbody tr');
      
      if (searchInput && rows.length) {
        searchInput.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          
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
    };
  
    // Inizializza tutte le funzioni
    setupDateCalculation();
    setupReturnConfirmation();
    setupStatusFilter();
    setupLoanSearch();
  });