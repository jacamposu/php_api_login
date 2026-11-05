<?php

class Index
{
	private $_controller;
    private $_method;
    private $_params;
    private $_response;
    private $_config;
    private string $_route_config;

	public function __construct()
	{
		$this->_route_config = 'Config/Config.json';
	}

	public function Main()
	{
		try
		{
			if(!file_exists($this->_route_config)) throw new Exception('CONFIG FILE NOT FOUND', 500);
			$this->_config = json_decode(file_get_contents($this->_route_config));
			$this->Init();
			$this->ParamsURL();
			$this->Autoload();
			$response = $this->LoadController();
            $code = 200;
		}
		catch(Exception $ex)
		{
			$response = [
				'title' => 'Error',
				'error' => $ex->getMessage()
			];
            $code = $ex->getCode();
			if($code >= 400) sleep($this->_config->login->waitLoginFailed);
		}
		finally
		{
			$this->Autoload();
            $this->_response = new HttpResponse();
			$code = isset($code) ? $code : 500;
			$response = isset($response) ? $response : ['title' => 'Error', 'error' => 'INTERNAL SERVER ERROR'];
            $this->_response->Response($code, $response);
		}
	}

	public function Init()
	{
		//error_reporting(E_ALL); // Error/Exception engine, always use E_ALL
		//ini_set('ignore_repeated_errors', TRUE); // always use TRUE
		//ini_set('display_errors', TRUE); // Error/Exception display, use FALSE only in production environment or real server. Use TRUE in development environment
		//ini_set('log_errors', TRUE); // Error/Exception file logging engine.
	}


	public function ParamsURL()
	{
		$url = isset($_GET['url']) ? $_GET['url'] : '';
		$arrUrl = explode('/', $url);

		$this->_controller = $arrUrl[0];
		$this->_method = $arrUrl[0];
		$this->_params = '';

		// Se obienen los métodos
		if(!empty($arrUrl[1])){
			if($arrUrl[1] != ''){
				$this->_method = $arrUrl[1];
			}
		}

		// Se obtienen los parámetros
		if(!empty($arrUrl[2])){
			$arrGet = [];
			for($i=2; $i < count($arrUrl); $i++){
				$arrayUrl = explode('=', $arrUrl[$i]);
				if(isset($arrayUrl[0]) && isset($arrayUrl[1])){
					$arrGet[$arrayUrl[0]] = $arrayUrl[1];
				}
			}
			$_GET = $arrGet;
		}
	}

	public function Autoload()
	{
		spl_autoload_register(function($class)
		{
			$directories = [
				'Lib/Shared/',
				'Lib/',
				'Models/',
				'Metadata/',
				'Interfaces/'
			];

			foreach($directories as $directory)
				if(file_exists($directory.$class.'.php')) 
					require_once $directory.$class.'.php';
		});
	}

	public function LoadController()
	{
		if(!file_exists('Controller/'.$this->_controller.'Controller.php')) throw new Exception("CONTROLLER NOT FOUND", 500);

		require_once 'Controller/'.$this->_controller.'Controller.php';
        $controller = $this->_controller.'Controller';
        $objectController = new $controller($this->_config);

		if(!method_exists($objectController, $this->_method)) throw new Exception("METHOD NOT FOUND", 500);

		return $objectController->{$this->_method}();
	}
}

$index = new Index();
$index->Main();