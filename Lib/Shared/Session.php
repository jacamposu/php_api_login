<?php
/**
* ----------------------------------------------------------
* Clase AppSession
* @return metodos de inicio y cierre de sesion
* @version 1
* @package Session
* @author Jose Antonio Campos Urquijo
* @copyright  Copyright (c)
* ----------------------------------------------------------
*/

class Session
{
	public function __construct()
	{
	}

	public function Start($urlRedirect=''):void
	{
		try{
			session_start(); 
			if(empty($_SESSION))
			{
				throw new Exception('Session expired', 404);
			}
		}catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function Close():bool
	{
		@session_start();
		session_unset();
		session_destroy();

		if(empty($_SESSION)) return true;

		return false;
	}

	// public function VerifyToken($config, $url)
	// {
	// 	$errorValidation = null;

	// 	if(!isset($_SESSION['UseToken']))
	// 	{
	// 		$errorValidation .= "Token indefinido";
	// 	}

    //     // obtener token registrado
    //     $headers[] = 'Content-Type: application/json';
    //     $curl = new Curl($url->apiCore.'/Login/GetTokenRegistered/');
    //     $responseApi = $curl->init()
    //             ->setOption()
    //             ->setOptionsDefault()
    //             ->PostMethod(json_encode(['user' => $_SESSION['UseId']]))
    //             ->decode();
    //     $curl->close();

    //     if(isset($responseApi->statusCode) && $responseApi->statusCode == 200)
    //     {
    //     	if($_SESSION['UseToken'] != $responseApi->result->token)
    //     	{
    //         	$errorValidation .= "Usuario o Token invalido";
    //     	}
    //     }
    //     else
    //     {
    //     	$errorValidation .= "No fue posible validar el token de usuario";
    //     }

    //     if(!is_null($errorValidation))
    //     {
	// 		die(header("Location: {$url->project}/"));
    //     }
	// }
}