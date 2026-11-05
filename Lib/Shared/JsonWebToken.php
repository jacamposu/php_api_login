<?php


class JsonWebToken extends MainController 
{

	private object $_config;
    public array $_session;
    private array $params;
	
	public function __construct($config)
	{
		parent::__construct($config, []);
		$this->_config = $config;
        $this->params = [];
	}

    public function GetToken($data)
    {
        $ch = curl_init();
        
        $headers = [
            // "Authorization: Bearer TU_TOKEN_AQUI",
            "Content-Type: application/json"
        ];

        curl_setopt($ch, CURLOPT_URL, $this->_config->jwt->getTokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if(curl_errno($ch)){
            echo "Error: " . curl_error($ch);
        }

        curl_close($ch);

        return json_decode($response, true)['response'];
    }

    public function DecodeTokenBearer()
    {
       
    }

    public function DecodeToken()
    {

    }

    public function ValidateToken($token)
    {
        $ch = curl_init();
        
        $headers = [
            // "Authorization: Bearer TU_TOKEN_AQUI",
            "Content-Type: application/json",
            "token: {$token}"
        ];

        curl_setopt($ch, CURLOPT_URL, $this->_config->jwt->validateTokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if(curl_errno($ch))
            return json_decode(curl_error($ch), true);

        curl_close($ch);

        return json_decode($response, true);
    }
}