-- StackSnap · Global Leaderboard
-- Run this script on your MySQL database (via phpMyAdmin SQL tab or mysql CLI):
-- mysql -u username -p database_name < db_setup.sql

CREATE TABLE IF NOT EXISTS stacksnap_scores (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nickname   VARCHAR(16)  NOT NULL,
    score      INT UNSIGNED NOT NULL,
    blocks     INT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_score_created (score DESC, created_at ASC),
    KEY idx_score (score DESC),
    KEY idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: All submitted scores are permanently stored in this table.
-- The local reset button in the game only affects browser localStorage.
