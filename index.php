<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'movies_db');

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$logged_in = false;
$user_id = null;
$username = '';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];
    $logged_in = true;
}

// Обработка выхода
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Оценка фильмов</title>
<!-- Ваши стили -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
<style>
/* Ваш CSS изначально, который вы предоставили */
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #f0f4f8, #d9e2ec);
  margin: 0;
  padding: 40px;
  color: #333;
  line-height: 1.6;
}
/* Остальной CSS остается без изменений */
h1 {
  text-align: center;
  margin-bottom: 30px;
  font-size: 3em;
  background: linear-gradient(90deg, #667eea, #764ba2);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: 600;
  letter-spacing: 2px;
  animation: fadeIn 2s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}
.container {
  max-width: 1200px;
  margin: 0 auto;
}
/* ... остальные стили без изменений ... */

#addMovieForm {
  display: flex;
  gap: 15px;
  justify-content: center;
  flex-wrap: wrap;
  background: #fff;
  padding: 25px;
  border-radius: 20px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
  margin-bottom: 50px;
  animation: fadeInUp 1s ease forwards;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
#addMovieForm input {
  flex: 1 1 200px;
  padding: 16px 20px;
  border: 2px solid #ccc;
  border-radius: 12px;
  font-family: 'Poppins', sans-serif;
  font-size: 1.1em;
  transition: border-color 0.3s, box-shadow 0.3s;
}
#addMovieForm input:focus {
  border-color: #667eea;
  box-shadow: 0 0 8px rgba(102, 126, 234, 0.4);
  outline: none;
}
#addMovieForm button {
  padding: 16px 24px;
  background: linear-gradient(45deg, #667eea, #764ba2);
  color: #fff;
  border: none;
  border-radius: 12px;
  font-family: 'Poppins', sans-serif;
  font-size: 1.2em;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}
