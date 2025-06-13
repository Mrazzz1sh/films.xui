<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    exit();
}
$conn = new mysqli('localhost', 'root', '', 'movies_db');
$conn->query("DELETE FROM movies WHERE user_id={$_SESSION['user_id']}");
$conn->query("DELETE FROM scores WHERE movie_id NOT IN (SELECT id FROM movies)");
echo 'ok';
?>