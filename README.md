# Установка
```
git clone https://github.com/cetver/backend-symfony-city
cd backend-symfony-city
composer install
```
Создать БД:
```
mysql -u <user> -p -e 'CREATE SCHEMA cetver_backend_symfony_city DEFAULT CHARACTER SET = utf8 DEFAULT COLLATE = utf8_unicode_ci;'
```

Изменить `DB_URL` в `.env`, `.env.prod`

Выбрать окружение:
```
composer dump-env dev|prod
```

Сделать `bin/console` исполняемым файлом
```
chmod +x bin/console
```

Установить права `var` директории
```
chmod 755 var
```

Запустить миграции
```
bin/console migrations:migrate --no-interaction
```

# Использование

Сгенерировать файл с данными

```
bin/console help file:generate                                                                                             7.4.4
Description:
  Сгенерировать файл с данными

Usage:
  file:generate [options]

Options:
      --filepath[=FILEPATH]  Путь к файлу
      --lines[=LINES]        Количество строк

Help:
  Команда file:generate генерирует заданный файл с данными, с определенным количестом строк.
  Пример:
  php bin/console file:generate --filepath="/tmp/data" --lines="100"

```

Сохранить данные из файла

```
bin/console help file:parse                                                                                                7.4.4
Description:
  Сохранить данные из файла

Usage:
  file:parse [options]

Options:
      --filepath[=FILEPATH]  Путь к файлу

Help:
  Команда file:parse парсит данные из файла и сохраняет их в файлы и БД.
  Пример:
  php bin/console file:parse --filepath="/tmp/data"

```

# Прочее
Логи в консольных командах пишутся от уровня `error` и выше, в 2 потока:
`stderr` и `var/log/console-command/<command-name>`, можно посмотреть, запустив `bin/console file:generate`

Unit тесты писать лень

Проверка на стандарты symfony и doctrine
```
vendor/bin/php-cs-fixer fix --config=.php_cs.dist --dry-run
```
Стандарты, которые не нравятся:

- [yoda_style](https://mlocati.github.io/php-cs-fixer-configurator/#version:2.16|fixer:yoda_style)
- [increment_style](https://mlocati.github.io/php-cs-fixer-configurator/#version:2.16|fixer:increment_style)
- [concat_space](https://mlocati.github.io/php-cs-fixer-configurator/#version:2.16|fixer:concat_space)
- [phpdoc_inline_tag](https://mlocati.github.io/php-cs-fixer-configurator/#version:2.16|fixer:phpdoc_inline_tag)
- [blank_line_after_opening_tag [x] <php declare(strict_types=1);](https://mlocati.github.io/php-cs-fixer-configurator/#version:2.16|fixer:blank_line_after_opening_tag)

`localhost` в `DB_URL` устанавливает соединение через unix-сокет

Тест:
```
# вкладка терминала №1
vendor/bin/doctrine dbal:run-sql 'select sleep(10);'
# вкладка терминала №2
ss | grep mysql
```



---

Описание проекта
========
Данный проект реализован в виде CLI приложения для обрабокти некой выгрузки данных. Предполагается, что на входе предоставляется файл с json данными. Этот файл необходимо прочитать, разобрать и сохранить их куда-либо (в файлы/базу) данные.

Для того, чтобы сгенерировать файл с тестовыми данными можно воспользоваться коммандой `./bin/console file:generator` соответственно, для запуска парсинга подготовлена комманда `./bin/console file:parser`

На данный момент приложение полностью рабочее и выполняет все изначально поставленные задачи. Но, хотелось бы довести код до идеала насколько это возможно, попутно решив несколько сопутствующих задач.

Минимально необходимые требования
=========
* Доработать код таким образом, чтобы можно было сохранять данные при парсинге сразу в 2 места (файлы и базу)
* Понять схему БД на основании кода и подготовить миграцию (компонент для миграций нужно будет выбрать самостоятельно)
* После загрузки данных в БД из файла необходимо расчитать сколько человек в каждом городе и вывести эту инфомрацию так же в консоли

Дополнительные задачи
========
* Привести код в читабельный вид в соответствии со стандартами Symfony
* Исправить возможные проблемы в коде

Будет плюсом
=======
* Добавить логирование возможных ошибок
* Написать тест(ы) на PHPUnit

Дополнительная информация
========
* Решая данное задание вы можете выбрать любой удобный для вас подход - выкинуть весь код и переписать его так, чтобы полностью сохранить поведение приложения и исправить возможные проблемы. Либо же, установить/заменить пакеты на те, которые посчитаете нужными или с которыми привыкли работать.
* Если какой-то участок кода не поддается рефакторингу, но вы видите в нем проблему - можете обозначить это комментарием в коде. 
