<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit();
}
$data = json_decode(file_get_contents('php://input'), true);
$title = trim($data['title'] ?? '');
if (!$title) {
    echo json_encode(['error' => 'Пустое название']);
    exit();
}
$conn = new mysqli('localhost', 'root', '', 'movies_db');
$stmt = $conn->prepare("INSERT INTO movies (user_id, title, date_added) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $_SESSION['user_id'], $title);
$stmt->execute();
echo json_encode(['success' => true]);
?>