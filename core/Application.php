<?php

abstract class WP_Application_Passwords
{
    protected $debug = false;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;

    public function __construct()
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
}