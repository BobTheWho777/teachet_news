<?php
require_once 'config/database.php';

// Получаем ID новости
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Получаем новость из базы
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$newsItem = $stmt->fetch();

if (!$newsItem) {
    header('Location: index.php');
    exit;
}

$categoryColors = [
    'Низкая' => 'bg-green-100 text-green-800',
    'Средняя' => 'bg-yellow-100 text-yellow-800',
    'Высокая' => 'bg-red-100 text-red-800'
];

// Параметры для header
$page_title = $newsItem['title'] . ' - Бизнес и Право';
$page_desc = 'Новостной портал для преподавателей';
$show_admin_btn = true;

// Подключаем header
include 'includes/header.php';
?>
    <main class="container mx-auto px-4 py-8">
        <!-- Хлебные крошки -->
        <nav class="mb-6">
            <ol class="flex items-center gap-2 text-sm text-gray-600">
                <li>
                    <a href="index.php" class="hover:text-primary transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Главная
                    </a>
                </li>
                <li>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </li>
                <li>
                    <a href="index.php" class="hover:text-primary transition">Новости</a>
                </li>
                <li>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </li>
                <li class="text-gray-800 font-medium truncate max-w-md">
                    <?php echo htmlspecialchars($newsItem['title']); ?>
                </li>
            </ol>
        </nav>

        <!-- Статья -->
        <article class="bg-white rounded-lg shadow-md overflow-hidden max-w-4xl mx-auto">
            <?php if ($newsItem['image']): ?>
                <img src="<?php echo htmlspecialchars($newsItem['image']); ?>"
                     alt="<?php echo htmlspecialchars($newsItem['title']); ?>"
                     class="w-full h-64 md:h-96 object-cover">
            <?php else: ?>
                <div class="w-full h-64 md:h-96 bg-gradient-to-br from-primary to-accent flex items-center justify-center">
                    <svg class="w-32 h-32 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                </div>
            <?php endif; ?>

            <div class="p-8">
                <!-- Мета-информация -->
                <div class="flex flex-wrap items-center justify-between mb-6 pb-6 border-b border-gray-200">
                    <div class="flex flex-wrap items-center gap-3 mb-3 md:mb-0">
                        <span class="px-4 py-2 text-sm font-semibold rounded <?php echo $categoryColors[$newsItem['category']] ?? 'bg-gray-100 text-gray-800'; ?>">
                            <?php echo htmlspecialchars($newsItem['category']); ?> важность
                        </span>
                        <span class="text-gray-500 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <?php echo date('d.m.Y в H:i', strtotime($newsItem['published_at'])); ?>
                        </span>
                    </div>
                </div>

                <!-- Заголовок -->
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-6">
                    <?php echo htmlspecialchars($newsItem['title']); ?>
                </h2>

                <!-- Контент -->
                <div class="prose max-w-none text-gray-700 leading-relaxed text-base">
                    <?php echo nl2br(htmlspecialchars($newsItem['content'])); ?>
                </div>

                <!-- Кнопка назад -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <a href="index.php" class="inline-flex items-center text-primary hover:text-primary-light transition font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Назад к новостям
                    </a>
                </div>
            </div>
        </article>
    </main>

<?php
// Подключаем footer
include 'includes/footer.php';
?>
