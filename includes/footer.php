<?php
/**
 * Универсальный footer
 *
 * Параметры:
 *   $is_admin — режим админки (по умолчанию false)
 */

$is_admin = $is_admin ?? false;
$p        = $is_admin ? '../' : '';
?>
    <footer class="bg-primary-dark text-white <?php echo $is_admin ? 'py-6 mt-12' : 'py-8'; ?>">
        <div class="container mx-auto px-4">
<?php if ($is_admin): ?>
            <p class="text-gray-400 text-sm text-center">&copy; <?php echo date('Y'); ?> ЧУПОО ТЕХНИКУМ «БИЗНЕС И ПРАВО». Все права защищены.</p>
<?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-6">
                <div>
                    <h3 class="text-lg font-bold mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        О портале
                    </h3>
                    <p class="text-gray-300 text-sm">Новостной портал для преподавателей техникума «Бизнес и Право»</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Контакты
                    </h3>
                    <p class="text-gray-300 text-sm">г. Белореченск</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Режим работы
                    </h3>
                    <p class="text-gray-300 text-sm">Время работы:<br>
                                                        Понедельник - Пятница: 8.00 - 17.00<br>
                                                        Суббота - Воскресенье: выходной</p>
                </div>
            </div>
            <div class="border-t border-gray-700 pt-6 text-center">
                <p class="text-gray-400 text-sm">&copy; <?php echo date('Y'); ?> ЧУПОО ТЕХНИКУМ «БИЗНЕС И ПРАВО». Все права защищены.</p>
            </div>
<?php endif; ?>
        </div>
    </footer>
</body>
</html>
