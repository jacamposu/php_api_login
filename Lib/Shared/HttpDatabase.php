<?php

class HttpDatabase
{
	public array $params;
	public string $model;
	public string $resource_generic;
	public string $method;
	public string $type_result;
	public string $resource_model;
	public string $start_from;
	public array $_session;
	public object $_config;

	public function __construct()
	{
		// $this->_config = $config;
		$this->params = [];
		$this->type_result = 'assoc';
		$this->start_from = 0;
	}

	public function SetModel(string $model)
	{
		$this->model = $model;
		return $this;
	}

	public function SetResoruceModel(string $resource_model)
	{
		$this->method = $resource_model;
		return $this;
	}

	public function SetResourceGeneric(string $resource_generic)
	{
		$this->resource_generic = $resource_generic;
		return $this;
	}

	public function SetParams(array $newParams)
	{
		$this->params = $newParams;
		return $this;
	}

	public function Get($config): mixed
	{
		$this->_session = [
			'app' => $config->app->name, 
			'lang' => $config->app->lang, 
			'type' => $config->app->ProfileDefault
		];

		$http = new HpptClient($config);

	    $result = $http
				->SetUrl($config->url->wsDatabase)
				->SetHeaders($this)
				->Get()
				->decode();

		$http->close();

		return $result;
	}

	public function Post(array $body, $config): mixed
	{
		$this->_session = [
			'app' => $config->app->name, 
			'lang' => $config->app->lang, 
			'type' => $config->app->ProfileDefault
		];

		$http = new HpptClient($config);

		$result = $http
					->SetUrl($config->url->wsDatabase)
					->SetHeaders($this)
					->Post(['params'=> $body])
					->decode();
	
		$http->close();
		
		return $result;
	}

	public function Put(array $body, $config): mixed
	{
		$this->_session = [
			'app' => $config->app->name, 
			'lang' => $config->app->lang, 
			'type' => $config->app->ProfileDefault
		];
		
		$http = new HpptClient($config);

		$result = $http
					->SetUrl($config->url->wsDatabase)
					->SetHeaders($this)
					->Put(['params'=> $body])
					->decode();

		$http->close();
		
		return $result;
	}

	public function SP($config)
	{
		$this->_session = [
			'app' => $config->app->name, 
			'lang' => $config->app->lang, 
			'type' => $config->app->ProfileDefault
		];
		
		$http = new HpptClient($config);
		$result = $http
					->SetUrl($config->url->wsDatabaseSP)
					->SetHeaders($this)
					->Get()
					->execute();
					
		$http->close();
	}
}