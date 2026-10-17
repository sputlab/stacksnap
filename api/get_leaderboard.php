<?php
/**
 * StackSnap · GET /api/get_leaderboard.php
 * Returns global leaderboard scores ordered from highest to lowest.
 * Ties share the same dense rank (e.g., 100, 100, 90 -> ranks 1, 1, 2).
 *
 * Optional query parameter:
 *   ?limit=100 (limits number of returned scores; default returns all)
 *
 * Response: [ { "nickname": "Mario", "score": 1234, "blocks": 87, "rank": 1 }, ... ]
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

// Lightweight HTTP caching (5 seconds fresh, 15 seconds stale-while-revalidate)
header('Cache-Control: public, max-age=5, stale-while-revalidate=15');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Metodo non consentito']);
    exit;
}

try {
    $pdo = stacksnap_db();

    // Fast indexed query: scores pre-sorted descending, ties sorted chronologically
    $sql = '
        SELECT nickname, score, blocks, created_at
        FROM stacksnap_scores
        ORDER BY score DESC, created_at ASC
    ';

    if (isset($_GET['limit']) && is_numeric($_GET['limit']) && (int)$_GET['limit'] > 0) {
        $sql .= ' LIMIT ' . (int)$_GET['limit'];
    }

    $rows = $pdo->query($sql)->fetchAll();

    // Compute dense rank in a single O(N) pass in PHP:
    // Identical scores get the same rank; subsequent lower scores increment rank by 1 without gaps.
    $out = [];
    $currentRank = 0;
    $prevScore = null;

    foreach ($rows as $r) {
        $score = (int) $r['score'];
        if ($prevScore === null || $score < $prevScore) {
            $currentRank++;
            $prevScore = $score;
        }

        $out[] = [
            'nickname' => $r['nickname'],
            'score' => $score,
            'blocks' => (int) $r['blocks'],
            'rank' => $currentRank,
        ];
    }

    echo json_encode($out);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Errore del server']);
}
