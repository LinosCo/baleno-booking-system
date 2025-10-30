# Creare un nuovo progetto su GitHub

Questa guida ti accompagna passo dopo passo nella creazione di un nuovo repository GitHub per il plugin **Baleno Booking System** e nella pubblicazione del codice presente in questa cartella.

## 1. Prerequisiti

Prima di iniziare assicurati di avere:

- Un account GitHub attivo ([https://github.com](https://github.com)).
- Git installato sulla tua macchina (`git --version` per verificarlo).
- Accesso a questa cartella del progetto sul tuo computer.

## 2. Inizializzare Git (se necessario)

Se hai appena copiato i file su un nuovo computer e la cartella non è ancora un repository Git, esegui:

```bash
git init
git add .
git commit -m "Initial commit"
```

> Se stai lavorando direttamente da questa repository (ad esempio dopo averla clonata) puoi saltare questo passaggio.

## 3. Creare il repository su GitHub

1. Accedi a GitHub.
2. Clicca sul pulsante **New** nella tua pagina dei repository oppure visita direttamente [https://github.com/new](https://github.com/new).
3. Compila il form:
   - **Repository name:** `baleno-booking-system` (o il nome che preferisci).
   - **Description:** breve descrizione del progetto.
   - **Visibility:** scegli *Public* (pubblico) o *Private* (privato).
4. Lascia disabilitate le opzioni *Add a README*, *Add .gitignore* e *Choose a license* se il progetto le contiene già.
5. Premi **Create repository**.

## 4. Collegare il repository remoto

GitHub mostrerà i comandi per collegare la tua cartella al nuovo repository. Esegui quelli nella sezione **"…or push an existing repository from the command line"**:

```bash
git remote add origin https://github.com/<tuo-utente>/<nome-repository>.git
git branch -M main
git push -u origin main
```

Sostituisci `<tuo-utente>` e `<nome-repository>` con i valori corretti.

### Se hai collegato il repository sbagliato

Se ti accorgi di aver puntato al progetto GitHub errato (ad esempio perché il codice pubblicato non corrisponde), puoi correggere la configurazione locale in pochi passi:

1. Controlla a quale repository stai puntando:

   ```bash
   git remote -v
   ```

2. Rimuovi o rinomina il remote errato:

   ```bash
   git remote remove origin
   # oppure, per rinominare invece di rimuovere:
   # git remote rename origin old-origin
   ```

3. Aggiungi il nuovo repository corretto:

   ```bash
   git remote add origin https://github.com/<tuo-utente>/<nuovo-repository>.git
   ```

4. Invia nuovamente il codice:

   ```bash
   git push -u origin main
   ```

> Suggerimento: se sul repository sbagliato è già stato caricato del codice, archivialo o impostalo come privato da GitHub per evitare confusione. Puoi poi eliminare le release/branch non più necessari.

### Cambiare repository GitHub dopo il primo push

Se il progetto è già online ma vuoi spostare *tutta* la cronologia su un nuovo repository (ad esempio per rinominare l'organizzazione o separare ambienti di lavoro), segui questi passaggi:

1. **Tieni traccia del remoto attuale** (facoltativo ma consigliato):

   ```bash
   git remote rename origin old-origin
   ```

   In questo modo puoi ancora eseguire push/pull verso il repository precedente in caso di emergenza.

2. **Crea il nuovo repository vuoto su GitHub** senza README/LICENZA/`.gitignore`.

3. **Collega il nuovo remoto** mantenendo la stessa branch principale:

   ```bash
   git remote add origin https://github.com/<tuo-utente>/<nuovo-repository>.git
   git push -u origin main
   ```

4. **Verifica che tutte le branch siano disponibili nel nuovo repository**:

   ```bash
   git push origin --all
   git push origin --tags
   ```

5. **Aggiorna le integrazioni esterne** (CI/CD, webhook, deploy keys) in modo che puntino al nuovo URL GitHub.

6. **Quando sei certo che la migrazione è completa**, puoi rimuovere il vecchio remoto locale:

   ```bash
   git remote remove old-origin
   ```

   Se il repository precedente non serve più, valuta se archiviarlo o eliminarlo da GitHub per evitare contributi involontari.

> Nota: se preferisci effettuare una *transfer ownership* direttamente da GitHub (Settings → Transfer), la cronologia viene spostata automaticamente. Usa la procedura qui sopra se vuoi mantenere due repository distinti o hai bisogno di un passaggio graduale.

## 5. Creare un progetto GitHub (Project Board)

Oltre al repository puoi organizzare le attività con un **GitHub Project**:

1. Vai su **https://github.com/orgs/<tuo-utente>/projects** o apri la tab **Projects** direttamente dal repository appena creato.
2. Clicca su **New project** e scegli il template *Board* oppure *Table*.
3. Dai un nome al progetto (es. "Baleno Booking Roadmap").
4. Imposta colonne o gruppi (es. *Da fare*, *In corso*, *Completato*).
5. Collega il progetto al repository appena creato tramite la sezione **Repository**.
6. Aggiungi le issue come task nel progetto per tracciare lo stato di avanzamento.

## 6. Suggerimenti per il primo push

- Verifica lo stato del repository con `git status` prima del push.
- Utilizza commit chiari e descrittivi, ad esempio `git commit -m "Aggiunge guida per creare repository"`.
- Se utilizzi branch differenti dal principale, ricordati di aprire una Pull Request su GitHub per revisionare le modifiche.

## 7. Configurazione opzionale

- **Protezione branch main:** in GitHub → *Settings* → *Branches*, aggiungi una regola per richiedere pull request e approvazioni prima del merge.
- **Workflow automatizzati:** crea una cartella `.github/workflows/` per aggiungere pipeline (ad esempio test automatici o deploy).
- **Issue templates:** configura modelli per segnalazioni e nuove funzionalità nella cartella `.github/ISSUE_TEMPLATE/`.

## 8. Prossimi passi

Una volta pubblicato il repository:

- Invita i collaboratori tramite la sezione *Settings → Collaborators*.
- Crea le prime issue descrivendo le funzionalità da sviluppare.
- Pianifica le milestone per le release principali.

Con questa procedura il plugin sarà pronto per essere gestito completamente da GitHub, con cronologia versioni, progetti e collaborazione aperta.
