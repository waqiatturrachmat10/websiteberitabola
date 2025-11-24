<?php
require_once 'db.php';
header('Content-Type: application/json');
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 5;
$stmt = $pdo->prepare("SELECT a.title,a.slug,a.image_url,c.name as cat FROM articles a JOIN categories c ON a.category_id=c.id WHERE a.status='published' ORDER BY a.created_at DESC LIMIT 4 OFFSET :offset");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT); $stmt->execute();
$data = $stmt->fetchAll();
foreach($data as &$d) $d['image_url'] = getImageUrl($d['image_url']);
echo json_encode($data);
?>