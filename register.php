<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'movies_db');

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Проверка уникальности
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        die("Это имя уже занято. Попробуйте другое.");
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt2 = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt2->bind_param("ss", $username, $hashed);
    $stmt2->execute();

    $_SESSION['user_id'] = $stmt2->insert_id;
    $_SESSION['username'] = $username;
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8" /><title>Регистрация</title></head>
<body>
<h2>Регистрация</h2>
<form method="POST">
  <input type="text" name="username" placeholder="Имя" required />
  <br/><br/>
  <input type="password" name="password" placeholder="Пароль" required />
  <br/><br/>
  <button type="submit">Зарегистрироваться</button>
</form>
</body>
</html>