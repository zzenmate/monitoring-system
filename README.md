monitoring-system
=================

## Description
Написав консольну команду, яка сканує сайт, у двох режимах(опціях).
- fast - сканує перші 100 документів (php bin/console app:tools:monitoring-resource --mode=fast).
- full - сканує увесь сайт (php bin/console app:tools:monitoring-resource --mode=full).
Періодичність запускання, 15 хвилин і 3 години, описано нижче.
Також, хочу наголосити про великий рівень абстрації коду, який рознесений у 
Entity, EntityListener, Manager, Repository, Service.
Використав, softdeleteable для видалення, тобто при видалні, запис не буде видалятись, 
а буде записуватись у поле deleted_at - час видалення.

## Explanation
Я вперше беру участь у DevChallenge, і нажаль, я ніколи не працюв з Docker\Vagrant, тому не вистачило часу з ним розібратись, 
для його налаштування, з можливістю роботи cron-задач. З тієї ж причини не встиг написати тести, тому що багато часу витратив на Docker.
Розумію, що рішення подається в некоректному форматі. 
Але, прошу дати мені шанс, і я обіцяю, що до фіналу, я розберуся з Docker, і здивую вас своїм чистим об’єктно-орієнтовним рішеням.
## Information

ApiDoc URL: http://your_url/api/doc

## Installation

1. Using composer
  ```
  $ composer install
  ```
  This command requires you to have Composer installed globally, as explained
  in the [Composer documentation](https://getcomposer.org/doc/00-intro.md).

## Configuring

1. Make sure that your local system is properly configured for Symfony2. To do this, execute the following:
  ```
  $ php app/check.php
  ```
  If you got any warnings or recommendations, fix them before moving on.

2. Setting up permissions for directories app/cache/ and app/logs
  ```
  $ HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
  $ sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/logs
  $ sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var/cache var/logs
  ```

3. Change DBAL settings, create DB, update it and load fixtures
  
  Change DBAL setting if your need in `app/config/config.yml`, `app/config/config_dev.yml` or `app/config/config_test.yml`. After that execute the following:
  ```
  $ php bin/console doctrine:database:create
  $ php bin/console doctrine:schema:update --force
  ```
  You can set test environment for command if you add --env=test to it.

### Usage

1. Створити 2 крон задачі, які будуть виконуватись кожні 15 хвилин, і кожні 3 години для повного сканування.
## СRON commands
```bash
$WEB_PATH=%path_to_your_directory_with_project%
*/15    *       *       *       *   www-data $WEB_PATH/console app:tools:monitoring-resource  --option=fast --env=prod
*     /3       *       *       *   www-data $WEB_PATH/console app:tools:monitoring-resource --option=full --env=prod
```

2. Налаштувати хости для проекта або запустити вбудований сервер задопомогою команды:
  ```
php bin/console server:run
  ```
3. Перейти по url: http://127.0.0.1:8000/api/doc і ознайомитись з документацією.
