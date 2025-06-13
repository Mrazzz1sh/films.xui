<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'movies_db');

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit();
        } else {
            die("Неверный пароль");
        }
    } else {
        die("Пользователь не найден");
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8" /><title>Вход</title></head>
<body>
<h2>Войти</h2>
<form method="POST">
  <input type="text" name="username" placeholder="Имя" required />
  <br/><br/>
  <input type="password" name="password" placeholder="Пароль" required />
  <br/><br/>
  <button type="submit">Войти</button>
</form>
</body>
</html>