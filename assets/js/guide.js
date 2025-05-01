/**
 * BiblioTech - Sistema di Guida Interattiva Migliorato
 * Questa versione risolve il problema della selezione che evidenzia
 * solo la barra superiore invece del contenuto effettivo
 */

class BiblioGuide {
    constructor() {
      this.active = false;
      this.currentStep = 0;
      this.overlay = null;
      this.tooltipBox = null;
      this.clickBlocker = null;
  
      // Verifica se la guida è già attiva
      this.checkGuideStatus();
  
      // Inizializza l'interfaccia della guida
      this.initGuideInterface();
    }
  
    // Controlla se la guida è attiva dalla sessione
    checkGuideStatus() {
      const status = sessionStorage.getItem("biblioGuideActive");
      if (status === "true") {
        this.active = true;
        this.currentStep = parseInt(
          sessionStorage.getItem("biblioGuideStep") || "0"
        );
      }
    }
  
    // Salva lo stato della guida
    saveGuideStatus() {
      sessionStorage.setItem("biblioGuideActive", this.active);
      sessionStorage.setItem("biblioGuideStep", this.currentStep);
    }
  
    // Inizializza l'interfaccia della guida
    initGuideInterface() {
      // Crea l'overlay se non esiste
      if (!this.overlay) {
        this.overlay = document.createElement("div");
        this.overlay.className = "guide-overlay";
        this.overlay.style.display = "none";
        document.body.appendChild(this.overlay);
      }
  
      // Crea il blocco di click se non esiste
      if (!this.clickBlocker) {
        this.clickBlocker = document.createElement("div");
        this.clickBlocker.className = "guide-click-blocker";
        this.clickBlocker.style.display = "none";
        document.body.appendChild(this.clickBlocker);
      }
  
      // Crea il box del tooltip se non esiste
      if (!this.tooltipBox) {
        this.tooltipBox = document.createElement("div");
        this.tooltipBox.className = "guide-tooltip";
        this.tooltipBox.style.display = "none";
  
        const tooltipContent = document.createElement("div");
        tooltipContent.className = "guide-tooltip-content";
  
        const tooltipTitle = document.createElement("h3");
        tooltipTitle.className = "guide-tooltip-title";
  
        const tooltipText = document.createElement("p");
        tooltipText.className = "guide-tooltip-text";
  
        const tooltipButtons = document.createElement("div");
        tooltipButtons.className = "guide-tooltip-buttons";
  
        const prevButton = document.createElement("button");
        prevButton.textContent = "Indietro";
        prevButton.className = "btn btn-secondary guide-prev-btn";
        prevButton.addEventListener("click", () => this.prevStep());
  
        const nextButton = document.createElement("button");
        nextButton.textContent = "Avanti";
        nextButton.className = "btn btn-primary guide-next-btn";
        nextButton.addEventListener("click", () => this.nextStep());
  
        const closeButton = document.createElement("button");
        closeButton.textContent = "Chiudi Guida";
        closeButton.className = "btn btn-danger guide-close-btn";
        closeButton.addEventListener("click", () => this.stopGuide());
  
        tooltipButtons.appendChild(prevButton);
        tooltipButtons.appendChild(nextButton);
        tooltipButtons.appendChild(closeButton);
  
        tooltipContent.appendChild(tooltipTitle);
        tooltipContent.appendChild(tooltipText);
        tooltipContent.appendChild(tooltipButtons);
  
        this.tooltipBox.appendChild(tooltipContent);
        document.body.appendChild(this.tooltipBox);
      }
  
      // Se la guida è attiva, mostra lo step corrente
      if (this.active) {
        setTimeout(() => this.showCurrentStep(), 500);
      }
    }
  
    // Avvia la guida
    startGuide() {
      this.active = true;
      this.currentStep = 0;
      this.saveGuideStatus();
      this.showCurrentStep();
    }
  
    // Ferma la guida
    stopGuide() {
      this.active = false;
      this.saveGuideStatus();
      this.hideGuideElements();
    }
  
