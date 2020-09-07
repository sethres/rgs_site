<?php
namespace App\Application\Models;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use PDO;

abstract class APIModel {
    public $Logger;
    public $Settings;

    protected $DB;
    protected $Container;

    private $Alerts = [];

    /***
        $container = slim container
    ***/
    public function __construct (ContainerInterface $container, Logger $logger, PDO $db = null) {
        $this->DB = $db;
        $this->Logger = $logger;
        $this->Settings = $container->get('settings');
        $this->Container = $container;
    }

    protected function prepSQL ($strSQL) {
        $pdoStmt = $this->DB->prepare($strSQL,[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY=>true]);
        return $pdoStmt;
    }
}