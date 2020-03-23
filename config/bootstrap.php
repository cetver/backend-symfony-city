<?php

use App\Constant\EnvConstant;
use Symfony\Bundle\MonologBundle\DependencyInjection\MonologExtension;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\DependencyInjection\AddEventAliasesPass;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;
use Symfony\Component\Validator\DependencyInjection\AddAutoMappingConfigurationPass;
use Symfony\Component\Validator\DependencyInjection\AddConstraintValidatorsPass;
use Symfony\Component\Validator\DependencyInjection\AddValidatorInitializersPass;
use Symfony\Component\Yaml\Yaml;

$projectDir = dirname(__DIR__);
$vendorDir = $projectDir.'/vendor';
$srcDir = $projectDir.'/src';
$packagesDir = __DIR__.'/packages';

require $vendorDir.'/autoload.php';

/**
 * Env Vars.
 */
$env = @include $projectDir.'/.env.local.php';
if (!$env) {
    $envConstant = new EnvConstant();
    $message = sprintf(
        'Please run "composer dump-env <%s>" to dump ".env(?.<environment>)" in ".env.local.php"',
        $envConstant->toList('|')
    );
    throw new RuntimeException($message);
}

foreach ($env as $k => $v) {
    $_ENV[$k] = $_ENV[$k] ?? (isset($_SERVER[$k]) && 0 !== strpos($k, 'HTTP_') ? $_SERVER[$k] : $v);
}

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: EnvConstant::DEV;
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? EnvConstant::PROD !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] =
    (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';

/**
 * Container.
 */
$cacheDir = $projectDir.'/'.$_ENV['CACHE_DIR'];
$logDir = $projectDir.'/'.$_ENV['LOG_DIR'];
$dbDir = $projectDir.'/'.$_ENV['DB_DIR'];
$containerClass = $_ENV['APP_ENV'].'Container';
$doctrineEntityDir = $srcDir.'/Entity';
$doctrineMigrationDir = $srcDir.'/Migration';
$containerCacheFile = sprintf('%s/%s.php', $cacheDir, $containerClass);
$containerConfigCache = new ConfigCache($containerCacheFile, $_ENV['APP_DEBUG']);
if (!$containerConfigCache->isFresh()) {
    $doctrineCacheDir = $cacheDir.'/doctrine';
    $doctrineProxyCacheDir = $doctrineCacheDir.'/proxy';
    $doctrineQueryCacheDir = $doctrineCacheDir.'/query';

    $containerBuilder = new ContainerBuilder();
    $containerBuilder->setParameter('dir.project', $projectDir);
    $containerBuilder->setParameter('dir.cache', $cacheDir);
    $containerBuilder->setParameter('dir.log', $logDir);
    $containerBuilder->setParameter('dir.db', $dbDir);
    $containerBuilder->setParameter('dir.log.console_command', $logDir.'/console-command');
    $containerBuilder->setParameter('dir.doctrine.cache_query', $doctrineCacheDir.'/query');
    $containerBuilder->setParameter('dir.doctrine.cache_metadata', $doctrineCacheDir.'/metadata');
    $containerBuilder->setParameter('dir.doctrine.cache_proxy', $doctrineCacheDir.'/proxy');
    $containerBuilder->setParameter('dir.doctrine.entity_paths', [$doctrineEntityDir]);
    $containerBuilder->setParameter('dir.doctrine_migration', $doctrineMigrationDir);

    $loader = new YamlFileLoader($containerBuilder, new FileLocator($packagesDir));
    $loader->load('services.yaml');

    $containerBuilder->addCompilerPass(
        new RegisterListenersPass(),
        PassConfig::TYPE_BEFORE_REMOVING
    );
    $containerBuilder->addCompilerPass(
        new AddEventAliasesPass(
            [
                ConsoleCommandEvent::class => ConsoleEvents::COMMAND,
                ConsoleErrorEvent::class => ConsoleEvents::ERROR,
                ConsoleTerminateEvent::class => ConsoleEvents::TERMINATE,
            ]
        )
    );
    $containerBuilder->addCompilerPass(new AddConsoleCommandPass(), PassConfig::TYPE_BEFORE_REMOVING);
    $containerBuilder->addCompilerPass(new AddConstraintValidatorsPass());
    $containerBuilder->addCompilerPass(new AddValidatorInitializersPass());
    $containerBuilder->addCompilerPass(new AddAutoMappingConfigurationPass());

    $monologBundle = new MonologBundle();
    $monologBundle->build($containerBuilder);
    $monologBundleLoader = new XmlFileLoader(
        $containerBuilder,
        new FileLocator($vendorDir.'/symfony/monolog-bundle/Resources/config')
    );
    $monologBundleLoader->load('monolog.xml');

    $monologExtension = new MonologExtension();
    $monologExtensionConfig = (array) Yaml::parseFile($packagesDir.'/monolog.yaml');
    $monologExtension->load($monologExtensionConfig, $containerBuilder);
    $containerBuilder->registerExtension($monologExtension);

    $containerBuilder->compile();

    $dumper = new PhpDumper($containerBuilder);
    $containerConfigCache->write(
        $dumper->dump(['class' => $containerClass]),
        $containerBuilder->getResources()
    );
}

require_once $containerCacheFile;
$container = new $containerClass();
