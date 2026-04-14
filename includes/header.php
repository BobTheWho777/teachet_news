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
                    },
                    screens: {
                        'xs': '480px',
                    }
                }
            }
        }
    </script>
</head>
<body class="<?php echo $body_class; ?>">

<?php if ($is_admin): ?>
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-3 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                        <a href="<?php echo $p; ?>index.php">
                            <img src="<?php echo $p; ?>uploads/logo.png" alt="" class="w-full h-full object-contain p-1">
                        </a>
                    </div>
                    <div>
                        <h1 class="text-base sm:text-xl font-bold text-primary">Админ-панель</h1>
                        <p class="text-[10px] sm:text-xs text-gray-600 hidden sm:block">Управление новостями</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-700 hidden lg:block text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Админ'); ?>
                    </span>
                    <a href="<?php echo $p; ?>index.php" class="bg-gray-600 text-white px-2 sm:px-4 py-2 rounded hover:bg-gray-700 transition text-xs sm:text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        <span class="hidden sm:inline">На сайт</span>
                    </a>
                    <a href="logout.php" class="bg-red-600 text-white px-2 sm:px-4 py-2 rounded hover:bg-red-700 transition text-xs sm:text-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="hidden sm:inline">Выйти</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

<?php else: ?>
    <!-- Верхняя техническая панель -->
    <div class="bg-primary-dark text-white py-1.5 sm:py-2 text-xs sm:text-sm">
        <div class="container mx-auto px-3 sm:px-4">
            <div class="flex justify-between items-center">
                <span class="flex items-center gap-1 sm:gap-2">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="xs:inline">г. Белореченск</span>
                </span>
                <a href="<?php echo $p; ?>admin/login.php" class="hover:text-gray-300 transition flex items-center gap-1.5 text-xs">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="hidden xs:inline">Админ панель</span>
                    <span class="xs:hidden">Войти</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-3 sm:px-4 py-3 sm:py-4">
            <div class="flex justify-between items-center">
                <a href="<?php echo $p; ?>index.php" class="flex items-center gap-2 sm:gap-4">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 bg-primary rounded-lg flex items-center justify-center flex-shrink-0">
                        <img src="<?php echo $p; ?>uploads/logo.png" alt="" class="w-full h-full object-contain p-1">
                    </div>
                    <div>
                        <h1 class="text-base sm:text-xl md:text-2xl font-bold text-primary leading-tight"><?php echo htmlspecialchars($page_title); ?></h1>
                        <p class="text-[10px] sm:text-xs md:text-sm text-gray-600 hidden sm:block"><?php echo htmlspecialchars($page_desc); ?></p>
                    </div>
                </a>
                <?php if (!empty($show_admin_btn)): ?>
                <a href="<?php echo $p; ?>admin/login.php" class="bg-primary text-white px-3 sm:px-6 py-2 sm:py-3 rounded hover:bg-primary-light transition text-xs sm:text-sm font-medium flex items-center gap-1 sm:gap-2 flex-shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span class="hidden sm:inline">Админ-панель</span>
                    <span class="sm:hidden">Войти</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </header>
<?php endif; ?>
