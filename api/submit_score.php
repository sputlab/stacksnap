<?php
/**
 * StackSnap · POST /api/submit_score.php
 * Expected JSON body: { "nickname": "Mario", "score": 1234, "blocks": 87 }
 * Response: { "ok": true, "rank": 12, "totalPlayers": 340 }
 *
 * Client score validation:
 * Validates maximum bounds (MAX_SCORE, MAX_BLOCKS) to prevent absurd leaderboard entries.
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Metodo non consentito']);
    exit;
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

if ($score < 0 || $score > MAX_SCORE || $blocks < 0 || $blocks > MAX_BLOCKS) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Punteggio non valido']);
    exit;
}

try {
    $pdo = stacksnap_db();

    $stmt = $pdo->prepare('INSERT INTO stacksnap_scores (nickname, score, blocks) VALUES (:nickname, :score, :blocks)');
    $stmt->execute(['nickname' => $nickname, 'score' => $score, 'blocks' => $blocks]);

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
