# Lerna - TG Bot server
серверная часть


## Требования
`php` >= 7.4.3 `mysql` >= 8.0


## Конфигурация
- создать файл `config.php` в корне проекта по примеру `config.php.EXAMPLE` где:
    - `db_host` - имя хоста базы, как правило, `localhost`
    - `db_name` - имя базы
    - `db_user` - имя пользователя базы данных
    - `db_pass` - пароль на пользователя базы
    - `token` - токен telegram бота
- настроить маршрутизацию через корневой `index.php` в конфиге `nginx` сервера. Если используется `Apache2` создать `.htaccess` в корень проекта.
- (необязательно) Для логирования и тестов во время разработке есть функционал логов в файл `logs.txt` в корне проекта. Функция `log` в классе `Basic`. Для правильной работы необходимо создать файл `logs.txt` в корне проекта. Установить права на редактирование нужной группе-пользователю.


## Структура файлов

### index.php
- корневой файл `index.php` является входящей точкой.
- подключение конфига, классов, настройка обработки POST запросов.
- объявление маршрутов.

### classes 
- дирректория `classes` содержит основные классы серверной части.
    - `controllers` - дирректория контроллеров с маршрутами.
    - `Basic.php` - класс с базовыми функциями.
    - `Db.php` - класс с работы с БД.
    - `RouterLite.php` - класс маршрутизации.

### data
- дирректория `data` содержит файлы со статичными массивами данных.
    - `professions.php` - массив сообщений при отправки аватара.
    - `sendler.php` - массив сообщений для напоминаний.
    - `vectors.php` - массив сообщений с карьерным треком.

### public
- дирректория `public` содержит статичные файлы.
    - `css` - стили.
    - `font` - шрифты.0
    - `images` - картинки.
    - `js` - скрипты.

### templates
- дирректория `templates` содержит шаблоны.
    - `images` - дирректория картинок для генерации аватаров.
    - `php` -  файлы шаблонов админки.
    
### uploads
- дирректория `uploads` содержит созданные ранее экземпляры аватаров. Кэширование.
