# Описание
Этот telegram бот присылает проекты https://fl.ru/, отфильтрованные по ключевым словам.
Бот доступен по адресу @fl_ru_projects_bot https://t.me/fl_ru_projects_bot
# Установка
1. Скачать файлы zip арзивом или коммандой
git clone https://github.com/fakenpc/fl.ru-telegram-bot.git
2. Для установки необходимо использовать composer.
Если composer не установлен установить его коммандой. 
curl -sS https://getcomposer.org/installer | php
php composer.phar install
3. Переименовать config_example.php в config.php
4. Задать настроки в config.php
5. Устаовить db.sql и /vendor/longman/telegram-bot/structure.sql
6. Выставить права на выполнение, если необходимо
chmod u+x ./cron_run_bot.sh
chmod u+x ./cron_run_parser.sh
7. Запустить через консоль или поставить на cron на любой промежуток времени
./cron_run_bot.sh
./cron_run_parser.sh
