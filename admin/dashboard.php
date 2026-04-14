<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Генерация CSRF-токена
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Проверка CSRF-токена для всех POST-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Ошибка CSRF: недействительный токен');
    }
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// CREATE - Добавление новости
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? '';

    if (!empty($title) && !empty($content) && !empty($category)) {
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = uniqid('news_') . '.' . $fileExtension;
                $uploadPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $imagePath = 'uploads/' . $fileName;
                }
            }
        } elseif (!empty($_POST['use_stock_image'])) {
            $imagePath = 'uploads/stock.jpg';
        }

        $stmt = $pdo->prepare("INSERT INTO news (title, content, image, category) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $imagePath, $category]);
    }

    header('Location: dashboard.php?success=created');
    exit;
}

// UPDATE - Редактирование новости
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? '';

    if ($id > 0 && !empty($title) && !empty($content) && !empty($category)) {
        // Получаем текущую картинку
        $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $currentNews = $stmt->fetch();
        $imagePath = $currentNews['image'] ?? null;

        // Обработка загрузки нового изображения
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $fileName = uniqid('news_') . '.' . $fileExtension;
                $uploadPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    // Удаляем старое изображение
                    if ($imagePath && file_exists('../' . $imagePath)) {
                        unlink('../' . $imagePath);
                    }
                    $imagePath = 'uploads/' . $fileName;
                }
            }
        } elseif (!empty($_POST['use_stock_image']) && (!$imagePath || $imagePath === 'uploads/stock.jpg')) {
            // Если отмечено стоковое фото и нет загруженного
            if ($imagePath && $imagePath !== 'uploads/stock.jpg' && file_exists('../' . $imagePath)) {
                unlink('../' . $imagePath);
            }
            $imagePath = 'uploads/stock.jpg';
        }

        $stmt = $pdo->prepare("UPDATE news SET title = ?, content = ?, image = ?, category = ? WHERE id = ?");
        $stmt->execute([$title, $content, $imagePath, $category, $id]);
    }

    header('Location: dashboard.php?success=updated');
    exit;
}

