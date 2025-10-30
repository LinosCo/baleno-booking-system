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
