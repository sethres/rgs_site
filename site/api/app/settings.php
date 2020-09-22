<?php
declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // default everything to production
    $displayErrorDetails = false;
    $environment = 'production';
    $dbSettings = [
            'host' => 'production_db_host',
            'dbname' => 'production_db_name',
            'user' => 'production_db_user',
            'pass' => 'production_db_password'
        ];

        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            $dbSettings = [
                'host' => 'db',
                'dbname' => 'rgsfurniture_dev',
                'user' => 'root',
                'pass' => 'zuc*TPOI4&n6VfLAbSI54p*0'
            ];
            $environment = 'development';
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
                    'access_token' => '240aa835a0d14189b0a75f029cb85185',
                    'environment' => $environment
                )
            ]
        ]
    ]);
};
