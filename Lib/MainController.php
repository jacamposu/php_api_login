<?php 
/**
 * Controlador encargado de cargar la metadata del controlador
 * lang
 * config
 * 
 */

class MainController
{
	protected $response;
	protected $metadata;

	public function __construct($config, $headers)
	{
		$this->LoadMetadata($config, $headers);
		$this->response = new HttpResponse();
	}

	public function LoadMetadata($config, $headers)
	{
		$fileMetadata = explode('Controller', get_class($this))[0].'Metadata';
		$routClass = 'Metadata/'.$fileMetadata.'.php';
		if(file_exists($routClass))
		{
			require_once $routClass;
			$this->metadata = new $fileMetadata($headers);
		}
	}

}