    // Prossimo step
    nextStep() {
      // Verifica se siamo all'ultimo step
      const allSteps = this.getSteps();
      if (this.currentStep >= allSteps.length - 1) {
        // Se siamo all'ultimo step, fermiamo la guida
        this.stopGuide();
        return;
      }
  
      this.currentStep++;
      this.saveGuideStatus();
  
      const currentStepData = allSteps[this.currentStep];
  
      if (!currentStepData) {
        // Se abbiamo finito tutti gli step, terminiamo la guida
        this.stopGuide();
        return;
      }
  
      // Se il prossimo step è su un'altra pagina, reindirizza
      const currentPath = this.getCurrentPageName();
      if (currentStepData.page !== currentPath) {
        // Controlla se dobbiamo aggiungere il prefisso dell'amministrazione
        let targetPage = currentStepData.page;
        if (
          targetPage.startsWith("amministrazione/") &&
          !window.location.pathname.includes("amministrazione")
        ) {
          // Siamo nella root e dobbiamo andare in amministrazione
          window.location.href = targetPage;
        } else if (
          targetPage.startsWith("amministrazione/") &&
          window.location.pathname.includes("amministrazione")
        ) {
          // Siamo già in amministrazione e dobbiamo andare a un'altra pagina di amministrazione
          window.location.href = targetPage.replace("amministrazione/", "");
        } else if (
          !targetPage.startsWith("amministrazione/") &&
          window.location.pathname.includes("amministrazione")
        ) {
          // Siamo in amministrazione e dobbiamo tornare alla root
          window.location.href = "../" + targetPage;
        } else {
          // Navigazione normale nella root
          window.location.href = targetPage;
        }
      } else {
        this.showCurrentStep();
      }
    }
  
    // Step precedente
    prevStep() {
      if (this.currentStep > 0) {
        this.currentStep--;
        this.saveGuideStatus();
  
        const allSteps = this.getSteps();
        const currentStepData = allSteps[this.currentStep];
  
        // Se lo step precedente è su un'altra pagina, reindirizza
        const currentPath = this.getCurrentPageName();
        if (currentStepData.page !== currentPath) {
          // Controlla se dobbiamo aggiungere il prefisso dell'amministrazione
          let targetPage = currentStepData.page;
          if (
            targetPage.startsWith("amministrazione/") &&
            !window.location.pathname.includes("amministrazione")
          ) {
            // Siamo nella root e dobbiamo andare in amministrazione
            window.location.href = targetPage;
          } else if (
            targetPage.startsWith("amministrazione/") &&
            window.location.pathname.includes("amministrazione")
          ) {
            // Siamo già in amministrazione e dobbiamo andare a un'altra pagina di amministrazione
            window.location.href = targetPage.replace("amministrazione/", "");
          } else if (
            !targetPage.startsWith("amministrazione/") &&
            window.location.pathname.includes("amministrazione")
          ) {
            // Siamo in amministrazione e dobbiamo tornare alla root
            window.location.href = "../" + targetPage;
          } else {
            // Navigazione normale nella root
            window.location.href = targetPage;
          }
        } else {
          this.showCurrentStep();
        }
      }
    }
  
    // Ottiene il nome della pagina corrente
    getCurrentPageName() {
      const pathname = window.location.pathname;
      const segments = pathname.split("/");
      let pageName = segments[segments.length - 1] || "index.php";
  
      // Se siamo in una pagina di amministrazione, aggiungi il prefisso
      if (pathname.includes("amministrazione/")) {
        pageName = "amministrazione/" + pageName;
      }
  
      // Se siamo nella root senza nome di file, considera index.php
      if (pageName === "") {
        pageName = "index.php";
      }
  
      return pageName;
    }
  
    // Posiziona il click blocker sopra l'elemento evidenziato
    positionClickBlocker(element) {
      if (!element || !this.clickBlocker) return;
  
      const rect = element.getBoundingClientRect();
      const scrollY = window.scrollY || window.pageYOffset;
      const scrollX = window.scrollX || window.pageXOffset;
  
      this.clickBlocker.style.top = rect.top + scrollY + "px";
      this.clickBlocker.style.left = rect.left + scrollX + "px";
      this.clickBlocker.style.width = rect.width + "px";
      this.clickBlocker.style.height = rect.height + "px";
      this.clickBlocker.style.display = "block";
    }
  
