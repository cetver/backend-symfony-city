<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\DependencyInjection\ContainerBuilder;

// Load cached env vars if the .env.local.php file exists
// Run "composer dump-env prod" to create it (requires symfony/flex >=1.2)
if (is_array($env = @include __DIR__.'/.env.local.php') && ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? $env['APP_ENV']) === $env['APP_ENV']) {
    foreach ($env as $k => $v) {
        $_ENV[$k] = $_ENV[$k] ?? (isset($_SERVER[$k]) && 0 !== strpos($k, 'HTTP_') ? $_SERVER[$k] : $v);
    }
} elseif (!class_exists(Dotenv::class)) {
    throw new RuntimeException('Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');
} else {
    // load all the .env files
    (new Dotenv(false))->loadEnv(__DIR__.'/.env');
}

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'dev';

DEFINE('VAR_DIR', __DIR__.'/var/');

$container = new ContainerBuilder();
$container->autowire(PDO::class, PDO::class)
    ->addArgument(sprintf('mysql:host=%s;dbname=%s', $_SERVER['APP_MYSQL_HOST'], $_SERVER['APP_MYSQL_DATABASE']))
    ->addArgument($_SERVER['APP_MYSQL_USER'])
    ->addArgument($_SERVER['APP_MYSQL_PASSWORD'])
    ->setPublic(false);

array_map(
    fn($class) => $container->autowire($class, $class)->setPublic(true),
    [
        App\Console\FileGeneratorCommand::class,
        App\Console\FileParserCommand::class,
        App\Services\Generator::class,
        App\Services\Parser::class
    ]
);

array_map(
    fn($class) => $container->autowire($class, $class)
        ->setPublic(true)
        ->addTag(App\Interfaces\ProviderInterface::class),
    [
        App\Provider\DatabaseProvider::class,
        App\Provider\FileProvider::class
    ]
);
$container->setAlias(App\Interfaces\ProviderInterface::class, App\Provider\FileProvider::class);

$repos = [
    App\Repository\CityRepository::class => App\Entity\City::class,
    App\Repository\UserRepository::class => App\Entity\User::class
];
array_walk(
    $repos,
    fn($entity, $repo) => $container->autowire($repo, $repo)
        ->setPublic(true)
        ->addTag(App\Interfaces\RepositoryInterface::class)
        ->setArguments([
            '$provider' => new Symfony\Component\DependencyInjection\Reference(
                App\Interfaces\ProviderInterface::class
            ),
            '$entity' => $entity,
        ])
);

$cleaner = $container->autowire(App\Services\Cleaner::class, App\Services\Cleaner::class)->setPublic(true);
$repos = $container->findTaggedServiceIds(App\Interfaces\RepositoryInterface::class);
array_walk(
    $repos,
    fn($data, $class) => $cleaner->addMethodCall('addRepository', [
        new Symfony\Component\DependencyInjection\Reference($class)
    ]),
);

$container->compile();
