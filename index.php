<?php
require_once 'config/database.php';

// Настройки пагинации
$itemsPerPage = 6;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Фильтрация по важности
$validCategories = ['Низкая', 'Средняя', 'Высокая'];
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

// Строим запрос с учётом фильтра
$whereClause = '';
$params = [];

if (!empty($selectedCategory) && in_array($selectedCategory, $validCategories)) {
    $whereClause = "WHERE category = :category";
    $params[':category'] = $selectedCategory;
}

// Подсчёт общего количества записей
$countQuery = "SELECT COUNT(*) FROM news $whereClause";
$countStmt = $pdo->prepare($countQuery);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Получение новостей с пагинацией и фильтром
$query = "SELECT * FROM news $whereClause ORDER BY published_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$news = $stmt->fetchAll();

$categoryColors = [
    'Низкая' => 'bg-green-100 text-green-800',
    'Средняя' => 'bg-yellow-100 text-yellow-800',
    'Высокая' => 'bg-red-100 text-red-800'
];

// Формирование URL для фильтров
function buildUrl($params = []) {
    $current = $_GET;
    foreach ($params as $key => $value) {
        if ($value === null || $value === '') {
            unset($current[$key]);
        } else {
            $current[$key] = $value;
        }
    }
    // Сбрасываем страницу при смене фильтра
    if (isset($params['category'])) {
        unset($current['page']);
    }
    $queryString = http_build_query($current);
    return htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . ($queryString ? '?' . $queryString : '');
}

// Подключаем header
$page_title = 'БИЗНЕС И ПРАВО';
$page_desc = 'Новостной портал для преподавателей';
include 'includes/header.php';
?>

    <!-- Hero секция -->
    <section class="bg-gradient-to-r from-primary to-primary-light text-white py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">ДОБРО ПОЖАЛОВАТЬ НА НОВОСТНОЙ ПОРТАЛ</h2>
                <p class="text-lg text-gray-100 mb-6">Актуальные новости и события для преподавателей техникума «Бизнес и Право»</p>
                <a href="#news" class="inline-block bg-white text-primary px-6 py-3 rounded font-medium hover:bg-gray-100 transition">
                    Смотреть новости
                </a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main id="news" class="container mx-auto px-4 py-12">
        <!-- Секция с заголовком и фильтрами -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0 flex items-center gap-3">
                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                    Последние новости
                </h2>

                <!-- Фильтр по важности -->
                <div class="flex items-center gap-2">
                    <span class="text-gray-600 font-medium">Важность:</span>
                    <a href="<?php echo buildUrl(['category' => '']); ?>"
                       class="px-4 py-2 rounded text-sm font-medium transition <?php echo empty($selectedCategory) ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                        Все
                    </a>
                    <?php foreach ($validCategories as $cat): ?>
                        <a href="<?php echo buildUrl(['category' => $cat]); ?>"
                           class="px-4 py-2 rounded text-sm font-medium transition <?php echo $selectedCategory === $cat ? $categoryColors[$cat] . ' ring-2 ring-primary' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                            <?php echo $cat; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (empty($news)): ?>
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p class="text-gray-500 text-lg">
                        <?php echo !empty($selectedCategory) ? 'Новости с такой важностью не найдены' : 'Новости пока не добавлены'; ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($news as $item): ?>
                        <a href="news.php?id=<?php echo $item['id']; ?>" class="block bg-white border border-gray-200 rounded overflow-hidden hover:shadow-lg transition group">
                            <?php if ($item['image']): ?>
                                <div class="overflow-hidden">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>"
                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                         class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
                                </div>
                            <?php else: ?>
                                <div class="w-full h-48 bg-gradient-to-br from-primary to-accent flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>

                            <div class="p-5">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="px-3 py-1 text-xs font-semibold rounded <?php echo $categoryColors[$item['category']] ?? 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo htmlspecialchars($item['category']); ?> важность
                                    </span>
                                    <span class="text-sm text-gray-500 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <?php echo date('d.m.Y', strtotime($item['published_at'])); ?>
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-primary transition">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </h3>

                                <p class="text-gray-600 text-sm line-clamp-3">
                                    <?php echo htmlspecialchars(mb_substr($item['content'], 0, 150, 'UTF-8')) . '...'; ?>
                                </p>

                                <div class="mt-4 flex items-center text-primary font-medium text-sm">
                                    Читать далее
                                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Пагинация -->
                <?php if ($totalPages > 1): ?>
                    <div class="flex justify-center items-center mt-12 gap-2">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?php echo buildUrl(['page' => $currentPage - 1]); ?>"
                               class="px-4 py-2 bg-white border border-gray-300 rounded hover:bg-gray-100 transition text-gray-700">
                                ← Назад
                            </a>
                        <?php else: ?>
                            <span class="px-4 py-2 bg-gray-100 border border-gray-300 rounded text-gray-400 cursor-not-allowed">
                                ← Назад
                            </span>
                        <?php endif; ?>

                        <div class="flex gap-1">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i == $currentPage): ?>
                                    <span class="px-4 py-2 bg-primary text-white rounded font-medium">
                                        <?php echo $i; ?>
                                    </span>
                                <?php else: ?>
                                    <a href="<?php echo buildUrl(['page' => $i]); ?>"
                                       class="px-4 py-2 bg-white border border-gray-300 rounded hover:bg-gray-100 transition text-gray-700">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?php echo buildUrl(['page' => $currentPage + 1]); ?>"
                               class="px-4 py-2 bg-white border border-gray-300 rounded hover:bg-gray-100 transition text-gray-700">
                                Вперёд →
                            </a>
                        <?php else: ?>
                            <span class="px-4 py-2 bg-gray-100 border border-gray-300 rounded text-gray-400 cursor-not-allowed">
                                Вперёд →
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="text-center mt-4 text-sm text-gray-600">
                        Страница <?php echo $currentPage; ?> из <?php echo $totalPages; ?>
                        (всего новостей: <?php echo $totalItems; ?>)
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

<?php
// Подключаем footer
include 'includes/footer.php';
?>
