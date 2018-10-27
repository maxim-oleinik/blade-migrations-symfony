Blade/Migrations - Symfony
==========================
[![Latest Stable Version](https://poser.pugx.org/maxim-oleinik/blade-migrations-Symfony/v/stable)](https://packagist.org/packages/maxim-oleinik/blade-migrations-symfony)

Набор консольных комманд под Symfony Console
Используют текущее соединение с базой в вашем Symfony-проекте.  
См. https://github.com/maxim-oleinik/blade-migrations

Установка и настройка
---------

1. Добавить в **composer**
    ```
        composer require maxim-oleinik/blade-migrations-symfony
    ```

2. Создать запускаемый скрипт консоли (если его нет)  
  см. https://symfony.com/doc/current/components/console.html#creating-a-console-application
    ```
        cli.php
        -------
        #!/usr/bin/env php
        <?php
            require __DIR__.'/vendor/autoload.php';
            use Symfony\Component\Console\Application;
            
            $application = new Application();
            
            // ... register commands
            $application->run();
    ```

3. Схема сборки команд и их регистрации
    ```
        // Подключение к БД
        $conn      = new MyDbConnection; // implements \Blade\Database\DbConnectionInterface
        // $conn = new \Blade\Database\Connection\PostgresConnection($connectionString);
        // $conn = new \Blade\Database\Connection\MysqlConnection($host, $user, $pass, $dbName, $port);
        // $conn = new \Blade\Database\Connection\PdoConnection($dsn, $user, $pass);
        см. https://github.com/maxim-oleinik/blade-database
        
        $dbAdapter = new \Blade\Database\DbAdapter($conn);
        $repoDb    = new \Blade\Migrations\Repository\DbRepository($migrationTableName = 'migrations', $dbAdapter);
        $repoFile  = new \Blade\Migrations\Repository\FileRepository($migrationsDir = __DIR__ . '/migrations');
        $service   = new \Blade\Migrations\MigrationService($repoFile, $repoDb);
        
        $application->add(new \Blade\Migrations\Symfony\Console\InstallCommand($repoDb));
        $application->add(new \Blade\Migrations\Symfony\Console\MakeCommand(new \Blade\Migrations\Operation\MakeOperation($repoFile)));
        $application->add(new \Blade\Migrations\Symfony\Console\StatusCommand(new \Blade\Migrations\Operation\StatusOperation($service)));
        $application->add(new \Blade\Migrations\Symfony\Console\MigrateCommand(new \Blade\Migrations\Operation\MigrateOperation($service)));
        $application->add(new \Blade\Migrations\Symfony\Console\RollbackCommand(new \Blade\Migrations\Operation\RollbackOperation($service)));
    ```

4. Создать таблицу миграций в БД
    ```
        php cli.php migrate:install
    ```


Команды
---------

### Создать миграцию
```
    php cli.php migrate:make NAME
```

### Файл миграции
* `--TRANSACTION` - миграция должна быть запущена в транзации
* Инструкции разделяются тегами `--UP` и `--DOWN`
* SQL запросы разделяются `";"`
```
--TRANSACTION
--UP
ALTER TABLE authors ADD COLUMN code INT;
ALTER TABLE posts   ADD COLUMN slug TEXT;

--DOWN
ALTER TABLE authors DROP COLUMN code;
ALTER TABLE posts   DROP COLUMN slug;
```

**Если надо сменить раделитель**, когда в SQL необходимо использовать `";"`
```
--TRANSACTION
--SEPARATOR=@
--UP
    ... sql@
    ... sql@

--DOWN
    ... sql@
    ... sql@
```

см. синтаксис https://github.com/maxim-oleinik/blade-migrations


### Status
```
    php cli.php migrate:status

    +---+----+---------------------+------------------------+
    |   | ID | Date                | Name                   |
    +---+----+---------------------+------------------------+
    | Y | 6  | 28.08.2018 20:17:01 | 20180828_195348_M1.sql |
    | D | 7  | 28.08.2018 20:17:21 | 20180828_201639_M3.sql |
    | A |    |                     | 20180828_200950_M2.sql |
    +---+----+---------------------+------------------------+
```
где:
* **Y** - выполнена
* **D** - требует отката (в текущей ветке ее нет)
* **A** - в очереди


### Migrate
```
    # Накатить следующую по очереди А-миграцию
    php cli.php migrate:up

    # Не спрашивать подтверждение
    php cli.php migrate:up -f

    # Автомиграция - удаляет D-миграции, накатывает А-миграции
    php cli.php migrate:up --auto

    # Накатить миграцию из указанного файла
    php cli.php migrate:up FILE_NAME
```


### Rollback
```
    # Откатить последнюю Y-миграцию
    php cli.php migrate:rollback

    # Не спрашивать подтверждение
    php cli.php migrate:rollback -f

    # Откатить миграцию по ее номеру
    php cli.php migrate:rollback --id=N

    # Откатить миграцию, инструкции загрузить из файла, а не из БД (например, если в базу попала ошибка)
    php cli.php migrate:rollback --load-file
```
