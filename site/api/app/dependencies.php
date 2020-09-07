<?php
declare(strict_types=1);

use Rollbar\Rollbar;
use Rollbar\Payload\Level;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            Rollbar::init($loggerSettings['rollbar_config']);
            $logger = new Logger($loggerSettings['name']);

            $logger->pushHandler(new Monolog\Handler\PsrHandler(Rollbar::logger()));

            return $logger;
        },
        PDO::class => function (ContainerInterface $c) {
            $settings = $c->get('settings')['db'];

            $charset = 'utf8mb4';
            $dsn = "mysql:host=" . $settings["host"] . ";dbname=" . $settings["dbname"] . ";charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                $pdo = new PDO($dsn, $settings["user"], $settings["pass"], $options);
            } catch (PDOException $e) {
                $logger = $c->get('Psr\Log\LoggerInterface');
			    $logger->error('Database connection error', ['code' => (int)$e->getCode(), 'message' => $e->getMessage()]);
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
            return $pdo;
        },
        \ProductController::class => function (ContainerInterface $c, LoggerInterface $l, PDO $db) {
            return new \App\Application\Controllers\ProductController($c, $l, $db);
        }
    ]);
};
