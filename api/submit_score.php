<?php
/**
 * StackSnap · POST /api/submit_score.php
 * Expected JSON body: { "nickname": "Mario", "score": 1234, "blocks": 87 }
 * Response: { "ok": true, "rank": 12, "totalPlayers": 340 }
 *
 * Security & Anti-Abuse:
 * - Max bounds validation (MAX_SCORE, MAX_BLOCKS).
 * - Score-to-blocks mathematical plausibility check.
 * - Lightweight IP cooldown rate limiter.
 */

if (!file_exists(__DIR__ . '/../config.php')) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'Database configuration file missing. Please copy config.example.php to config.php and set your MySQL credentials.'
    ]);
    exit;
}

require_once __DIR__ . '/../config.php';
stacksnap_cors_and_json();

const MAX_SCORE = 999999;
const MAX_BLOCKS = 50000;
const IP_COOLDOWN_SECONDS = 3;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Metodo non consentito']);
    exit;
}

// Lightweight IP cooldown protection against automated flood scripts
$rawIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$clientIp = trim(explode(',', $rawIp)[0]);
$rateLockFile = sys_get_temp_dir() . '/stacksnap_cooldown_' . md5($clientIp) . '.lock';

if (file_exists($rateLockFile)) {
    $lastSubmit = (int) @file_get_contents($rateLockFile);
    if ((time() - $lastSubmit) < IP_COOLDOWN_SECONDS) {
        http_response_code(429);
        echo json_encode(['ok' => false, 'error' => 'Attendere qualche secondo prima di salvare di nuovo']);
        exit;
    }
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'JSON non valido']);
    exit;
}

$nickname = isset($data['nickname']) ? trim(strip_tags((string) $data['nickname'])) : '';
$score    = isset($data['score']) ? (int) $data['score'] : -1;
$blocks   = isset($data['blocks']) ? (int) $data['blocks'] : 0;

if ($nickname === '') $nickname = 'Anonymous';
$nickname = mb_substr($nickname, 0, 16);

// Basic bounds check
if ($score < 0 || $score > MAX_SCORE || $blocks < 0 || $blocks > MAX_BLOCKS) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Punteggio o blocchi non validi']);
    exit;
}

// Mathematical plausibility check (prevents submitting 1 block with 900k points)
if ($score > 0 && $blocks === 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Punteggio non plausibile']);
    exit;
}
$maxTheoreticalScore = max(500, ($blocks * 350) + 1000);
if ($score > $maxTheoreticalScore) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Punteggio non coerente con i blocchi piazzati']);
    exit;
}

try {
    $pdo = stacksnap_db();

    $stmt = $pdo->prepare('INSERT INTO stacksnap_scores (nickname, score, blocks) VALUES (:nickname, :score, :blocks)');
    $stmt->execute(['nickname' => $nickname, 'score' => $score, 'blocks' => $blocks]);

    // Update IP rate limiter timestamp
    @file_put_contents($rateLockFile, (string) time(), LOCK_EX);

    // Rank: count distinct strictly higher scores, + 1 (dense rank)
    $rankStmt = $pdo->prepare(
        'SELECT COUNT(DISTINCT score) + 1 AS rnk FROM stacksnap_scores WHERE score > :score'
    );
    $rankStmt->execute(['score' => $score]);
    $rank = (int) $rankStmt->fetch()['rnk'];

    $totalStmt = $pdo->query('SELECT COUNT(*) AS c FROM stacksnap_scores');
    $total = (int) $totalStmt->fetch()['c'];

    echo json_encode(['ok' => true, 'rank' => $rank, 'totalPlayers' => $total]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Errore del server']);
}
