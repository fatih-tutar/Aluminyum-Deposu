<?php
include 'functions/init.php';

if (!isset($_POST['product_id']) || !isset($_POST['factory_id'])) {
    echo json_encode(['status' => false]);
    exit;
}

$productId = (int) $_POST['product_id'];
$factoryId = (int) $_POST['factory_id'];

$stmt = $db->prepare("
    SELECT number 
    FROM mold_numbers 
    WHERE product_id = ? AND factory_id = ?
");

$stmt->execute([$productId, $factoryId]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    echo json_encode([
        'status' => true,
        'number' => $row['number']
    ]);
} else {
    echo json_encode([
        'status' => false,
        'number' => ''
    ]);
}