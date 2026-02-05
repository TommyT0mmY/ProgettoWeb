# Note del Progetto UniboStu

## Architettura Generale

### Stack Tecnologico
- **Backend**: PHP 8+ con Apache
- **Database**: MySQL 9.5
- **Containerizzazione**: Docker Compose
- **Pattern**: MVC (Model-View-Controller) custom

### Struttura Docker
Il progetto utilizza quattro container:
1. `www`: Apache + PHP (applicazione principale)
2. `db`: MySQL (database)
3. `phpmyadmin`: Interfaccia di gestione database
4. `session-gc`: Container dedicato alla garbage collection delle sessioni

---

## Architettura Applicativa

### Pattern MVC Custom
- **Controller**: Gestione delle richieste HTTP, validazione, orchestrazione
- **Model**: DTO (Data Transfer Object) + Repository + Service
- **View**: Template PHP con sistema di layout, componenti e rendering engine

### Sistema di Routing
- Router basato su **prefix tree** per matching efficiente delle route
- Supporto per route parametriche (es. `/users/:userid`, `/posts/:postid`)
- Definizione delle route tramite **PHP Attributes** (`#[Get]`, `#[Post]`, `#[Put]`, `#[Delete]`)
- Caricamento automatico delle route dai controller via reflection

### Middleware (ispirati a PSR-15)
Sistema di concatenazione middleware, applicabili globalmente o per singola route:
- `AuthMiddleware`: Controllo autenticazione e autorizzazione per ruolo
- `ValidationMiddleware`: Validazione CSRF e campi del body request

### Dependency Injection (ispirato a PSR-11)
Container DI semplice con lazy-loading dei servizi:
- Registrazione di factory per i servizi
- Caching delle istanze create
- Iniezione automatica nel costruttore dei controller

### Rendering Engine
- Sistema di layout (estensione di template base)
- Componenti riutilizzabili (`header`, `sidebar`, `post`, `posts-filter`)
- Layout differenziati: `main-layout`, `admin-layout`, `loggedout-layout`
- Generazione automatica di token CSRF nei template

---

## Sicurezza

### Gestione Segreti
- **Docker Secrets**: Le password MySQL (root e user) sono memorizzate in file separati (`secrets/`) e montate nei container come file in `/run/secrets/`
- Le credenziali non sono mai esposte in variabili d'ambiente o nel `docker-compose.yml`

### Protezione CSRF
- Token CSRF generati con `random_bytes(32)` (256 bit di entropia)
- Scadenza token: 1 ora
- Supporto per token single-use e multi-use
- Validazione timing-safe con `hash_equals()`
- Garbage collection probabilistica dei token scaduti (2% delle richieste)

### Gestione Sessioni
- Nome sessione custom (`UNIBOSTU_SESSID`)
- Rigenerazione periodica dell'ID sessione ogni 30 minuti
- Finestra di tolleranza di 5 minuti per supportare utenti su reti lente che permette di mantenere la sessione attiva durante la rigenerazione
- Validazione User-Agent per rilevare session hijacking
- Cookie `httponly` e `use_only_cookies` abilitati
- Garbage collection esterna tramite container `cron` dedicato, disabilitata in PHP (`gc_probability = 0`)

### Hashing Password
- Algoritmo: `bcrypt` (`PASSWORD_BCRYPT`)
- Verifica con `password_verify()`

### Protezione SQL Injection
- Tutte le query utilizzano **prepared statements** con PDO
- Binding esplicito dei parametri con tipo (`PDO::PARAM_STR`, `PDO::PARAM_INT`, `PDO::PARAM_BOOL`)

### Protezione XSS
- Funzione helper `h()` per escape HTML (`htmlspecialchars` con `ENT_QUOTES` e `UTF-8`)

### Upload File
- Validazione lato server di:
  - Dimensione massima: 10 MB per file
  - Massimo 5 file per post
  - Estensioni consentite: pdf, doc, docx, txt, jpg, jpeg, png, gif, zip, rar
  - MIME type verificato con `finfo` (ulteriore controllo oltre all'estensione)
- Nomi file sanitizzati: generazione UUID con timestamp + random bytes
- Storage fuori dalla document root (`/var/uploads/`, non accessibile direttamente)
- Serve dei file tramite controller con validazione (no path traversal)

### Controllo Accessi
- Sistema di ruoli: `ADMIN`, `USER`, `GUEST`
- Autenticazione separata per utenti e amministratori
- Ogni richiesta protetta da middleware di autorizzazione

### Volume Read-Only
- Il volume del codice sorgente (`./apache/www:/var/www/html`) e' montato in sola lettura (`:ro`)
- I file uploadati sono salvati in un volume separato (`uploads_data`)

---

## Database

### Schema
Tabelle principali:
- `users`: Utenti con supporto sospensione
- `administrators`: Amministratori (tabella separata)
- `posts`: Post con foreign key a utente, corso, categoria
- `comments`: Commenti con supporto risposte annidate (self-referencing)
- `categories`, `tags`, `courses`, `faculties`: Entita' di classificazione
- `post_tags`: Relazione many-to-many post-tag
- `likes`: Sistema di like/dislike
- `user_courses`: Iscrizione utenti ai corsi
- `post_attachments`: Allegati dei post

---

## Gestione Errori

### Sistema di Eccezioni Custom
- `DomainException`: Errori di dominio
- `ValidationException`: Errori di validazione
- `RepositoryException`: Errori del repository (DB)
- Enum `ValidationErrorCode` e `DomainErrorCode` per codici errore tipizzati, il frontend può usarli per mostrare messaggi specifici

---

## Frontend

### JavaScript
- Moduli ES6 con import/export
- Classe `Form` per gestione submission form con:
  - Validazione client-side
  - Supporto multipart/form-data per upload
  - Mapping errori client o server su campi specifici
  - Gestione CSRF token automatica

---

## Funzionalita' Applicative

### Utenti
- Registrazione con validazione
- Login/Logout con gestione sessione
- Profilo utente modificabile
- Cambio password con verifica password corrente
- Sospensione utenti

### Post
- Creazione con titolo, descrizione, corso, categoria, tag
- Upload allegati multipli
- Sistema like/dislike
- Eliminazione (solo autore o admin)
- Filtri per categoria e ordinamento

### Commenti
- Commenti sui post
- Risposte annidate
- Soft delete

### Amministrazione
- Dashboard admin separata
- Gestione utenti (visualizzazione, sospensione)
- Gestione categorie, facoltà, corsi, tag
- Accesso a tutti i post e commenti

---

## Containerizzazione

### Healthcheck
- MySQL healthcheck con `mysqladmin ping`
- Retry automatico con interval 2s, timeout 2s, 10 retry
- Start period di 30s per avvio iniziale

### Persistenza
- Volume `db_data`: Dati MySQL
- Volume `php_sessions`: File di sessione (condiviso con container gc)
- Volume `uploads_data`: File caricati dagli utenti

### Session Garbage Collection
- Container Alpine dedicato con cron
- Script bash che rimuove sessioni scadute basandosi su mtime
- Configurabile via variabili d'ambiente (`SESSION_MAX_LIFETIME`, `CRON_SCHEDULE`)
- Log solo quando vengono effettivamente rimosse sessioni
