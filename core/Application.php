<?php

abstract class WP_Application_Passwords
{
    protected $debug = false;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;

    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    public function setDebugMode($debug)
    {
        if($debug){
            $this->debug = true;
            ini_set('display_errors',1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors',0);
        }
    }

    public function initialize()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->db_manager = new DbManager();
        $this->router = new Router($this->registerRoutes);

    }

    protected function configure()
    {

    }

    abstract public function getRootDir();

    abstract protected function retisterRoutes();

    public function isDebugMode()
    {
        return $this->debug;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getDbManager()
    {
        return $this->db_manager;
    }

    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }

    public function getViewDir()
    {
        return $this->getRootDir() . '/views';
    }

    public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }

    public function run()
    {
        try{
            $params = $this->router->resolve($this->request->getPathInfo());
            if($params === false){
                throw new HttpNotFoundException('No route found for') . $this->request->getPathInfo();
            }

            $controller = $params['controller'];
            $action = $params['action'];

            $this->runAction($controller,$action,$params);
        } catch (HttpNotFoundException $e){
            $this->render404Page($e);
        }

        $this->response->send();
    }

    public function runAction($controller_name, $action, $params = array())
    {
        $controller_class = ucfirst($controller_name) . 'Controller';

        $controller = $this->findController($controller_class);
        if($controller === false){
            throw new HttpNotFoundException($controller_class . 'controller is not found.');
        }

        $content = $controller->run($action,$params);

        $this->response->setContent($content);
    }

    public function findController($controller_class)
    {
        if(!class_exists($controller_class)){
            $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';
        }

        if(!is_readable($controller_file)){
            return false;
        } else {
            require_once $controller_file;

            if(!class_exists($controller_class)){
                return false;
            }
        }

        return new $controller_class($this);
    }

    protected function render404Page($e){
        $this->response->setStatusCode(404,'Not Found');
        $message = $this->isDebugMode() ? $e->getMassage() : 'Page not found';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(<<<EOF
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>404page</title>
            </head>
            <body>
                {$message}
            </body>
            </html>
            EOF
        );
    }
}