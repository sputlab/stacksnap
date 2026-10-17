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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Metodo non consentito']);
    exit;
}

try {
    $pdo = stacksnap_db();

    // Dense rank computed server-side, compatible with all MySQL versions (5.7+ / 8.0+)
    $sql = '
        SELECT
            s.nickname,
            s.score,
            s.blocks,
            s.created_at,
            (SELECT COUNT(DISTINCT s2.score) FROM stacksnap_scores s2 WHERE s2.score > s.score) + 1 AS `rank`
        FROM stacksnap_scores s
        ORDER BY s.score DESC, s.created_at ASC
    ';

    if (isset($_GET['limit']) && is_numeric($_GET['limit']) && (int)$_GET['limit'] > 0) {
        $sql .= ' LIMIT ' . (int)$_GET['limit'];
    }

    $rows = $pdo->query($sql)->fetchAll();

    $out = array_map(function ($r) {
        return [
            'nickname' => $r['nickname'],
            'score' => (int) $r['score'],
            'blocks' => (int) $r['blocks'],
            'rank' => (int) $r['rank'],
        ];
    }, $rows);

    echo json_encode($out);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Errore del server']);
}
