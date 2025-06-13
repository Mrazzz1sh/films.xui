<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'movies_db');

if (!$conn || !isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM movies WHERE user_id=$user_id");
$movies = [];

while ($row = $result->fetch_assoc()) {
    // Получаем оценки
    $scores_res = $conn->query("SELECT * FROM scores WHERE movie_id=" . $row['id']);
    $scores = [];
    while ($s = $scores_res->fetch_assoc()) {
        $scores[$s['criterion_key']] = $s['score'];
    }

    // Определяем коэффициент по впечатлению
    $impressionScore = 5; // по умолчанию
    if (isset($scores['impression'])) {
        $impressionScore = $scores['impression'];
    }
    // карты для коэффициентов
    $impressionCoefficients = [
        1 => 1.1,
        2 => 1.2,
        3 => 1.3,
        4 => 1.4,
        5 => 1.5,
        6 => 1.6,
        7 => 1.7,
        8 => 1.8,
        9 => 1.9,
        10 => 2.0
    
    ];

    $coefficient = isset($impressionCoefficients[$impressionScore]) ? $impressionCoefficients[$impressionScore] : 1.0;

    // Суммируем все оценки критериев, кроме impression
    $sumCriteria = 0;
    foreach ($scores as $k => $v) {
        if ($k != 'impression') {
            $sumCriteria += $v;
        }
    }

    // Итоговая оценка — сумма критериев, умноженная на коэффициент
    $finalScore = $sumCriteria * $coefficient;

    $row['scores'] = $scores;
    $row['average'] = round($finalScore, 2);
    $row['estimated'] = true; // показываем что оценка есть
    $row['dateAdded'] = strtotime($row['date_added']);

    $movies[] = $row;
}
echo json_encode($movies);
?>