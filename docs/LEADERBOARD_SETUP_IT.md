# StackSnap · Guida Backend Classifica Globale (Italiano)

Questi file implementano la classifica globale del gioco usando PHP + MySQL.
Non richiedono librerie esterne: solo PHP con estensione PDO/MySQL (presente
nella quasi totalità dei servizi di web hosting).

---

## 1. Crea il database

Importa il file `db_setup.sql` nel tuo database MySQL (una sola volta):

```bash
mysql -u utente -p nome_del_database < db_setup.sql
```

oppure apri **phpMyAdmin**, seleziona il tuo database e incolla il contenuto nella scheda **SQL**.

---

## 2. Configura le credenziali

1. Copia il file `config.example.php` in `config.php`:
   ```bash
   cp config.example.php config.php
   ```
2. Apri `config.php` e compila le credenziali del database:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'nome_del_tuo_database');
   define('DB_USER', 'utente_mysql');
   define('DB_PASS', 'password_mysql');
   ```
3. Se vuoi restringere l'accesso all'API al solo tuo sito, configura l'origine CORS:
   ```php
   define('ALLOWED_ORIGIN', 'https://tuosito.com');
   ```

> **Sicurezza**: il file `config.php` è inserito in `.gitignore` e non deve mai essere caricato su repository pubblici.

---

## 3. Carica i file sul server

Carica l'intera cartella `api/` (insieme a `config.php` nella cartella superiore o nella root) sul tuo hosting via FTP o pannello di controllo.

Verifica che siano raggiungibili via browser:
- `https://tuosito.com/api/get_leaderboard.php`
- `https://tuosito.com/api/submit_score.php`

---

## 4. Collega il gioco al backend

Apri `index.html` del gioco, cerca la riga:

```javascript
const API_BASE_URL = 'api';
```

Se l'API è ospitata sullo stesso dominio/cartella, `'api'` funziona immediatamente. Se l'API risiede su un dominio o sottodominio diverso, specifica l'URL completo (senza slash finale):

```javascript
const API_BASE_URL = 'https://tuosito.com/api';
```

---

## Note tecniche

- **Persistenza**: La tabella `stacksnap_scores` conserva ogni partita registrata con "SAVE". Il pulsante di reset nel gioco cancella unicamente i dati memorizzati in locale nel browser (`localStorage`).
- **Dense Ranking**: A parità di punteggio, i giocatori condividono la stessa posizione in classifica (es. due punteggi da 500 sono entrambi #2 se il primo posto è 900).
- **Validazione**: Lo script `submit_score.php` valida i campi e verifica che il punteggio rispetti limiti ragionevoli (`MAX_SCORE`, `MAX_BLOCKS`).
