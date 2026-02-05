# UniboStu 📚

Il sito web è basato su uno stack LAMP containerizzato utilizzando Docker Compose.

-----

## 🚀 Quick Start in 3 Passi

Assicurati di avere **Docker** e **Docker Compose (v2)** installati.

### 1. Preparazione dell'ambiente per la prima esecuzione

#### Windows

Per prima cosa, crea manualmente un file `.env` nella root del progetto con il seguente contenuto:

```env
# MySQL Configuration
MYSQL_DATABASE=unibostu
MYSQL_USER=unibostu
```

E poi, crea la cartella `secrets/` e al suo interno i file `db_root_password.txt` e `db_user_password.txt` con le password desiderate per l'utente `root` e l'utente `unibostu`.

Per esempio:

```txt
rootpassword123
```

```txt
userpassword123
```

#### Linux

Per linux si può eseguire direttamente lo script `init.sh` che si occupa di creare il file `.env` e le password nella cartella `secrets/`.

```bash
chmod +x init.sh
./init.sh
```

### 2. Avvia i servizi

Avvia l'intera infrastruttura. Aggiungi la flag `-d` per eseguire i container in background.

```bash
docker compose up --build
```

### 3. Accedi

| Servizio | Porta Locale | Dettagli di Accesso
|---|---|---|
| **Sito Web (www)** | `http://localhost:80` | Mappato su `./www`
| **phpMyAdmin** | `http://localhost:8001` | **User:** `{$MYSQL_USER}` in `.env` <br> **Password:** `./secrets/db_user_password.txt`
| **db (MySQL)** | (Rete interna) | Configurato in `.env` e `secrets/`.

-----

## 👤 Utenti di Test

Il database viene inizializzato con i seguenti utenti di test:

| Username | Password | Ruolo | Nome | Cognome |
|---|---|---|---|---|
| `testuser` | `aaaaaa` | Utente normale | Test | User |
| `testadmin` | `aaaaaa` | Amministratore | - | - |

**Nota:** Questi utenti sono pre-caricati nel database per scopi di testing e sviluppo.

-----

## ✅ Struttura Essenziale del Progetto

```
├── init.sh              # Script di Setup Essenziale (crea .env e secrets)
├── docker-compose.yml   # Definizione dei servizi Docker
├── Dockerfile           # Definizione del container PHP/Apache
├── db/                  # Script SQL di inizializzazione (eseguiti al primo avvio)
├── www/                 # Root del server web (sorgenti PHP)
    ├── index.php
    └── ...
├── secrets/             # 🔐 Directory per i segreti (password)
└── .env                 # Variabili d'ambiente importate in docker-compose.yml
```

-----

## 🛠 Comandi Utili

| Azione | Comando | Descrizione |
|---|---|---|
| **Log in tempo reale** | `docker compose logs -f` | Utile per debug (mostra tutti i log) |
| **Entra nel container PHP** | `docker compose exec www bash` | Per eseguire comandi PHP, installare dipendenze, ecc. |
| **Entra nel container MySQL** | `docker compose exec db mysql -u{$MYSQL_USER} -p` | Inserisci l'username (configurato in `.env`) e la password (contenuta in `secrets/db_user_password.txt`) |
| **Ferma i servizi** | `docker compose down` | Spegne i container |
| **Reset totale (Dati inclusi)** | `docker compose down -v` | **ATTENZIONE\!** Ferma i servizi e cancella il **volume persistente** del database. |

-----

## ⚙️ Configurazione e Sicurezza

### Variabili d'Ambiente (`.env`)

Questo file contiene le configurazioni non sensibili: il nome del database (`{$MYSQL_DATABASE}`) e l'utente (`{$MYSQL_USER}`).

### Secrets (`secrets/`)

Le password di MySQL di `root` e dell'utente applicativo (`{$MYSQL_USER}`) sono archiviate all'interno della cartella `secrets/`.
```
secrets/
├── db_root_password.txt
└── db_user_password.txt 
```