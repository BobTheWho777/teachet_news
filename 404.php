<?php
http_response_code(404);
$page_title = '404 - Страница не найдена';
$page_desc = 'Запрашиваемая страница не существует';
include 'includes/header.php';
?>

    <section class="bg-gradient-to-r from-primary to-primary-light text-white py-8 sm:py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-3 sm:mb-4 leading-tight">ОШИБКА 404</h2>
                <p class="text-base sm:text-lg text-gray-100">Страница не найдена</p>
            </div>
        </div>
    </section>

    <main class="container mx-auto px-4 py-8 sm:py-12">
        <div class="bg-white rounded-lg shadow-md p-6 sm:p-8 max-w-2xl mx-auto text-center">
            <!-- Иконка -->
            <div class="mb-6 sm:mb-8">
                <div class="w-24 h-24 sm:w-32 sm:h-32 mx-auto mb-4 sm:mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 sm:w-16 sm:h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-black text-primary mb-3 sm:mb-4">404</h1>
                <p class="text-gray-600 text-base sm:text-lg mb-2">К сожалению, запрашиваемая страница не существует.</p>
                <p class="text-gray-500 text-sm sm:text-base">Возможно, она была удалена, переименована или никогда не существовала.</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 sm:p-6 mb-6 sm:mb-8 text-left">
                <h3 class="text-sm sm:text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Возможные причины:
                </h3>
                <ul class="text-gray-600 text-xs sm:text-sm space-y-2">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span>Неверно набран адрес в строке браузера</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span>Страница была удалена администратором</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <span>Ссылка устарела или была изменена</span>
                    </li>
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                <a href="index.php" class="w-full sm:w-auto bg-primary text-white px-6 sm:px-8 py-3 sm:py-3.5 rounded-lg hover:bg-primary-light transition font-medium flex items-center justify-center gap-2 text-sm sm:text-base">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    На главную
                </a>
                <a href="javascript:history.back()" class="w-full sm:w-auto bg-gray-100 text-gray-700 px-6 sm:px-8 py-3 sm:py-3.5 rounded-lg hover:bg-gray-200 transition font-medium flex items-center justify-center gap-2 text-sm sm:text-base">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Вернуться назад
                </a>
            </div>
        </div>
    </main>

<?php
include 'includes/footer.php';
?>
