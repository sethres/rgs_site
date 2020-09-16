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

    protected function GetResults ($sql, $params, $type, $fetch = PDO::FETCH_ASSOC, $fetchAll = true) {
        $statement = $this->prepSQL($sql);

        if (is_array($params) && count($params) > 0) {
            foreach ($params as $key=>$param) {
                $statement->bindValue($key, $param);
            }
        }

        try {
            $statement->execute();
        } catch (PDOException $e) {
			$this->Logger->error("$type (PDO Exception)", $e);
            return ["error" => ($e->getMessage())];
        }

        if (!$statement) {
			$this->Logger->error("$type (Statement)", $statement->errorInfo());
            return ["error" => $statement->errorInfo()];
        }

        if ($fetchAll) {
            $return = $statement->fetchAll($fetch);
        } else {
            $return = $statement->fetch($fetch);
        }
        
        $statement->closeCursor();

        return $return;
    }
}