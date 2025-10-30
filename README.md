# Baleno Booking System

Sistema completo di gestione prenotazioni per la Casa di Quartiere Baleno - San Zeno, Verona.

## Descrizione

Plugin WordPress professionale per la gestione completa delle prenotazioni degli spazi della Casa di Quartiere Baleno. Include sistema di approvazione manuale, notifiche email automatiche, calendario, gestione utenti e molto altro.

## Caratteristiche Principali

### 🎯 Funzionalità Utente (Frontend)
- **Modulo di prenotazione completo** con tutti i campi richiesti
- **Validazione Codice Fiscale** automatica
- **Calcolo automatico dei prezzi** basato su sala, durata e attrezzature
- **Verifica disponibilità in tempo reale**
- **Selezione attrezzature aggiuntive** (videoproiettore, impianto audio, ecc.)
- **Conferma email automatica** dopo la richiesta
- **Responsive** - funziona perfettamente su mobile e tablet

### 👨‍💼 Funzionalità Amministrative (Backend)
- **Dashboard statistiche** con panoramica completa
- **Gestione prenotazioni** con approvazione/rifiuto manuale
- **Calendario visivo** delle prenotazioni
- **Gestione spazi** con tariffe personalizzate
- **Gestione attrezzature**
- **Sistema di ruoli e permessi** per più utenti
- **Notifiche email** automatiche per ogni cambio stato
- **Filtri avanzati** per data, stato, sala
- **Esportazione dati** (feature pronta per implementazione)

### 📧 Sistema Email
- Email di conferma ricezione richiesta all'utente
- Email di notifica nuova prenotazione all'amministratore
- Email di approvazione all'utente
- Email di rifiuto con motivazione all'utente
- Template HTML professionali e personalizzati

### 💾 Database
- **3 tabelle ottimizzate**:
  - `wp_baleno_bookings` - Prenotazioni
  - `wp_baleno_spaces` - Spazi/Sale
  - `wp_baleno_equipment` - Attrezzature
- **Dati precaricati** da regolamento Baleno
- **Backup-friendly** - tutte le tabelle con prefisso WordPress

## Installazione

### 1. Carica il Plugin

**Metodo A - Tramite WordPress Admin:**
1. Comprimi la cartella `baleno-booking-system` in un file ZIP
2. Vai su WordPress Admin → Plugin → Aggiungi nuovo
3. Clicca su "Carica plugin"
4. Seleziona il file ZIP
5. Clicca su "Installa ora"

**Metodo B - Tramite FTP:**
1. Carica la cartella `baleno-booking-system` in `/wp-content/plugins/`
2. Vai su WordPress Admin → Plugin
3. Attiva "Baleno Booking System"

### 2. Attivazione

Quando attivi il plugin:
- ✅ Vengono create automaticamente le tabelle del database
- ✅ Vengono inseriti tutti gli spazi e le attrezzature dal regolamento
- ✅ Viene creato il ruolo "Baleno Manager"
- ✅ Vengono impostate le opzioni predefinite

### 3. Configurazione Iniziale

1. Vai su **Baleno Booking → Impostazioni**
2. Configura:
   - Email amministratore per le notifiche
   - Importo cauzione (default: €50)
   - Approvazione automatica (se desiderato)

### 4. Gestione Utenti

**Assegnare ruolo Baleno Manager a un utente:**
1. Vai su Utenti → Tutti gli utenti
2. Modifica l'utente desiderato
3. Nel campo "Ruolo" seleziona "Baleno Manager"
4. Salva

**Permessi:**
- **Administrator** - Accesso completo + impostazioni
- **Baleno Manager** - Gestione prenotazioni e approvazioni

### 5. Aggiungere il Modulo al Sito

**Shortcode disponibili:**

```
[baleno_booking_form]
```
Mostra il modulo di prenotazione completo

```
[baleno_spaces]
```
Mostra l'elenco degli spazi disponibili con tariffe

**Come utilizzare:**
1. Crea una nuova Pagina (es. "Prenota")
2. Inserisci lo shortcode `[baleno_booking_form]`
3. Pubblica la pagina

Oppure usa il Block Editor:
1. Aggiungi un blocco "Shortcode"
2. Inserisci `[baleno_booking_form]`

## Utilizzo

### Per gli Utenti

1. **Accedere al modulo di prenotazione**
   - Vai alla pagina dove hai inserito lo shortcode

2. **Compilare il modulo**
   - Sezione 1: Dati personali (obbligatorio)
   - Sezione 2: Dati organizzazione (opzionale)
   - Sezione 3: Dettagli prenotazione (sala, data, orario)
   - Sezione 4: Attrezzature aggiuntive
   - Sezione 5: Riepilogo costi (calcolato automaticamente)
   - Sezione 6: Accettazione regolamento

3. **Invio e conferma**
   - Il sistema verifica la disponibilità
   - Viene generato un codice prenotazione unico
   - L'utente riceve email di conferma ricezione
   - Lo staff riceve notifica della nuova richiesta

### Per gli Amministratori

1. **Dashboard**
   - Vai su **Baleno Booking** nel menu WordPress
   - Visualizza statistiche in tempo reale
   - Vedi prenotazioni in attesa di approvazione

2. **Gestire le prenotazioni**
   - Clicca su "👁 Dettagli" per vedere tutti i dettagli
   - Clicca su "✓ Approva" per approvare (invia email automatica)
   - Clicca su "✗ Rifiuta" per rifiutare (richiede motivazione)
   - Clicca su "🗑 Elimina" per eliminare definitivamente

