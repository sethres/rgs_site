<?php
declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // default everything to production
    $displayErrorDetails = false;
    $environment = getenv('ENVIRONMENT');
    $dbSettings = [
            'host' => getenv('DB_HOST'),
            'dbname' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'pass' => getenv('DB_PASS')
        ];

    if ($environment === 'development') {
        $displayErrorDetails = true;
    }

    // Global Settings Object
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => $displayErrorDetails, // Should be set to false in production
            'db' => $dbSettings, // Database settings
            // Monolog settings
            'logger' => [
                'name' => 'slim-app',
                'path' => __DIR__ . '/../logs/app.log',
                'level' => \Monolog\Logger::DEBUG,
                'rollbar_config' => array(
                    'access_token' => getenv('ROLLBAR_ACCESS_TOKEN'),
                    'environment' => $environment
                )
            ]
        ]
    ]);
};
