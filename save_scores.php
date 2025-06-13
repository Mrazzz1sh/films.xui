<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Не авторизован']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$movieId = intval($data['movieId']);
$scores = $data['scores'];

// Подключение к базе данных
$conn = new mysqli('localhost', 'root', '', 'movies_db');
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Обновляем или вставляем оценки
foreach ($scores as $key => $score) {
    // Проверка существования оценки
    $res = $conn->query("SELECT id FROM scores WHERE movie_id=$movieId AND criterion_key='$key'");
    if ($res->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE scores SET score=? WHERE movie_id=? AND criterion_key=?");
        $stmt->bind_param("iis", $score, $movieId, $key);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO scores (movie_id, criterion_key, score) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $movieId, $key, $score);
        $stmt->execute();
        $stmt->close();
    }
}

// После сохранения оценок помечаем фильм как оценённый
$stmt = $conn->prepare("UPDATE movies SET estimated=1 WHERE id=?");
$stmt->bind_param("i", $movieId);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
$conn->close();
// Предположим, что у вас есть переменная $movieId и массив $scores
// Изначально сохраняете оценки, а затем пересчитываете среднее

// В конце скрипта:
$conn->query("UPDATE movies SET average = (
    SELECT AVG(score) FROM scores WHERE movie_id = $movieId
)");
?>
