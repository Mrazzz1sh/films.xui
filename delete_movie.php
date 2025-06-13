<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit();
}
$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id']);
$conn = new mysqli('localhost', 'root', '', 'movies_db');
$stmt = $conn->prepare("DELETE FROM movies WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $_SESSION['user_id']);
$stmt->execute();
echo json_encode(['success' => true]);
?>