// DELETE - Удаление новости 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['id'] ?? 0);

    if ($id > 0) {
        $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $newsItem = $stmt->fetch();

        if ($newsItem && $newsItem['image']) {
            $imagePath = '../' . $newsItem['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'csrf_token' => $_SESSION['csrf_token']]);
        exit;
    }

    echo json_encode(['success' => false]);
    exit;
}

// Получение новости для редактирования
if (isset($_GET['get_news']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $newsItem = $stmt->fetch();

    if ($newsItem) {
        header('Content-Type: application/json');
        echo json_encode($newsItem);
    } else {
        echo json_encode(['error' => 'Новость не найдена']);
    }
    exit;
}

// ПАГИНАЦИЯ 
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

$totalItems = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

$stmt = $pdo->prepare("SELECT * FROM news ORDER BY published_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$news = $stmt->fetchAll();

$categoryColors = [
    'Низкая' => 'bg-green-100 text-green-800',
    'Средняя' => 'bg-yellow-100 text-yellow-800',
    'Высокая' => 'bg-red-100 text-red-800'
];

$is_admin = true;
$page_title = 'Админ-панель - Бизнес и Право';
include '../includes/header.php';
?>

    <main class="container mx-auto px-3 sm:px-4 py-4 sm:py-8">
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-3 sm:px-4 py-2 sm:py-3 rounded mb-4 sm:mb-6 flex items-center gap-2 text-sm">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <?php
                if ($_GET['success'] === 'created') echo 'Новость успешно добавлена!';
                elseif ($_GET['success'] === 'updated') echo 'Новость успешно обновлена!';
                ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6 mb-6 sm:mb-8">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2" id="formTitle">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Добавить новость</span>
            </h2>

            <form action="" method="POST" enctype="multipart/form-data" id="newsForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="newsId" value="">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div class="sm:col-span-2">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="title">
                            Заголовок новости *
                        </label>
                        <input class="w-full py-2 px-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                               id="title"
                               name="title"
                               type="text"
                               placeholder="Введите заголовок новости"
                               required>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="content">
                            Содержание *
                        </label>
                        <textarea class="w-full py-2 px-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                                  id="content"
                                  name="content"
                                  rows="6"
                                  placeholder="Введите текст новости"
                                  required></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="category">
                            Категория важности *
                        </label>
                        <select class="w-full py-2 px-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                                id="category"
                                name="category"
                                required>
                            <option value="Низкая">Низкая</option>
                            <option value="Средняя">Средняя</option>
                            <option value="Высокая">Высокая</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="image">
                            Картинка новости
                        </label>
                        <input class="w-full py-2 px-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent text-sm"
                               id="image"
                               name="image"
                               type="file"
                               accept="image/*">
                        <div class="mt-3 flex items-center gap-2">
                            <input type="checkbox" id="use_stock_image" name="use_stock_image" value="1"
                                   class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                            <label for="use_stock_image" class="text-xs sm:text-sm text-gray-600 cursor-pointer">
                                Использовать стоковое фото
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1" id="currentImageText"></p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 sm:gap-3 mt-4 sm:mt-6">
                    <button class="bg-primary hover:bg-primary-light text-white font-medium py-2 px-4 sm:px-6 rounded transition flex items-center gap-1.5 sm:gap-2 text-sm"
                            type="submit" id="submitBtn">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Добавить новость</span>
                    </button>
                    <button type="button" onclick="resetForm()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-2 px-4 sm:px-6 rounded transition hidden flex items-center gap-1.5 sm:gap-2 text-sm"
                            id="cancelBtn">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Отмена</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 sm:p-6">
            <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                <span>Все новости</span>
                <span class="text-xs sm:text-sm font-normal text-gray-500">(<?php echo $totalItems; ?>)</span>
            </h2>

            <?php if (empty($news)): ?>
                <div class="text-center py-8 sm:py-12">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-gray-500 text-base sm:text-lg">Новостей пока нет</p>
                </div>
            <?php else: ?>
                <div class="space-y-3 sm:space-y-4">
                    <?php foreach ($news as $item): ?>
                        <div class="border border-gray-200 rounded-lg p-3 sm:p-4 hover:shadow-md transition">
                            <div class="flex items-start gap-3 sm:gap-4">
                                <?php if ($item['image']): ?>
                                    <img src="../<?php echo htmlspecialchars($item['image']); ?>"
                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                         class="w-16 h-16 sm:w-24 sm:h-24 object-cover rounded flex-shrink-0">
                                <?php endif; ?>

                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2 gap-2">
                                        <h3 class="text-sm sm:text-lg font-semibold text-gray-800 truncate">
                                            <?php echo htmlspecialchars($item['title']); ?>
                                        </h3>
                                        <span class="px-2 sm:px-3 py-1 text-[10px] sm:text-xs font-semibold rounded flex-shrink-0 <?php echo $categoryColors[$item['category']]; ?>">
                                            <?php echo htmlspecialchars($item['category']); ?>
                                        </span>
                                    </div>
                                    <p class="text-gray-600 text-xs sm:text-sm mb-2 line-clamp-2">
                                        <?php echo htmlspecialchars(mb_substr($item['content'], 0, 120, 'UTF-8')) . '...'; ?>
                                    </p>
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                        <span class="text-xs sm:text-sm text-gray-500 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <?php echo date('d.m.Y H:i', strtotime($item['published_at'])); ?>
                                        </span>
                                        <div class="flex items-center gap-2 sm:gap-3">
                                            <button onclick="editNews(<?php echo $item['id']; ?>)"
                                                    class="text-primary hover:text-primary-light text-xs sm:text-sm font-medium flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                <span class="hidden sm:inline">Редактировать</span>
                                                <span class="sm:hidden">Ред.</span>
                                            </button>
                                            <button onclick="deleteNews(<?php echo $item['id']; ?>)"
                                                    class="text-red-600 hover:text-red-800 text-xs sm:text-sm font-medium flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                <span class="hidden sm:inline">Удалить</span>
                                                <span class="sm:hidden">Удал.</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="flex flex-col items-center mt-6 sm:mt-8 gap-3">
                        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap justify-center">
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?php echo $currentPage - 1; ?>"
                                   class="px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded hover:bg-gray-100 transition text-gray-700 text-xs sm:text-sm">
                                    ← Назад
                                </a>
                            <?php else: ?>
                                <span class="px-3 sm:px-4 py-2 bg-gray-100 border border-gray-300 rounded text-gray-400 cursor-not-allowed text-xs sm:text-sm">
                                    ← Назад
                                </span>
                            <?php endif; ?>

                            <div class="flex gap-1 flex-wrap justify-center">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <?php if ($i == $currentPage): ?>
                                        <span class="px-3 sm:px-4 py-2 bg-primary text-white rounded font-medium text-xs sm:text-sm">
                                            <?php echo $i; ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="?page=<?php echo $i; ?>"
                                           class="px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded hover:bg-gray-100 transition text-gray-700 text-xs sm:text-sm">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?php echo $currentPage + 1; ?>"
                                   class="px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded hover:bg-gray-100 transition text-gray-700 text-xs sm:text-sm">
                                    Вперёд →
                                </a>
                            <?php else: ?>
                                <span class="px-3 sm:px-4 py-2 bg-gray-100 border border-gray-300 rounded text-gray-400 cursor-not-allowed text-xs sm:text-sm">
                                    Вперёд →
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="text-center text-xs sm:text-sm text-gray-600">
                            Страница <?php echo $currentPage; ?> из <?php echo $totalPages; ?>
                            (всего новостей: <?php echo $totalItems; ?>)
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

<?php
$is_admin = true;
include '../includes/footer.php';
?>

    <script>
        // Редактирование новости
        function editNews(id) {
            fetch('dashboard.php?get_news=1&id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    // Заполняем формы
                    document.getElementById('formAction').value = 'update';
                    document.getElementById('newsId').value = data.id;
                    document.getElementById('title').value = data.title;
                    document.getElementById('content').value = data.content;
                    document.getElementById('category').value = data.category;

                    document.getElementById('formTitle').innerHTML = `
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Редактировать новость`;
                    document.getElementById('submitBtn').innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Сохранить изменения`;
                    document.getElementById('cancelBtn').classList.remove('hidden');

                    if (data.image) {
                        document.getElementById('currentImageText').textContent = 'Текущее изображение: ' + data.image;
                        if (data.image === 'uploads/stock.jpg') {
                            document.getElementById('use_stock_image').checked = true;
                            document.getElementById('image').disabled = true;
                        } else {
                            document.getElementById('use_stock_image').checked = false;
                            document.getElementById('image').disabled = false;
                        }
                    } else {
                        document.getElementById('currentImageText').textContent = '';
                    }

                    document.getElementById('newsForm').scrollIntoView({ behavior: 'smooth' });
                })
                .catch(error => {
                    alert('Ошибка при загрузке новости');
                });
        }

        // Удаление новости
        function deleteNews(id) {
            if (confirm('Вы уверены, что хотите удалить эту новость?')) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);
                formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');

                fetch('dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Обновляем CSRF-токен для возможных последующих запросов
                        if (data.csrf_token) {
                            document.querySelectorAll('input[name="csrf_token"]').forEach(el => {
                                el.value = data.csrf_token;
                            });
                        }
                        window.location.reload();
                    } else {
                        alert('Ошибка при удалении новости');
                    }
                })
                .catch(error => {
                    alert('Ошибка при удалении новости');
                });
            }
        }

        // Сброс формы
        function resetForm() {
            document.getElementById('newsForm').reset();
            document.getElementById('formAction').value = 'create';
            document.getElementById('newsId').value = '';
            document.getElementById('formTitle').innerHTML = `
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Добавить новость`;
            document.getElementById('submitBtn').innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Добавить новость`;
            document.getElementById('cancelBtn').classList.add('hidden');
            document.getElementById('currentImageText').textContent = '';
            document.getElementById('use_stock_image').checked = false;
            document.getElementById('image').disabled = false;
        }

        // Связь чекбокса с полем файла
        document.getElementById('use_stock_image').addEventListener('change', function() {
            const fileInput = document.getElementById('image');
            if (this.checked) {
                fileInput.disabled = true;
                fileInput.value = '';
            } else {
                fileInput.disabled = false;
            }
        });
    </script>
