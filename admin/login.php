<?php
session_start();

if (isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Админ-панель | Бизнес и Право</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e3a8a',
                        'primary-light': '#2563eb',
                        'primary-dark': '#1e3a5f',
                    },
                    fontFamily: {
                        sans: ['Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans min-h-screen flex flex-col">
    <!-- Верхняя панель -->
    <div class="bg-primary-dark text-white py-2 text-sm">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    г. Белореченск
                </span>
                <a href="../index.php" class="hover:text-gray-300 transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    На главную
                </a>
            </div>
        </div>
    </div>

    <!-- Основной контент -->
    <div class="flex-1 flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <!-- Логотип и заголовок -->
            <div class="text-center mb-8">
                <div class="w-28 h-28 bg-primary rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <img src="../uploads/logo.png" alt="" class="w-full h-full object-contain p-2">
                </div>
                <h1 class="text-2xl font-bold text-primary">БИЗНЕС И ПРАВО</h1>
                <p class="text-gray-600 mt-1">Новостной портал для преподавателей</p>
            </div>

            <!-- Форма входа -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Вход в админ-панель</h2>
                    <p class="text-sm text-gray-500 mt-1">Введите данные для авторизации</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Неверный логин или пароль
                    </div>
                <?php endif; ?>

                <form action="auth.php" method="POST">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="username">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Логин
                        </label>
                        <input class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                               id="username"
                               name="username"
                               type="text"
                               placeholder="Введите логин"
                               required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-medium mb-2" for="password">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Пароль
                        </label>
                        <input class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition"
                               id="password"
                               name="password"
                               type="password"
                               placeholder="Введите пароль"
                               required>
                    </div>

                    <button class="w-full bg-primary hover:bg-primary-light text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2"
                            type="submit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Войти
                    </button>
                </form>
            </div>

            <!-- Дополнительная информация -->
            <div class="text-center mt-6">
                <a href="../index.php" class="text-primary hover:text-primary-light text-sm transition flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Вернуться на главную
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-primary-dark text-white py-4">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400 text-sm">&copy; <?php echo date('Y'); ?> ЧУПОО ТЕХНИКУМ «БИЗНЕС И ПРАВО»</p>
        </div>
    </footer>
</body>
</html>
