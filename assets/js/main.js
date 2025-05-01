/**
 * BiblioTech - Script principale
 */
document.addEventListener("DOMContentLoaded", function() {
    // Gestione Tab per tutte le pagine
    const tabs = document.querySelectorAll(".tab");
    if (tabs.length > 0) {
        const tabContents = document.querySelectorAll(".tab-content");
        
        // Funzione per mostrare il tab selezionato
        function showTab(tabId) {
            tabContents.forEach(content => {
                content.style.display = "none";
            });
            
            tabs.forEach(tab => {
                tab.classList.remove("active");
            });
            
            const contentToShow = document.getElementById(tabId + "-content");
            if (contentToShow) {
                contentToShow.style.display = "block";
                document.querySelector(`[data-tab="${tabId}"]`).classList.add("active");
            }
        }
        
        // Assegna eventi click ai tab
        tabs.forEach(tab => {
            tab.addEventListener("click", function() {
                showTab(this.getAttribute("data-tab"));
            });
        });
        
        // Mostra il primo tab come default (se esiste)
        if (tabs[0]) {
            const firstTabId = tabs[0].getAttribute("data-tab");
            showTab(firstTabId);
        }
    }
    
    // Gestione ricerche in tempo reale
    const searchInputs = document.querySelectorAll("input[type='text'][placeholder*='Cerca']");
    searchInputs.forEach(input => {
        input.addEventListener("input", function() {
            const searchTerm = this.value.toLowerCase();
            const table = this.closest(".section, .table-section").querySelector("table");
            
            if (table) {
                const rows = table.querySelectorAll("tbody tr");
                rows.forEach(row => {
                    let found = false;
                    const cells = row.querySelectorAll("td");
                    
                    cells.forEach(cell => {
                        if (cell.textContent.toLowerCase().includes(searchTerm)) {
                            found = true;
                        }
                    });
                    
                    row.style.display = found ? "" : "none";
                });
            }
        });
    });
    
    // Bottoni per nuovi elementi
    const btnNuovo = document.querySelectorAll("[id^='btn-nuovo-'], [id^='show-nuovo-']");
    btnNuovo.forEach(btn => {
        btn.addEventListener("click", function() {
            const type = this.id.replace("btn-nuovo-", "").replace("show-nuovo-", "");
            const tabToShow = document.querySelector(`[data-tab="nuovo-${type}"]`);
            if (tabToShow) {
                tabToShow.click();
            }
        });
    });
    
    // Bottoni per annullare
    const btnAnnulla = document.querySelectorAll("[id^='annulla-']");
    btnAnnulla.forEach(btn => {
        btn.addEventListener("click", function() {
            const type = this.id.replace("annulla-", "");
            const tabToShow = document.querySelector(`[data-tab="lista-${type}s"], [data-tab="lista-${type}"]`);
            if (tabToShow) {
                tabToShow.click();
            }
        });
    });
    
    // Conferma per eliminazioni
    const deleteButtons = document.querySelectorAll("a[href*='elimina'], a[href*='delete']");
    deleteButtons.forEach(btn => {
        btn.addEventListener("click", function(e) {
            if (!confirm("Sei sicuro di voler eliminare questo elemento?")) {
                e.preventDefault();
            }
        });
    });
});