<?php
namespace App\Application\Controllers;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use PDO;

class ProductController extends APIController
{
    function __construct(ContainerInterface $container, Logger $logger, PDO $db)
    {
        parent::__construct($container, $logger, $db);
    }

    function Categories (Request $request, Response $response, array $args) {
        $this->AddReturnData($this->Model->Categories());
    }

    function Collections (Request $request, Response $response, array $args) {
        $this->AddReturnData($this->Model->Collections($args['categoryURL']));
    }

    function SubCollections (Request $request, Response $response, array $args) {
        $this->AddReturnData($this->Model->SubCollections($args['categoryURL'], $args['collectionURL']));
    }

    function Products (Request $request, Response $response, array $args) {
        $this->AddReturnData($this->Model->Products($args['categoryURL'], $args['collectionURL'], $args['subcollectionURL']));
    }
}