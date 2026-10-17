-- StackSnap · Global Leaderboard
-- Esegui questo script una sola volta sul tuo database MySQL
-- (es. da phpMyAdmin -> scheda "SQL", oppure da riga di comando: mysql -u utente -p nome_db < db_setup.sql)

CREATE TABLE IF NOT EXISTS stacksnap_scores (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nickname   VARCHAR(16)  NOT NULL,
    score      INT UNSIGNED NOT NULL,
    blocks     INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_score (score DESC),
    KEY idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nota: la tabella non ha limiti di righe: ogni partita salvata resta per sempre
-- (la classifica globale non viene mai svuotata dal pulsante "reset" del gioco,
-- che agisce solo sui dati salvati in locale nel browser del giocatore).
