/**
 * BiblioTech - Fix completo per la guida interattiva
 * Risolve il problema del bordo blu che rimane sulla barra superiore
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inizia il miglioramento dopo un breve ritardo
    setTimeout(function() {
      // Sostituisci la funzione originale di highlighting
      if (window.biblioGuide) {
        // Salva un riferimento alla funzione originale
        const originalHighlightElement = window.biblioGuide.highlightElement;
        const originalShowCurrentStep = window.biblioGuide.showCurrentStep;
        
        // Sostituisci completamente la funzione di highlighting
        window.biblioGuide.highlightElement = function(element) {
          // Prima rimuovi qualsiasi highlight precedente
          const prevHighlights = document.querySelectorAll('.guide-highlight');
          prevHighlights.forEach(el => {
            el.classList.remove('guide-highlight');
            el.style.boxShadow = '';
            el.style.position = '';
            el.style.zIndex = '';
          });
          
          // Poi cerca il contenuto corretto da evidenziare
          let targetElement = element;
          
          // Se l'elemento è un tab, trova il contenuto associato
          if (element.classList.contains('tab') || element.parentElement.classList.contains('tabs')) {
            const tabId = element.getAttribute('data-tab');
            if (tabId) {
              const tabContent = document.getElementById(tabId + '-content');
              if (tabContent) {
                // Cerca una tabella all'interno del tab content
                const tableContainer = tabContent.querySelector('.table-container');
                if (tableContainer) {
                  targetElement = tableContainer;
                } else {
                  // Se non c'è una tabella, usa tutto il contenuto del tab
                  targetElement = tabContent;
                }
              }
            }
          }
          
          // Applica l'highlight al nuovo elemento target
          if (targetElement) {
            targetElement.classList.add('guide-highlight');
            targetElement.style.position = 'relative';
            targetElement.style.zIndex = '9002';
            targetElement.style.boxShadow = '0 0 0 4px var(--primary), 0 0 20px 8px rgba(255, 255, 255, 0.9)';
            
            // Se è una tabella, migliora anche la visualizzazione della tabella
            const table = targetElement.querySelector('table');
            if (table) {
              table.style.position = 'relative';
              table.style.zIndex = '9003';
            }
          }
        };
        
        // Migliora anche il metodo showCurrentStep
        window.biblioGuide.showCurrentStep = function() {
          // Nascondi prima eventuali elementi precedenti
          this.hideGuideElements();
  
          const allSteps = this.getSteps();
          const currentStepData = allSteps[this.currentStep];
  
          if (!currentStepData) {
            console.error("Step non trovato:", this.currentStep);
            return;
          }
  
          // Ottiene l'elemento target originale
          let targetElement = null;
          if (currentStepData.target) {
            targetElement = document.querySelector(currentStepData.target);
          }
  
          // Se il target non esiste e abbiamo un target di fallback, usalo
          if (!targetElement && currentStepData.fallbackTarget) {
            targetElement = document.querySelector(currentStepData.fallbackTarget);
          }
          
          // MIGLIORAMENTO: Se l'elemento è un tab, trova il contenuto corrispondente
          if (targetElement && (targetElement.classList.contains('tab') || targetElement.classList.contains('tabs') || targetElement.parentElement.classList.contains('tabs'))) {
            // Determina quale pagina stiamo visualizzando
            const currentPath = window.location.pathname;
            const pageName = currentPath.split('/').pop();
            
            // Cerca il contenuto appropriato
            let newTarget = null;
            
            switch(pageName) {
              case 'collane.php':
                newTarget = document.querySelector('#lista-collane-content .table-container');
                break;
              case 'edizioni.php':
                newTarget = document.querySelector('#lista-edizioni-content .table-container'); 
                break;
              case 'editori.php':
                newTarget = document.querySelector('#lista-editori-content .table-container');
                break;
              case 'prestiti.php':
                newTarget = document.querySelector('#prestiti-attivi-content .table-container');
                break;
              case 'utenti.php':
                newTarget = document.querySelector('#lista-utenti-content .table-container');
                break;
            }
            
            if (newTarget) {
              targetElement = newTarget;
            }
          }
  
          // Se ancora non abbiamo un target, mostra il tooltip al centro
          const isCentered = !targetElement;
  
          // Mostra l'overlay
          this.overlay.style.display = "block";
  
          // Aggiorna e mostra il tooltip
          const tooltipTitle = this.tooltipBox.querySelector(".guide-tooltip-title");
          const tooltipText = this.tooltipBox.querySelector(".guide-tooltip-text");
          const prevButton = this.tooltipBox.querySelector(".guide-prev-btn");
          const nextButton = this.tooltipBox.querySelector(".guide-next-btn");
  
          tooltipTitle.textContent = currentStepData.title;
          tooltipText.textContent = currentStepData.text;
  
          // Nascondi il pulsante "Indietro" se siamo al primo step
          prevButton.style.display = this.currentStep === 0 ? "none" : "inline-block";
  
          // Cambia il testo del pulsante "Avanti" a "Fine" se siamo all'ultimo step
          if (this.currentStep === allSteps.length - 1) {
            nextButton.textContent = "Fine";
          } else {
            nextButton.textContent = "Avanti";
          }
  
          // Reset delle trasformazioni precedenti
          this.tooltipBox.style.transform = "";
  
          if (isCentered || this.currentStep === allSteps.length - 1) {
            // Posiziona il tooltip al centro della finestra
            this.tooltipBox.style.top = "50%";
            this.tooltipBox.style.left = "50%";
            this.tooltipBox.style.transform = "translate(-50%, -50%)";
            this.tooltipBox.style.display = "block";
          } else {
            // Prima assicurati che l'elemento sia visibile
            targetElement.scrollIntoView({ behavior: "smooth", block: "center" });
  
            // Rimuovi qualsiasi highlight precedente e aggiungi la classe all'elemento corrente
            this.highlightElement(targetElement);
  
            // Posiziona il click blocker sopra l'elemento
            this.positionClickBlocker(targetElement);
  
            // Aspetta che lo scroll sia completato
            setTimeout(() => {
              // Calcola la posizione dell'elemento target
              const rect = targetElement.getBoundingClientRect();
              const scrollY = window.scrollY || window.pageYOffset;
  
              // Posiziona il tooltip sotto l'elemento target
              const tooltipTop = rect.bottom + scrollY + 10;
              let tooltipLeft = rect.left + rect.width / 2 - 175; // Metà della larghezza del tooltip (350px/2)
  
              // Assicurati che il tooltip non vada fuori dallo schermo
              if (tooltipLeft < 10) tooltipLeft = 10;
              if (tooltipLeft + 350 > window.innerWidth)
                tooltipLeft = window.innerWidth - 350 - 10;
  
              this.tooltipBox.style.top = tooltipTop + "px";
              this.tooltipBox.style.left = tooltipLeft + "px";
              this.tooltipBox.style.display = "block";
            }, 300);
          }
        };
      }
    }, 500);
  });