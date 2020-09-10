<?php
namespace App\Application\Controllers;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use PDO;

abstract class APIController {
    protected $Model; // Model for DB actions and logging
    protected $Args; // Args
    protected $Query; // Querystring
    private $SentData; // Data sent in the request
    private $Request; // Request object
    private $Response; // Response object
    private $CustomResponse = false;

    // standard json data variables to return
    private $Data = [];

    public function __construct (ContainerInterface $container, Logger $logger, PDO $db = null) {
        try {
            $modelPath = explode('\\', get_class($this));
            $modelName = str_replace('Controller', '', $modelPath[count($modelPath) - 1]);
            $model = '\App\Application\Models\\'.$modelName.'Model';
            $this->Model = new $model($container, $logger, $db);
        } catch (\Exception $e) {
            $this->Model = null;
        }
    }

    /**
     * This is called on every API request. The route name is used to figure out which function to call.
     * It initializes and validates the request before calling the function.
     * After calling the function it returns the response.
     */
    public function __invoke(Request $request, $response, $args) {
        $this->InitRequest($request, $response, $args);
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
    
        // return NotFound for non existent route
        if (empty($route)) {
            throw new NotFoundException($request, $response);
        }

        $function = $route->getName();
        if (method_exists($this, $function)) {
            $this->$function($request, $response, $args);
        }
        else {
            //$this->Model->Logger->error('Function does not exist: '.$function.'.', []);
        }

        return $this->Response();
    }
    
    protected function AddReturnData ($data) {
        $this->Data = $data;
    }

    protected function GetSentData () {
        return $this->SentData;
    }

    private function InitRequest (Request $request, Response $response, array $args) {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $contents = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request = $request->withParsedBody($contents);
            }
        }

        $this->SentData = $request->getParsedBody();
        $this->Request = $request;
        $this->Response = $response;        
        $this->Args = $args;
        $this->Query = $this->Request->getQueryParams();
    }

    protected function CustomResponse (Response $response) {
        $this->Response = $response;
        $this->CustomResponse = true;
    }

    protected function Response () {
        $responseData = [];

        if (count($this->Data) > 0) {
            $responseData['Data'] = $this->Data;
        }

        if (!$this->CustomResponse) {
            $this->Response->withHeader('Content-type', 'application/json')->getBody()->write(json_encode($responseData));
        }

        return $this->Response;
    }
}