3. **Filtri**
   - Filtra per stato: Tutte / In Attesa / Approvate / Rifiutate
   - Filtra per sala dal calendario

4. **Calendario**
   - Vai su **Baleno Booking → Calendario**
   - Visualizza tutte le prenotazioni approvate
   - Filtra per sala specifica

## Dati Precaricati

Il plugin include già tutti i dati dal regolamento Baleno:

### Spazi
- **A1** - Navata Completa (Pipino) - 74 m²
- **A2** - Sala Riunioni Orata (Pipino) - 18.5 m²
- **A3** - Spazio Libero (Pipino) - 37 m²
- **B1** - Sala Riunioni (Spagna) - 18.5 m²
- **C** - Navata Centrale - 148 m²
- **D** - Baleno Completo - 314.5 m²

### Tariffe (come da regolamento)
Tutte le tariffe sono già configurate per 1h, 2h, mezza giornata, giornata intera.

### Attrezzature
- Videoproiettore - €20
- Impianto Audio - €30
- Lavagna a Fogli Mobili - €5
- Tavoli Pieghevoli Extra - €10
- Sedie Extra - €5
- Frigorifero - €10

## Personalizzazione

### Modificare le Tariffe

1. Vai su **Baleno Booking → Spazi**
2. Visualizza le tariffe attuali
3. Per modificarle, accedi al database:
   - Tabella: `wp_baleno_spaces`
   - Campi: `price_1h`, `price_2h`, `price_half_day`, `price_full_day`

### Aggiungere Nuovi Spazi

```sql
INSERT INTO wp_baleno_spaces
(space_code, space_name, space_category, description, capacity, size_mq, price_1h, price_2h, price_half_day, price_full_day, is_active, display_order)
VALUES
('E1', 'Nuova Sala', 'CATEGORIA', 'Descrizione', 30, 50, 30.00, 50.00, 80.00, 120.00, 1, 7);
```

### Modificare le Email

Le email si trovano in:
`includes/class-baleno-booking-email.php`

Modifica i template HTML nelle funzioni:
- `user_confirmation` - Email all'utente dopo richiesta
- `admin_notification` - Email all'admin per nuova richiesta
- `booking_approved` - Email all'utente per approvazione
- `booking_rejected` - Email all'utente per rifiuto

### Personalizzare i Colori

Modifica i file CSS:
- `assets/css/baleno-public.css` - Stili frontend
- `assets/css/baleno-admin.css` - Stili backend

Colore principale: `#2c5aa0` (blu Baleno)

## Risoluzione Problemi

### Le email non vengono inviate

1. Verifica le impostazioni SMTP di WordPress
2. Installa un plugin SMTP (es. WP Mail SMTP)
3. Controlla la cartella spam

### Errore "Tabella non trovata"

1. Disattiva il plugin
2. Riattiva il plugin (ricrea le tabelle)
3. Se persiste, esegui manualmente lo script di creazione tabelle

### Le prenotazioni non vengono salvate

1. Verifica i permessi del database
2. Controlla il log degli errori di WordPress
3. Verifica che il nonce sia valido (cache?)

### Problemi di visualizzazione

1. Svuota la cache del browser
2. Svuota la cache di WordPress (se usi un plugin di cache)
3. Verifica che i file CSS/JS siano caricati correttamente

## Sicurezza

Il plugin implementa:
- ✅ Nonce verification per tutte le richieste AJAX
- ✅ Sanitizzazione di tutti gli input
- ✅ Prepared statements per query SQL
- ✅ Capability checks per azioni amministrative
- ✅ Validazione lato client e server
- ✅ Escape di tutti gli output

## Pubblicazione su GitHub

Se desideri creare un nuovo repository GitHub per questo progetto, nella cartella `docs/` trovi la guida passo passo **[Creare un nuovo progetto su GitHub](docs/github-project-setup.md)** con tutte le indicazioni per:

- inizializzare Git (se necessario);
- creare il repository remoto su GitHub;
- collegare il tuo codice locale al repository appena creato;
- impostare un Project Board per organizzare le attività;
- configurare opzioni avanzate come protezione dei branch e workflow automatizzati.

Se hai pubblicato il codice nel repository GitHub sbagliato, la guida spiega anche come verificare i remote configurati e ricollegarti rapidamente al progetto corretto.

Segui quei passaggi per pubblicare Baleno Booking System online e collaborare facilmente con il tuo team.

## Supporto

Per supporto, contatta:
- **Email**: info@balenosanzeno.it
- **Sito**: https://balenosanzeno.it

## Informazioni Tecniche

- **Versione**: 1.0.0
- **Richiede WordPress**: 5.0 o superiore
- **Richiede PHP**: 7.0 o superiore
- **Licenza**: GPL-2.0+
- **Autore**: Nicola Zago

## Changelog

### Version 1.0.0 (2025-10-14)
- Prima release
- Sistema completo di prenotazione
- Dashboard amministrativa
- Calendario prenotazioni
- Sistema email automatico
- Gestione ruoli utente
- Tutti i dati da regolamento Baleno precaricati

## Crediti

Sviluppato per **Casa di Quartiere Baleno**
Via Re Pipino 3/A - San Zeno, Verona
https://balenosanzeno.it

---

© 2025 Baleno Casa di Quartiere. Tutti i diritti riservati.
