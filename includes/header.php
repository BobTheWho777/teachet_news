<?php
/**
 * Универсальный header
 *
 * Параметры:
 *   $is_admin      — режим админки (по умолчанию false)
 *   $page_title    — заголовок страницы
 *   $page_desc     — описание страницы
 *   $body_class    — CSS-класс для body
 */

// Значения по умолчанию
$is_admin   = $is_admin ?? false;
$page_title = $page_title ?? 'БИЗНЕС И ПРАВО';
$page_desc  = $page_desc ?? 'Новостной портал для преподавателей';
$body_class = $body_class ?? ($is_admin ? 'bg-gray-100 font-sans' : 'bg-gray-50 font-sans');

// Префикс путей
$p = $is_admin ? '../' : '';

// Старт сессии для админки
if ($is_admin && session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="<?php echo $p; ?>uploads/icon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e3a8a',
                        'primary-light': '#2563eb',
                        'primary-dark': '#1e3a5f',
                        accent: '#0ea5e9',
                    },
                    fontFamily: {
                        sans: ['Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="<?php echo $body_class; ?>">

<?php if ($is_admin): ?>
    <!-- ===== ADMIN HEADER ===== -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                        <a href="<?php echo $p; ?>index.php">
                            <img src="<?php echo $p; ?>uploads/logo.png" alt="" class="w-full h-full object-contain p-1">
                        </a>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-primary">Админ-панель</h1>
                        <p class="text-xs text-gray-600">Управление новостями</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-gray-700 hidden md:block">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Админ'); ?>
                    </span>
                    <a href="<?php echo $p; ?>index.php" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        На сайт
                    </a>
                    <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Выйти
                    </a>
                </div>
            </div>
        </div>
    </header>

<?php else: ?>
    <!-- ===== PUBLIC HEADER ===== -->
    <!-- Верхняя техническая панель -->
    <div class="bg-primary-dark text-white py-2 text-sm">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        г. Белореченск
                    </span>
                </div>
                <a href="<?php echo $p; ?>admin/login.php" class="hover:text-gray-300 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    Админ панель
                </a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <a href="<?php echo $p; ?>index.php" class="flex items-center gap-4">
                    <div class="w-20 h-20 bg-primary rounded-lg flex items-center justify-center">
                        <img src="<?php echo $p; ?>uploads/logo.png" alt="" class="w-full h-full object-contain p-1">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-primary"><?php echo htmlspecialchars($page_title); ?></h1>
                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($page_desc); ?></p>
                    </div>
                </a>
                <?php if (!empty($show_admin_btn)): ?>
                <a href="<?php echo $p; ?>admin/login.php" class="bg-primary text-white px-6 py-3 rounded hover:bg-primary-light transition font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Админ-панель
                </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
<?php endif; ?>