#addMovieForm button:hover {
  background: linear-gradient(45deg, #764ba2, #667eea);
  transform: scale(1.05);
}
#moviesContainer {
  margin-top: 60px;
}
.movie {
  background: #fff;
  padding: 30px 25px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.07);
  transition: box-shadow 0.3s, transform 0.3s;
  margin-bottom: 25px;
}
.movie:hover {
  box-shadow: 0 15px 30px rgba(0,0,0,0.15);
  transform: translateY(-8px);
}
.movie h2 {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  font-size: 2em;
  background: linear-gradient(90deg, #ff7e5f, #feb47b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: 600;
}
.average {
  font-size: 1.5em;
  font-weight: 700;
  color: #2c3e50;
  margin-top: 15px;
  text-align: center;
  font-family: 'Poppins', sans-serif;
  animation: pulse 1.5s infinite;
}
@keyframes pulse {
  0% { color: #2c3e50; }
  50% { color: #e74c3c; }
  100% { color: #2c3e50; }
}
.criteria {
  margin-top: 25px;
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
}
.criterion {
  flex: 1 1 200px;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  background: #fefefe;
  padding: 15px;
  border-radius: 15px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.05);
  transition: transform 0.3s, box-shadow 0.3s;
}
.criterion:hover {
  box-shadow: 0 12px 24px rgba(0,0,0,0.1);
  transform: translateY(-4px);
}
.criterion label {
  font-weight: 600;
  margin-bottom: 8px;
  font-size: 1.1em;
  color: #444;
}
.criterion input[type="range"] {
  width: 100%;
}
.save-scores {
  margin-top: 15px;
  padding: 16px 30px;
  background: linear-gradient(135deg, #2ecc71, #27ae60);
  color: #fff;
  border: none;
  border-radius: 15px;
  font-family: 'Poppins', sans-serif;
  font-size: 1.2em;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}
.save-scores:hover {
  background: linear-gradient(135deg, #27ae60, #2ecc71);
  transform: scale(1.05);
}
#filmsList {
  margin-top: 60px;
  background: linear-gradient(135deg, #f6d365, #fda085);
  padding: 40px;
  border-radius: 30px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}
#filmsList h2 {
  text-align: center;
  font-size: 2.5em;
  margin-bottom: 30px;
  background: linear-gradient(90deg, #ff7e5f, #feb47b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: fadeIn 2s ease-in-out;
}
#sortButtons {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-bottom: 40px;
}
.sort-btn {
  padding: 14px 30px;
  font-size: 1.2em;
  font-weight: 700;
  border: none;
  border-radius: 20px;
  cursor: pointer;
  background: linear-gradient(45deg, #6a11cb, #2575fc);
  color: #fff;
  box-shadow: 0 8px 16px rgba(0,0,0,0.2);
  transition: all 0.3s;
}
.sort-btn:hover {
  background: linear-gradient(45deg, #2575fc, #6a11cb);
  transform: scale(1.05);
}
.film-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  margin-bottom: 15px;
  background: rgba(255,255,255,0.7);
  border-radius: 15px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.05);
}
.film-item:hover {
  background: rgba(255,255,255,0.9);
  transform: translateY(-4px);
}
.film-info {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.film-title {
  font-weight: 700;
  font-size: 1.4em;
  color: #2c3e50;
}
.film-score {
  font-size: 1em;
  color: #555;
}
#clearAllBtn {
  display: block;
  margin: 30px auto 0;
  padding: 14px 30px;
  background: linear-gradient(135deg, #e74c3c, #c0392b);
  color: #fff;
  border: none;
  border-radius: 20px;
  font-family: 'Poppins', sans-serif;
  font-size: 1.2em;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}
#clearAllBtn:hover {
  background: linear-gradient(135deg, #c0392b, #e74c3c);
  transform: scale(1.05);
}
/* Кастомизация range */
/* Стиль для всех range-слайдеров */
input[type=range] {
  -webkit-appearance: none; /* Убираем стандартный стиль */
  width: 200px; /* Увеличенная ширина для удобства */
  height: 8px;
  background: #ddd;
  border-radius: 5px;
  outline: none;
  transition: background 0.3s;
  cursor: pointer;
  margin: 10px 0;
}

/* Внешний вид ползунка для WebKit (Chrome, Safari) */
input[type=range]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 24px;
  height: 24px;
  background: #4CAF50; /* Зеленый цвет для ручки */
  border-radius: 50%;
  box-shadow: 0 0 4px rgba(0,0,0,0.3);
  cursor: pointer;
  transition: background 0.3s, transform 0.2s;
  margin-top: -4px; /* чтобы по центру было */
}

input[type=range]::-webkit-slider-thumb:hover {
  background: #45a049;
  transform: scale(1.1);
}

/* Внешний вид ползунка для Mozilla Firefox */
input[type=range]::-moz-range-thumb {
  width: 24px;
  height: 24px;
  background: #4CAF50;
  border: none;
  border-radius: 50%;
  box-shadow: 0 0 4px rgba(0,0,0,0.3);
  cursor: pointer;
  transition: background 0.3s, transform 0.2s;
}

input[type=range]::-moz-range-thumb:hover {
  background: #45a049;
  transform: scale(1.1);
}

/* Можно добавить еще эффект при фокусе, если нужно */
input[type=range]:focus {
  outline: none;
  box-shadow: 0 0 4px #4CAF50;
}
/* Кнопка удаления */
.delete-btn {
  background: transparent;
  border: none;
  font-size: 1.4em;
  cursor: pointer;
  transition: transform 0.2s, color 0.2s;
  color: #c0392b;
  padding: 8px;
  border-radius: 50%;
}
.delete-btn:hover {
  color: #e74c3c;
  transform: scale(1.2);
  background: rgba(0,0,0,0.05);
}
/* Формы входа/регистрации */
.form-container {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  justify-content: center;
  margin-bottom: 30px;
}
.auth-form {
  background: #fff;
  padding: 55px;
  border-radius: 15px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
  max-width: 400px;
  width: 100%;
  transition: transform 0.2s, box-shadow 0.2s;
}
.auth-form:hover {
  box-shadow: 0 12px 24px rgba(0,0,0,0.2);
  transform: translateY(-2px);
}
.auth-form h2 {
  text-align: center;
  margin-bottom: 20px;
  font-size: 2em;
  color: #333;
}
.auth-form input {
  width: 100%;
  padding: 15px 10px;
  margin-bottom: 15px;
  border: 2px solid #ccc;
  border-radius: 10px;
  font-family: 'Poppins', sans-serif;
  font-size: 1em;
  transition: border-color 0.3s, box-shadow 0.3s;
  width: 375px;
}
.auth-form input:focus {
  border-color: #667eea;
  box-shadow: 0 0 8px rgba(102, 126, 234, 0.2);
  outline: none;
}
.auth-form button {
  width: 100%;
  padding: 14px;
  background: linear-gradient(45deg, #667eea, #764ba2);
  color: #fff;
  border: none;
  border-radius: 12px;
  font-family: 'Poppins', sans-serif;
  font-size: 1.2em;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}
.auth-form button:hover {
  background: linear-gradient(45deg, #764ba2, #667eea);
  transform: scale(1.05);
}
/* Стиль для приветствия */
.user-greeting {
  font-family: 'Poppins', sans-serif;
  font-size: 1.8em;
  display: flex;
  align-items: center;
  gap: 15px;
  justify-content: center;
  margin-bottom: 30px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  padding: 15px 25px;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  color: #fff;
  font-weight: 600;
}
.user-greeting .username {
  color: #ffd700; /* яркий цвет для имени */
  font-weight: 700;
}
.logout-btn {
  padding: 8px 16px;
  background: linear-gradient(135deg, #f7971e, #ffd200);
  color: #fff;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  transition: background 0.3s, transform 0.2s;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.logout-btn:hover {
  background: linear-gradient(135deg, #feb47b, #f7971e);
  transform: scale(1.05);
}
/* Плавное появление элементов */
h1, #filmsList, #moviesContainer, .movie, .criteria, .auth-form {
  transition: all 0.3s ease;
}
/* Hover-эффекты */
.sort-btn:hover, .delete-btn:hover, #clearAllBtn:hover, .theme-btn:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>
</head>
<!-- авторизованный пользователь -->
<p class="user-greeting">
  Здравствуйте, <strong class="username"><?php echo htmlspecialchars($username); ?></strong>!
  <a href="?logout=1" class="logout-btn">Выйти</a>
</p>
<body>
<div class="container">
<h1>🎬 Оценка фильмов</h1>

<?php if (!$logged_in): ?>
  <!-- Форма входа и регистрации -->
  <div class="form-container">
  <form method="POST" action="login.php" class="auth-form">
    <h2>Войти</h2>
    <input type="text" name="username" placeholder="Имя" required />
    <input type="password" name="password" placeholder="Пароль" required />
    <button type="submit">Войти</button>
  </form>
  <form method="POST" action="register.php" class="auth-form">
    <h2>Регистрация</h2>
    <input type="text" name="username" placeholder="Имя" required />
    <input type="password" name="password" placeholder="Пароль" required />
    <button type="submit">Зарегистрироваться</button>
  </form>
</div>
<?php else: ?>
  

  <!-- интерфейс -->
  <form id="addMovieForm" style="margin-bottom:50px; display:flex; align-items:center; gap:15px;">
    <input type="text" id="movieTitle" placeholder="Введите название фильма" required />
    <button id="dobav" type="submit">➕ Добавить фильм</button>
  </form>
  <button id="clearAllBtn">🧹 Очистить список фильмов</button>
  <div id="moviesContainer"></div>

  <div id="filmsList">
    <h2>📋 Список фильмов и средняя оценка</h2>
    <div id="sortButtons">
      <button id="sortByRating" class="sort-btn">⭐ По рейтингу</button>
      <button id="sortByDate" class="sort-btn">🗓️ По дате добавления</button>
    </div>
    <div id="films"></div>
  </div>
<?php endif; ?>

<script>
  const btn = document.getElementById('dobav');
btn.onclick = () => {
  document.getElementById('moviesContainer').style.display = 'block';
};
const criteria = [
  { name: 'Актерская игра', key: 'acting' },
  { name: 'Сюжет', key: 'plot' },
  { name: 'Эффекты', key: 'effects' },
  { name: 'Атмосфера', key: 'atmosphere' },
  { name: 'Оригинальность', key: 'originality' }
];

let movies = [];
let currentSort = 'rating';

const userId = <?php echo json_encode($user_id); ?>;

// Загрузка фильмов
async function loadMovies() {
  const res = await fetch('load_movies.php');
  movies = await res.json();
  renderMovies();
  updateFilmsList();
}

// Сохранение нового фильма
async function saveMovie(title) {
  const res = await fetch('save_movie.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ title })
  });
  const data = await res.json();
  if (data.success) {
    await loadMovies();
    // после загрузки берём последний добавленный фильм
    const lastMovie = movies[movies.length - 1];
    showEvaluationForm(lastMovie);
  }
}

// Удаление фильма
async function deleteMovie(id) {
  await fetch('delete_movie.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id })
  });
  await loadMovies();
}

// Сохранение оценок
async function saveScores(movieId, scores) {
  await fetch('save_scores.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ movieId, scores })
  });
  await loadMovies();
}


// Обработчик добавления фильма
document.getElementById('addMovieForm').addEventListener('submit', e => {
  e.preventDefault();
  const title = document.getElementById('movieTitle').value.trim();
  if (title && userId) {
    saveMovie(title);
    document.getElementById('movieTitle').value = '';
  }
});

// Обработчик очистки списка
document.getElementById('clearAllBtn').addEventListener('click', () => {
  if (confirm('Вы уверены, что хотите очистить весь список фильмов?')) {
    fetch('clear_movies.php').then(() => {
      loadMovies();
    });
  }
});

// Обработка сортировки
document.getElementById('sortByRating').addEventListener('click', () => {
  currentSort = 'rating';
  updateFilmsList();
});
document.getElementById('sortByDate').addEventListener('click', () => {
  currentSort = 'date';
  updateFilmsList();
});

// Обновление списка фильмов
async function updateFilmsList() {
  if (!movies.length) {
    document.getElementById('films').innerHTML = '<p style="text-align:center;">Нет оцененных фильмов.</p>';
    return;
  }
  let sorted = [...movies];
  if (currentSort === 'rating') {
    sorted.sort((a,b) => b.average - a.average);
  } else {
    sorted.sort((a,b) => b.dateAdded - a.dateAdded);
  }

  const container = document.getElementById('films');
  container.innerHTML = '';
  for (const f of sorted) {
    const div = document.createElement('div');
    div.className = 'film-item';

    const info = document.createElement('div');
    info.className = 'film-info';

    const title = document.createElement('div');
    title.className = 'film-title';
    title.textContent = f.title;

    const score = document.createElement('div');
    score.className = 'film-score';
    score.textContent = `Средняя оценка: ${f.average}`;

    info.appendChild(title);
    info.appendChild(score);

    const delBtn = document.createElement('button');
    delBtn.className = 'delete-btn';
    delBtn.textContent = '🗑️';
    delBtn.title = 'Удалить фильм';
    delBtn.onclick = () => {
      fetch('delete_movie.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: f.id })
      }).then(() => loadMovies());
    };

    div.appendChild(info);
    div.appendChild(delBtn);
    container.appendChild(div);
  }
}

// Отрисовка фильмов и добавление формы оценки
function renderMovies() {
  const container = document.getElementById('moviesContainer');
  container.innerHTML = '';

  movies.forEach((movie, index) => {
    const movieDiv = document.createElement('div');
    movieDiv.className = 'movie';

    const titleH2 = document.createElement('h2');
    const titleSpan = document.createElement('span');
    titleSpan.textContent = movie.title;

    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = '🗑️';
    deleteBtn.title = 'Удалить фильм';
    deleteBtn.onclick = () => {
      fetch('delete_movie.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: movie.id })
      }).then(() => loadMovies());
    };

    titleH2.appendChild(titleSpan);
    titleH2.appendChild(deleteBtn);
    movieDiv.appendChild(titleH2);

    if (!movie.estimated) {
      showEvaluationForm(movie);
    } else {
      const infoDiv = document.createElement('div');
      infoDiv.style.display = 'flex';
      infoDiv.style.justifyContent = 'space-between';
      infoDiv.style.alignItems = 'center';

      const avgText = document.createElement('div');
      avgText.className = 'average';
      avgText.textContent = `Средняя оценка: ${movie.average}`;

      const deleteBtn2 = document.createElement('button');
      deleteBtn2.className = 'delete-btn';
      deleteBtn2.textContent = '🗑️';
      deleteBtn2.title = 'Удалить фильм';
      deleteBtn2.onclick = () => {
        fetch('delete_movie.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: movie.id })
        }).then(() => loadMovies());
      };

      infoDiv.appendChild(avgText);
      infoDiv.appendChild(deleteBtn2);
      movieDiv.appendChild(infoDiv);
    }

    container.appendChild(movieDiv);
  });
}

// Функция показывать форму оценки
// Создаем блок формы оценки для фильма
function showEvaluationForm(movie) {
  // Проверка, есть ли уже форма
  const existingForm = document.querySelector('.evaluation-form-' + movie.id);
  if (existingForm) return;

  const container = document.getElementById('moviesContainer');

  const formDiv = document.createElement('div');
  formDiv.className = 'evaluation-form evaluation-form-' + movie.id;
  formDiv.style.border = '1px solid #ccc';
  formDiv.style.padding = '15px';
  formDiv.style.marginTop = '15px';
  formDiv.style.borderRadius = '10px';
  formDiv.style.backgroundColor = '#f9f9f9';

  const h = document.createElement('h3');
  h.textContent = `Оценка для "${movie.title}"`;
  formDiv.appendChild(h);

  const criteriaDiv = document.createElement('div');
  criteriaDiv.className = 'criteria';

  // Создаем критерии
  criteria.forEach((crit, i) => {
    const critDiv = document.createElement('div');
    critDiv.className = 'criterion';

    const label = document.createElement('label');
    label.textContent = crit.name;

    const rangeInput = document.createElement('input');
    rangeInput.type = 'range';
    rangeInput.min = 1;
    rangeInput.max = 10;
    rangeInput.value = 5;

    const scoreSpan = document.createElement('span');
    scoreSpan.className = 'rating';
    scoreSpan.textContent = rangeInput.value;

    rangeInput.oninput = () => {
      scoreSpan.textContent = rangeInput.value;
    };

    critDiv.appendChild(label);
    critDiv.appendChild(rangeInput);
    critDiv.appendChild(scoreSpan);
    criteriaDiv.appendChild(critDiv);
  });

  // Создаем блок "Общее впечатление"
  const impressionDiv = document.createElement('div');
  impressionDiv.className = 'criterion';

  const labelImpression = document.createElement('label');
  labelImpression.textContent = 'Общее впечатление (1-10)';
  const rangeImpression = document.createElement('input');
  rangeImpression.type = 'range';
  rangeImpression.min = 1;
  rangeImpression.max = 10;
  rangeImpression.value = 5;

  const scoreSpanImp = document.createElement('span');
  scoreSpanImp.className = 'rating';
  scoreSpanImp.textContent = rangeImpression.value;

  rangeImpression.oninput = () => {
    scoreSpanImp.textContent = rangeImpression.value;
  };

  impressionDiv.appendChild(labelImpression);
  impressionDiv.appendChild(rangeImpression);
  impressionDiv.appendChild(scoreSpanImp);

  // Кнопка сохранить
  const saveBtn = document.createElement('button');
  saveBtn.textContent = '✅ Сохранить оценки';
  saveBtn.className = 'save-scores';

  saveBtn.onclick = () => {
    const scores = {};
    let sumOtherScores = 0;
    criteria.forEach((c, i) => {
      const val = parseInt(criteriaDiv.children[i].querySelector('input[type=range]').value);
      scores[c.key] = val;
      sumOtherScores += val;
    });
    const impressionRating = parseInt(rangeImpression.value); // 1-10
    scores.impression = impressionRating;

    // Вычисляем коэффициент: 1.1 для 1, до 2.0 для 10
    const coefficient = 1 + 0.1 * (impressionRating - 1);

    // Итоговая оценка
    const finalScore = sumOtherScores * coefficient;

    fetch('save_scores.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ movieId: movie.id, scores, finalScore })
    }).then(() => {
      loadMovies();
      document.getElementById('moviesContainer').style.display = 'none';
    });
  };

  formDiv.appendChild(criteriaDiv);
  formDiv.appendChild(impressionDiv);
  formDiv.appendChild(saveBtn);

  // Вставляем под фильм
  const movieElems = document.querySelectorAll('.movie');
  for (let m of movieElems) {
    const titleSpan = m.querySelector('h2 span');
    if (titleSpan && titleSpan.textContent === movie.title) {
      m.appendChild(formDiv);
      break;
    }
  }
}

// Инициализация
if (userId) {
  loadMovies();
}
</script>
</div>
</body>
</html>