    // Mostra lo step corrente con focus migliorato sul contenuto
    showCurrentStep() {
      // Nascondi prima eventuali elementi precedenti
      this.hideGuideElements();
  
      const allSteps = this.getSteps();
      const currentStepData = allSteps[this.currentStep];
  
      if (!currentStepData) {
        console.error("Step non trovato:", this.currentStep);
        return;
      }
  
      // Ottiene l'elemento target
      let targetElement = null;
      if (currentStepData.target) {
        targetElement = document.querySelector(currentStepData.target);
      }
  
      // Se il target non esiste e abbiamo un target di fallback, usalo
      if (!targetElement && currentStepData.fallbackTarget) {
        targetElement = document.querySelector(currentStepData.fallbackTarget);
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
    }
  
    // Evidenzia un elemento specifico con focus migliorato
    highlightElement(element) {
      // Rimuovi prima l'highlight precedente
      const prevHighlight = document.querySelector(".guide-highlight");
      if (prevHighlight) {
        prevHighlight.classList.remove("guide-highlight");
      }
  
      // Aggiungi la classe di highlight all'elemento
      element.classList.add("guide-highlight");
    }
  
    // Nascondi gli elementi della guida
    hideGuideElements() {
      // Rimuovi l'highlight
      const highlight = document.querySelector(".guide-highlight");
      if (highlight) {
        highlight.classList.remove("guide-highlight");
      }
  
      // Nascondi il click blocker
      if (this.clickBlocker) {
        this.clickBlocker.style.display = "none";
      }
  
      this.overlay.style.display = "none";
      this.tooltipBox.style.display = "none";
    }
  
    // Gli step della guida migliorati per evidenziare meglio il contenuto
    getSteps() {
      return [
        // Home page
        {
          step: 0,
          page: "index.php",
          target: ".sidebar",
          fallbackTarget: ".logo",
          title: "Benvenuto in BiblioTech!",
          text: "Questa è la guida interattiva che ti aiuterà a scoprire tutte le funzionalità del sistema. Iniziamo con la barra laterale che ti permette di navigare tra le varie sezioni.",
        },
        {
          step: 1,
          page: "index.php",
          target: ".stats-grid",
          title: "Dashboard",
          text: "Questa è la dashboard principale che mostra le statistiche della biblioteca: opere totali, esemplari, utenti e prestiti attivi.",
        },
        {
          step: 2,
          page: "index.php",
          target: ".section:nth-child(4) .table-container",
          fallbackTarget: ".section .table-container",
          title: "Ultime Opere Aggiunte",
          text: "Qui puoi vedere le opere aggiunte più recentemente. Puoi visualizzare i dettagli o modificarle rapidamente.",
        },
        {
          step: 3,
          page: "index.php",
          target: 'a[href="catalogo.php"]',
          title: "Catalogo",
          text: "Clicca AVANTI per accedere al catalogo completo delle opere. Proseguiamo la guida esplorando il catalogo.",
        },
  
        // Catalogo
        {
          step: 4,
          page: "catalogo.php",
          target: ".filter-section .filters",
          title: "Filtri Catalogo",
          text: "Qui puoi filtrare il catalogo per autore, anno di pubblicazione, editore e disponibilità.",
        },
        {
          step: 5,
          page: "catalogo.php",
          target: ".book-grid",
          title: "Visualizzazione Opere",
          text: "Qui vengono visualizzate tutte le opere disponibili nel catalogo. Ogni scheda mostra titolo, autore, anno e disponibilità.",
        },
        {
          step: 6,
          page: "catalogo.php",
          target: ".book-card:first-child .book-actions",
          fallbackTarget: ".book-card .book-actions",
          title: "Azioni sui Libri",
          text: "Da qui puoi visualizzare i dettagli di un'opera o procedere al prestito se disponibile.",
        },
        {
          step: 7,
          page: "catalogo.php",
          target: 'a[href="prestiti.php"]',
          fallbackTarget: ".sidebar",
          title: "Gestione Prestiti",
          text: "Clicca AVANTI per accedere alla pagina dei prestiti.",
        },
  
        // Prestiti
        {
          step: 8,
          page: "prestiti.php",
          target: ".table-container",
          title: "Prestiti Attivi",
          text: "Qui puoi visualizzare tutti i prestiti attualmente in corso e gestirli.",
        },
        {
          step: 9,
          page: "prestiti.php",
          target: "#btn-nuovo-prestito",
          fallbackTarget: ".btn-primary",
          title: "Nuovo Prestito",
          text: "Da qui puoi registrare un nuovo prestito.",
        },
        {
          step: 10,
          page: "prestiti.php",
          target: 'a[href="utenti.php"]',
          fallbackTarget: ".sidebar",
          title: "Gestione Utenti",
          text: "Clicca AVANTI per accedere alla gestione degli utenti della biblioteca.",
        },
  
        // Utenti
        {
          step: 11,
          page: "utenti.php",
          target: ".table-container",
          title: "Lista Utenti",
          text: "Qui puoi vedere tutti gli utenti registrati nel sistema.",
        },
        {
          step: 12,
          page: "utenti.php",
          target: "#btn-nuovo-utente",
          fallbackTarget: ".btn-primary",
          title: "Nuovo Utente",
          text: "Da qui puoi aggiungere un nuovo utente al sistema.",
        },
        {
          step: 13,
          page: "utenti.php",
          target: 'a[href="amministrazione/editori.php"]',
          fallbackTarget: ".sidebar",
          title: "Amministrazione",
          text: "Clicca AVANTI per accedere alle funzionalità amministrative e gestire gli editori.",
        },
  
        // Editori
        {
          step: 14,
          page: "amministrazione/editori.php",
          target: ".table-container",
          title: "Gestione Editori",
          text: "Qui puoi visualizzare e gestire tutti gli editori.",
        },
        {
          step: 15,
          page: "amministrazione/editori.php",
          target: 'a[href="collane.php"]',
          fallbackTarget: ".sidebar",
          title: "Gestione Collane",
          text: "Clicca AVANTI per accedere alla gestione delle collane.",
        },
  
        // Collane
        {
          step: 16,
          page: "amministrazione/collane.php",
          target: ".table-container",
          title: "Gestione Collane",
          text: "Qui puoi visualizzare e gestire tutte le collane editoriali.",
        },
        {
          step: 17,
          page: "amministrazione/collane.php",
          target: 'a[href="edizioni.php"]',
          fallbackTarget: ".sidebar",
          title: "Gestione Edizioni",
          text: "Clicca AVANTI per accedere alla gestione delle edizioni.",
        },
  
        // Edizioni
        {
          step: 18,
          page: "amministrazione/edizioni.php",
          target: ".table-container",
          title: "Gestione Edizioni",
          text: "Qui puoi gestire le diverse edizioni delle opere disponibili in biblioteca.",
        },
        {
          step: 19,
          page: "amministrazione/edizioni.php",
          target: 'a[href="rapporti.php"]',
          fallbackTarget: ".sidebar",
          title: "Rapporti e Statistiche",
          text: "Clicca AVANTI per accedere ai rapporti statistici.",
        },
  
        // Rapporti
        {
          step: 20,
          page: "amministrazione/rapporti.php",
          target: ".stats-grid",
          title: "Statistiche",
          text: "Qui puoi visualizzare le statistiche generali della biblioteca. Clicca AVANTI per completare la guida.",
        },
  
        // Ultimo step - sempre centrato a prescindere dalla pagina
        {
          step: 21,
          page: "index.php", // Questo è solo indicativo, l'ultimo step viene mostrato sempre al centro
          title: "Fine della Guida",
          text: "Complimenti! Hai completato la guida di BiblioTech. Ora conosci tutte le funzionalità principali del sistema. Clicca FINE per terminare la guida.",
        },
      ];
    }
  }
  
  // Attendi che il DOM sia completamente caricato
  document.addEventListener("DOMContentLoaded", function () {
    // Inizializza la guida
    window.biblioGuide = new BiblioGuide();
  
    // Aggiungi pulsanti alla home
    if (
      window.location.pathname.endsWith("index.php") ||
      window.location.pathname === "/" ||
      window.location.pathname.endsWith("/")
    ) {
      const startGuideBtn = document.getElementById("start-guide-btn");
      if (startGuideBtn) {
        startGuideBtn.addEventListener("click", function () {
          window.biblioGuide.startGuide();
        });
      }
  
      const stopGuideBtn = document.getElementById("stop-guide-btn");
      if (stopGuideBtn) {
        stopGuideBtn.addEventListener("click", function () {
          window.biblioGuide.stopGuide();
        });
      }
    }
  });