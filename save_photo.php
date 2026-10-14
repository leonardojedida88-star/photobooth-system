<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['ok'=>false,'message'=>'POST required']); exit;
}
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data['image'])) {
    http_response_code(400); echo json_encode(['ok'=>false,'message'=>'No image was received.']); exit;
}
if (!preg_match('#^data:image/png;base64,#', $data['image'])) {
    http_response_code(415); echo json_encode(['ok'=>false,'message'=>'Only PNG images are accepted.']); exit;
}
$encoded = substr($data['image'], strpos($data['image'], ',') + 1);
$binary = base64_decode($encoded, true);
if ($binary === false || strlen($binary) > 8 * 1024 * 1024) {
    http_response_code(413); echo json_encode(['ok'=>false,'message'=>'Image is invalid or too large (8 MB maximum).']); exit;
}
$info = @getimagesizefromstring($binary);
if (!$info || $info['mime'] !== 'image/png') {
    http_response_code(415); echo json_encode(['ok'=>false,'message'=>'Invalid PNG image.']); exit;
}
$dir = __DIR__ . '/uploads';
if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
    http_response_code(500); echo json_encode(['ok'=>false,'message'=>'Could not create uploads directory.']); exit;
}
$filename = 'strip_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.png';
if (file_put_contents($dir . '/' . $filename, $binary, LOCK_EX) === false) {
    http_response_code(500); echo json_encode(['ok'=>false,'message'=>'Could not save image.']); exit;
}
try {
    $stmt = $pdo->prepare('INSERT INTO photos (filename, created_at) VALUES (?, NOW())');
    $stmt->execute([$filename]);
} catch (Throwable $e) {
    @unlink($dir . '/' . $filename);
    http_response_code(500); echo json_encode(['ok'=>false,'message'=>'Database save failed.']); exit;
}
echo json_encode(['ok'=>true,'filename'=>$filename,'url'=>'uploads/'.$filename]);
