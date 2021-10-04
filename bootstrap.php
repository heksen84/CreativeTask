<?php

use App\Provider\AppProvider;
use App\Provider\ConsoleCommandProvider;
use App\Provider\DoctrineOrmProvider;
use App\Provider\RenderProvider;
use App\Provider\WebProvider;
use App\Support\Config;
use App\Support\ServiceProviderInterface;
use Symfony\Component\Dotenv\Dotenv;
use App\Container\Container;

require_once __DIR__ . '/vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$paths = array("./src/Entity");
$isDevMode = true;

// the connection configuration
$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => '',
    'dbname'   => 'slim_project',
);

//$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
$config = Setup::createAnnotationMetadataConfiguration([__DIR__."/src"]	, $isDevMode, null, null, false);
$entityManager = EntityManager::create($dbParams, $config);

(new Dotenv('APP_ENV'))->loadEnv(__DIR__ . '/.env');

$env = $_ENV['APP_ENV'];

if (!$env) {
    $env = 'dev';
}

$config = new Config(__DIR__ . '/config', $env, __DIR__);


$providers = [
    AppProvider::class,
    DoctrineOrmProvider::class,
    ConsoleCommandProvider::class,
    WebProvider::class,
    RenderProvider::class,
];

$container = new Container([
    Config::class => static function () use ($config) { return $config; },
]);

//echo var_dump($container);

foreach ($providers as $providerClassName) {


    if (!class_exists($providerClassName)) {
        throw new RuntimeException(sprintf('Provider %s not found', $providerClassName));
    }
    $provider = new $providerClassName;
    if (!($provider instanceof ServiceProviderInterface)) {
        throw new RuntimeException(sprintf('%s class is not a Service Provider', $providerClassName));
    }
    $provider->register($container);
}

return $container;
